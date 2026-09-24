<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        Schema::create('services_rebuilt_v2', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unique('name', 'services_v2_name_unique');
            $table->string('icon', 10)->default('🧺');
            $table->string('pricing_type');
            $table->decimal('price', 10, 2);
            $table->decimal('price_per_load', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::statement('INSERT INTO services_rebuilt_v2 (id, name, icon, pricing_type, price, price_per_load, is_active, created_at, updated_at) SELECT id, name, icon, pricing_type, price, price_per_load, is_active, created_at, updated_at FROM services');
        Schema::drop('services');
        Schema::rename('services_rebuilt_v2', 'services');
    }

    public function down(): void
    {
        //
    }
};
