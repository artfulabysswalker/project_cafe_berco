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
        // 1. Tabel Bahan Baku (Raw Materials)
        if (! Schema::hasTable('raw_materials')) {
            Schema::create('raw_materials', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('unit', 50)->default('gr'); // gr, ml, pcs, shot, pump, gram
                $table->decimal('purchase_price', 15, 2)->default(0); // Harga per unit
                $table->decimal('stock_quantity', 15, 2)->default(0); // Jumlah stok bahan
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 2. Tabel Pivot Resep Menu (Product Recipes)
        if (! Schema::hasTable('product_recipes')) {
            Schema::create('product_recipes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('menu_id');
                $table->unsignedBigInteger('raw_material_id');
                $table->decimal('quantity_used', 12, 3)->default(0); // Jumlah takaran per porsi
                $table->timestamps();

                $table->foreign('menu_id')
                    ->references('id_menu')
                    ->on('menus')
                    ->onDelete('cascade');

                $table->foreign('raw_material_id')
                    ->references('id')
                    ->on('raw_materials')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_recipes');
        Schema::dropIfExists('raw_materials');
    }
};
