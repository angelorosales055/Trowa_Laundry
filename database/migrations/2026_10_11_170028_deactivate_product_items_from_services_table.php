<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Deactivate retail and inventory supplies from services so service lists only display pure laundry services
        \Illuminate\Support\Facades\DB::table('services')
            ->whereIn('name', [
                'Fabric Softener',
                'Detergent',
                'Fabcon',
                'Cellophane',
                'Bleach',
            ])
            ->update(['is_active' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('services')
            ->whereIn('name', [
                'Fabric Softener',
                'Detergent',
                'Fabcon',
                'Cellophane',
                'Bleach',
            ])
            ->update(['is_active' => true]);
    }
};
