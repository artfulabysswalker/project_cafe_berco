<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_histories', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('id_order');
            $table->unsignedBigInteger('id_user');

            $table->string('nama_pelanggan');
            $table->integer('total_harga');

            $table->enum('payment_method', ['cash', 'credit']);
            $table->enum('service_type', ['dine_in', 'take_away']);

            $table->enum('status_order', ['completed', 'cancelled']);
            $table->enum('status_pembayaran', ['pending', 'paid']);

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_histories');
    }
};