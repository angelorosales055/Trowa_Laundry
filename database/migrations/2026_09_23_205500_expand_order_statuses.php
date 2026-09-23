<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'in-progress', 'ready', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE orders SET status = 'ready' WHERE status IN ('delivered', 'cancelled')");
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'in-progress', 'ready') NOT NULL DEFAULT 'pending'");
        }
    }
};
