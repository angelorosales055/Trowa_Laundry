<?php

// Vercel Serverless Function: Direct Database Migration & Seeding Endpoint
$key = $_GET['key'] ?? '';

// Ensure storage paths exist
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

putenv('APP_STORAGE=/tmp/storage');
$_ENV['APP_STORAGE'] = '/tmp/storage';
$_SERVER['APP_STORAGE'] = '/tmp/storage';

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

require __DIR__.'/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$expectedKey = (string) config('app.key');
if (empty($expectedKey) || !hash_equals($expectedKey, (string) $key)) {
    http_response_code(403);
    die("<pre style='font-family:monospace;padding:24px;background:#450a0a;color:#fca5a5;border-radius:8px;'><strong>403 Unauthorized</strong><br>The key in ?key= did not match your Vercel APP_KEY.</pre>");
}

try {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $migrateOutput = \Illuminate\Support\Facades\Artisan::output();

    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    $seedOutput = \Illuminate\Support\Facades\Artisan::output();

    echo "<pre style='font-family:monospace;padding:24px;background:#0f172a;color:#38bdf8;border-radius:8px;line-height:1.6;'>";
    echo "<h2 style='color:#4ade80;margin-top:0;'>Migration and Database Seeding Succeeded</h2>\n";
    echo "<strong style='color:#f8fafc;'>--- MIGRATIONS ---</strong>\n".htmlspecialchars($migrateOutput)."\n\n";
    echo "<strong style='color:#f8fafc;'>--- SEEDING ---</strong>\n".htmlspecialchars($seedOutput)."\n\n";
    echo "<span style='color:#4ade80;'>All tables created and initial accounts (admin123, staff123) are ready!</span>\n\n";
    echo "<a href='/login' style='color:#fbbf24;display:inline-block;margin-top:16px;'>Go to Sign In &rarr;</a>";
    echo "</pre>";
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<pre style='font-family:monospace;padding:24px;background:#450a0a;color:#fca5a5;border-radius:8px;line-height:1.6;'>";
    echo "<h2 style='color:#ef4444;margin-top:0;'>Migration Error</h2>\n";
    echo "<strong>Message:</strong> ".htmlspecialchars($e->getMessage())."\n\n";
    echo "<strong>File:</strong> ".htmlspecialchars($e->getFile().':'.$e->getLine())."\n\n";
    echo "<strong>Stack Trace:</strong>\n".htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}
