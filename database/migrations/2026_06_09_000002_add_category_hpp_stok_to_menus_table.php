<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (! Schema::hasColumn('menus', 'id_kategori')) {
                $table->unsignedBigInteger('id_kategori')->nullable()->after('nama_menu');
            }
            if (! Schema::hasColumn('menus', 'hpp')) {
                $table->decimal('hpp', 12, 2)->default(0)->after('harga');
            }
            if (! Schema::hasColumn('menus', 'stok')) {
                $table->integer('stok')->default(50)->after('hpp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['id_kategori', 'hpp', 'stok']);
        });
    }
};
