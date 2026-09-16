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
        Schema::table('menus', function (Blueprint $table) {
            if (! Schema::hasColumn('menus', 'series')) {
                $table->string('series', 100)->nullable()->after('id_kategori')
                    ->comment('Sub-kategori atau Series (misal: Black, White, Tea, Matcha, Signature, dll)');
                $table->index(['id_kategori', 'series'], 'idx_menus_category_series');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (Schema::hasColumn('menus', 'series')) {
                $table->dropIndex('idx_menus_category_series');
                $table->dropColumn('series');
            }
        });
    }
};
