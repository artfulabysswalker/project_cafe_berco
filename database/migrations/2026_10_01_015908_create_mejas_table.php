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
        Schema::create('mejas', function (Blueprint $table) {
            $table->id('id_meja');
            $table->string('nama_meja', 50)->unique();
            $table->integer('kapasitas')->default(4);
            $table->enum('status', ['tersedia', 'terpakai', 'reservasi', 'maintenance'])->default('tersedia');
            $table->string('area', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mejas');
    }
};
