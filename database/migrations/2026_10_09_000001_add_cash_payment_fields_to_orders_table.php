<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menyimpan nominal uang tunai yang diterima kasir dan kembaliannya agar
     * struk thermal dapat menampilkan rincian pembayaran tunai dan laporan
     * kas dapat direkonsiliasi.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'uang_diterima')) {
                $table->unsignedBigInteger('uang_diterima')->nullable()->after('paid_at');
            }

            if (! Schema::hasColumn('orders', 'kembalian')) {
                $table->unsignedBigInteger('kembalian')->nullable()->after('uang_diterima');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'kembalian')) {
                $table->dropColumn('kembalian');
            }

            if (Schema::hasColumn('orders', 'uang_diterima')) {
                $table->dropColumn('uang_diterima');
            }
        });
    }
};
