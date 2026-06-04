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
        Schema::create('qris_transactions', function (Blueprint $table) {
            $table->id('id_qris_transaction');
            $table->unsignedBigInteger('id_order')->nullable();
            $table->string('qris_code')->unique();
            $table->string('transaction_id')->nullable();
            $table->string('invoice_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'paid', 'failed', 'expired', 'cancelled'])->default('pending');
            $table->enum('payment_channel', ['qris', 'card', 'e_wallet', 'bank_transfer'])->default('qris');
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('metadata')->nullable(); // JSON for additional data
            $table->foreign('id_order')->references('id_order')->on('orders')->nullOnDelete();
            $table->timestamps();
            $table->index('qris_code');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qris_transactions');
    }
};
