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
        Schema::table('customers', function (Blueprint $table): void {
            $table->boolean('is_active')->default(true);
            $table->foreignId('merged_into_id')->nullable()->constrained('customers')->nullOnDelete();
        });

        Schema::table('payments', function (Blueprint $table): void {
            $table->foreignId('related_payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->foreignId('refunded_by')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::create('order_edit_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('changed_by')->constrained('users')->restrictOnDelete();
            $table->json('before');
            $table->json('after');
            $table->text('reason');
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('unit', 30);
            $table->decimal('quantity_on_hand', 12, 3)->default(0);
            $table->decimal('low_stock_threshold', 12, 3)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('inventory_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->restrictOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->string('movement_type', 30);
            $table->decimal('quantity_change', 12, 3);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['inventory_item_id', 'created_at']);
        });

        Schema::create('service_inventory_usages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_per_load', 12, 3);
            $table->timestamps();
            $table->unique(['service_id', 'inventory_item_id']);
        });

        Schema::create('expenses', function (Blueprint $table): void {
            $table->id();
            $table->string('category', 100);
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->string('reference_number')->nullable();
            $table->timestamps();
            $table->index(['expense_date', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('service_inventory_usages');
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('order_edit_histories');
        Schema::table('payments', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('related_payment_id');
            $table->dropConstrainedForeignId('refunded_by');
        });
        Schema::table('customers', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('merged_into_id');
            $table->dropColumn('is_active');
        });
    }
};
