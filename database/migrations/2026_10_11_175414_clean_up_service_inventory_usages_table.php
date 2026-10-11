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
        // Remove recipes for services that are deactivated products (e.g. Detergent, Fabcon, Fabric Softener)
        $inactiveServiceIds = \Illuminate\Support\Facades\DB::table('services')
            ->where('is_active', false)
            ->pluck('id');

        \Illuminate\Support\Facades\DB::table('service_inventory_usages')
            ->whereIn('service_id', $inactiveServiceIds)
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
