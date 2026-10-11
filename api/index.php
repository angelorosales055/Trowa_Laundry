<?php

// Ensure serverless temporary storage directories exist for Vercel read-only filesystem
putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

putenv('APP_STORAGE=/tmp/storage');
$_ENV['APP_STORAGE'] = '/tmp/storage';
$_SERVER['APP_STORAGE'] = '/tmp/storage';

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

$dirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/app/private',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

require __DIR__.'/../public/index.php';
