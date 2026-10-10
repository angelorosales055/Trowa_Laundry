<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete()->after('id');
            }
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'address')) {
                $table->string('address', 255)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('users', 'verification_code')) {
                $table->string('verification_code', 10)->nullable()->after('remember_token');
            }
        });

        Schema::table('orders', function (Blueprint $table): void {
            if (! Schema::hasColumn('orders', 'soap_preference')) {
                $table->string('soap_preference', 255)->nullable()->after('services');
            }
            if (! Schema::hasColumn('orders', 'customer_notes')) {
                $table->text('customer_notes')->nullable()->after('soap_preference');
            }
            if (! Schema::hasColumn('orders', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('customer_notes');
            }
            if (! Schema::hasColumn('orders', 'rating')) {
                $table->unsignedTinyInteger('rating')->nullable()->after('rejection_reason');
            }
            if (! Schema::hasColumn('orders', 'rating_comment')) {
                $table->text('rating_comment')->nullable()->after('rating');
            }
            if (! Schema::hasColumn('orders', 'rated_at')) {
                $table->timestamp('rated_at')->nullable()->after('rating_comment');
            }
            if (! Schema::hasColumn('orders', 'ready_notified_at')) {
                $table->timestamp('ready_notified_at')->nullable()->after('rated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $columns = ['soap_preference', 'customer_notes', 'rejection_reason', 'rating', 'rating_comment', 'rated_at', 'ready_notified_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
            $columns = ['phone', 'address', 'verification_code'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
