<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('nama_pelanggan');
            }

            if (! Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone', 20)->nullable()->after('customer_name');
            }

            if (! Schema::hasColumn('orders', 'table_id')) {
                $table->unsignedBigInteger('table_id')->nullable()->after('customer_phone');
                $table->foreign('table_id')
                    ->references('id_meja')
                    ->on('mejas')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'table_id')) {
                $table->dropForeign(['table_id']);
                $table->dropColumn('table_id');
            }

            if (Schema::hasColumn('orders', 'customer_phone')) {
                $table->dropColumn('customer_phone');
            }

            if (Schema::hasColumn('orders', 'customer_name')) {
                $table->dropColumn('customer_name');
            }
        });
    }
};
