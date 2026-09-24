<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('services')
            ->whereNotNull('price_per_load')
            ->whereIn('pricing_type', ['per_kg', 'flat_rate'])
            ->update(['pricing_type' => 'per_load']);
    }

    public function down(): void
    {
        //
    }
};
