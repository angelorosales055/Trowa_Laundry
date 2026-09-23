<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('customer_name');
            $table->decimal('weight_kg', 8, 2)->nullable();
            $table->text('services');
            $table->enum('status', ['pending', 'in-progress', 'ready', 'delivered', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->index(['created_by', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
