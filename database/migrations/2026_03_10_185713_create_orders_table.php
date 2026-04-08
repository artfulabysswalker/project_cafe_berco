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
       Schema::create('orders', function (Illuminate\Database\Schema\Blueprint $table) {
    $table->id('id_order');
    $table->dateTime('tanggal');
    $table->string('nama_pelanggan');
    $table->integer('total_harga');
    $table->enum('status_pembayaran', ['Sudah', 'Belum']);
    $table->foreignId('id_user')->constrained('users'); // Kasir
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
