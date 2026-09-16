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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['id_shift']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('id_shift')
                ->references('id_shift')
                ->on('cashier_shifts')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['id_shift']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('id_shift')
                ->references('id_shift')
                ->on('shifts')
                ->nullOnDelete();
        });
    }
};
