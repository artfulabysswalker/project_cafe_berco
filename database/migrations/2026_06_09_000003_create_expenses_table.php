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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->dateTime('tanggal');
            $table->string('deskripsi');
            $table->string('kategori')->default('Operasional');
            $table->decimal('nominal', 12, 2);
            $table->string('operator')->default('Gina (Kasir)');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
