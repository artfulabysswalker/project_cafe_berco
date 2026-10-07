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
        Schema::table('cart_items', function (Blueprint $table) {
            if (! Schema::hasColumn('cart_items', 'temperature')) {
                $table->string('temperature', 20)->nullable()->after('quantity')
                    ->comment('Pilihan suhu minuman: Hot / Cold');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'temperature')) {
                $table->string('temperature', 20)->nullable()->after('quantity')
                    ->comment('Pilihan suhu minuman: Hot / Cold');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'temperature')) {
                $table->dropColumn('temperature');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'temperature')) {
                $table->dropColumn('temperature');
            }
        });
    }
};
