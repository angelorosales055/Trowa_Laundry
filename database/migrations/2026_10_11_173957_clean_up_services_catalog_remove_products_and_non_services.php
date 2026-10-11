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
        // Deactivate non-services and retail product items so that the service catalog
        // strictly retains authentic machine laundry services (e.g. Wash, Dry, Wash & Fold, etc.)
        \Illuminate\Support\Facades\DB::table('services')
            ->whereIn('name', [
                'Shoe Cleaning',
                'Stain Removal',
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
                'Shoe Cleaning',
                'Stain Removal',
            ])
            ->update(['is_active' => true]);
    }
};
