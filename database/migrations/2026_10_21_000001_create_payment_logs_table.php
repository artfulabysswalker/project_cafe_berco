<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Log mentah setiap charge QRIS Midtrans: dipakai untuk menampilkan QR ke
     * pelanggan, mencocokkan webhook, dan sebagai jejak audit (raw_response).
     * midtrans_order_id adalah kunci idempotensi karena Midtrans boleh
     * mengirim notifikasi yang sama lebih dari sekali.
     */
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('midtrans_order_id')->unique();
            $table->string('transaction_id')->nullable()->unique();
            $table->text('qr_code_url')->nullable();
            $table->bigInteger('gross_amount');
            $table->string('transaction_status')->default('pending');
            $table->string('fraud_status')->nullable();
            $table->string('payment_type')->default('qris');
            $table->json('raw_response')->nullable();
            $table->string('signature_key')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')
                ->references('id_order')
                ->on('orders')
                ->cascadeOnDelete();

            $table->index('transaction_status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
