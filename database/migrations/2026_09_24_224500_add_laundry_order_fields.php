<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->string('address')->nullable()->after('name');
            $table->string('contact_number', 30)->nullable()->after('address');
        });

        Schema::table('services', function (Blueprint $table): void {
            $table->decimal('price_per_load', 10, 2)->nullable()->after('price');
            $table->boolean('is_active')->default(true)->after('price_per_load');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE services MODIFY pricing_type ENUM('per_kg', 'per_load', 'flat_rate') NOT NULL");
        } elseif (DB::getDriverName() === 'sqlite') {
            Schema::create('services_rebuilt', function (Blueprint $table): void {
                $table->id();
                $table->string('name')->unique();
                $table->string('icon', 10)->default('🧺');
                $table->string('pricing_type');
                $table->decimal('price', 10, 2);
                $table->decimal('price_per_load', 10, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
            DB::statement('INSERT INTO services_rebuilt (id, name, icon, pricing_type, price, price_per_load, is_active, created_at, updated_at) SELECT id, name, icon, pricing_type, price, price_per_load, is_active, created_at, updated_at FROM services');
            Schema::drop('services');
            Schema::rename('services_rebuilt', 'services');
        }

        DB::table('services')->whereNull('price_per_load')->update([
            'price_per_load' => DB::raw('price'),
        ]);

        Schema::table('orders', function (Blueprint $table): void {
            $table->string('order_number')->nullable()->unique()->after('id');
            $table->unsignedInteger('number_of_loads')->default(1)->after('weight_kg');
            $table->decimal('amount_paid', 10, 2)->default(0)->after('total_price');
            $table->decimal('change', 10, 2)->default(0)->after('amount_paid');
            $table->enum('payment_status', ['paid', 'partially_paid', 'unpaid'])->default('unpaid')->after('change');
            $table->timestamp('order_date')->nullable()->after('payment_status');
        });

        Schema::create('order_services', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->unsignedInteger('loads');
            $table->decimal('price_per_load', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            $table->unique(['order_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_services');

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropUnique(['order_number']);
            $table->dropColumn(['order_number', 'number_of_loads', 'amount_paid', 'change', 'payment_status', 'order_date']);
        });

        Schema::table('services', function (Blueprint $table): void {
            $table->dropColumn(['price_per_load', 'is_active']);
        });

        Schema::table('customers', function (Blueprint $table): void {
            $table->dropColumn(['address', 'contact_number']);
        });
    }
};
