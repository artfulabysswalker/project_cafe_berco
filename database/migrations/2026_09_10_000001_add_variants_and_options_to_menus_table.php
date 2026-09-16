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
            if (! Schema::hasColumn('menus', 'sub_variants')) {
                $table->json('sub_variants')->nullable()->after('deskripsi')
                    ->comment('Pilihan sub-varian produk (misal: Americano, Long Black)');
            }

            if (! Schema::hasColumn('menus', 'has_temperature_option')) {
                $table->boolean('has_temperature_option')->default(false)->after('sub_variants')
                    ->comment('Apakah produk memiliki opsi suhu (Hot / Cold)');
            }

            if (! Schema::hasColumn('menus', 'temperature_options')) {
                $table->json('temperature_options')->nullable()->after('has_temperature_option')
                    ->comment('Detail harga & HPP per opsi suhu [{"type":"Hot","price":20000,"hpp":6500}]');
            }

            if (! Schema::hasColumn('menus', 'flavor_options')) {
                $table->json('flavor_options')->nullable()->after('temperature_options')
                    ->comment('Pilihan rasa / sirup (misal: Hazelnut, Caramel, Vanilla)');
            }

            if (! Schema::hasColumn('menus', 'variant_selection_rules')) {
                $table->json('variant_selection_rules')->nullable()->after('flavor_options')
                    ->comment('Aturan pemilihan varian {"min_select":1,"max_select":2,"options":["Original","Matcha"]}');
            }

            if (! Schema::hasColumn('menus', 'portion_count')) {
                $table->unsignedInteger('portion_count')->default(1)->after('variant_selection_rules')
                    ->comment('Jumlah porsi atau pieces per porsi (misal: 2 pcs untuk donat)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach ([
                'sub_variants',
                'has_temperature_option',
                'temperature_options',
                'flavor_options',
                'variant_selection_rules',
                'portion_count',
            ] as $col) {
                if (Schema::hasColumn('menus', $col)) {
                    $columnsToDrop[] = $col;
                }
            }

            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
