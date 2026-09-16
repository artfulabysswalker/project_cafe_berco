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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id('id_shift');
            $table->foreignId('id_user')->constrained('users', 'id_user');
            $table->string('nama_pegawai');
            $table->decimal('modal_awal', 15, 2);
            $table->decimal('total_penjualan_tunai', 15, 2)->default(0);
            $table->decimal('total_penjualan_nontunai', 15, 2)->default(0);
            $table->decimal('kas_akhir_aktual', 15, 2)->nullable();
            $table->decimal('selisih', 15, 2)->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
