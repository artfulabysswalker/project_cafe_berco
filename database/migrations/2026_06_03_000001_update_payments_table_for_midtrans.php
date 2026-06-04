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
        Schema::table('payments', function (Blueprint $table) {
            // Add new columns for payment gateway integration (Xendit, etc.)
            if (!Schema::hasColumn('payments', 'snap_token')) {
                $table->string('snap_token')->nullable();
            }
            if (!Schema::hasColumn('payments', 'transaction_id')) {
                $table->string('transaction_id')->nullable();
            }
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
            if (!Schema::hasColumn('payments', 'amount')) {
                $table->decimal('amount', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('payments', 'status')) {
                $table->string('status')->default('pending'); // pending, paid, failed, cancelled
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'transaction_id', 'payment_method', 'amount', 'status']);
        });
    }
};
