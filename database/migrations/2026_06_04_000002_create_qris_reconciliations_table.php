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
        Schema::create('qris_reconciliations', function (Blueprint $table) {
            $table->id('id_reconciliation');
            $table->unsignedBigInteger('id_qris_transaction');
            $table->string('reference_id')->nullable(); // From bank
            $table->string('reconciliation_status')->default('pending'); // pending, matched, mismatched, resolved
            $table->decimal('bank_amount', 15, 2)->nullable();
            $table->decimal('system_amount', 15, 2);
            $table->decimal('amount_difference', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('bank_transaction_date')->nullable();
            $table->timestamp('reconciled_at')->nullable();
            $table->unsignedBigInteger('reconciled_by')->nullable(); // Admin user
            $table->foreign('id_qris_transaction')->references('id_qris_transaction')->on('qris_transactions')->cascadeOnDelete();
            $table->foreign('reconciled_by')->references('id_user')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->index('reconciliation_status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qris_reconciliations');
    }
};
