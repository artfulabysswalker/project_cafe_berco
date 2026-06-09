<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->id('id_order');

            $table->dateTime('tanggal');

            $table->string('nama_pelanggan');

            $table->integer('total_harga');

            $table->enum('status_pembayaran', ['pending', 'paid'])->default('pending');
            $table->enum('service_type', ['dine_in', 'take_away'])->default('dine_in');
            $table->enum('payment_method', ['cash', 'credit'])->nullable();
            $table->text('notes')->nullable();

            $table->enum('status_order', [
                'pending',
                'completed',
                'cancelled'
            ])->default('pending');

            $table->foreignId('id_user')
                ->constrained('users', 'id_user');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
