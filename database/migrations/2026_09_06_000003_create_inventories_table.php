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
        if (! Schema::hasTable('inventories')) {
            Schema::create('inventories', function (Blueprint $table) {
                $table->id();
                $table->string('item_code')->unique()->comment('Kode SKU / Bahan, contoh: ING-001');
                $table->string('item_name')->comment('Nama Item / Bahan Baku');
                $table->string('category')->default('Bahan Baku')->comment('Kategori: Bahan Baku, Kemasan, Topping, Sirup, dll');
                $table->decimal('current_stock', 12, 2)->default(0)->comment('Stok Fisik Tersedia');
                $table->string('unit', 50)->default('Pcs')->comment('Satuan: Gram, Liter, Botol, Pcs, dll');
                $table->decimal('min_stock', 12, 2)->default(0)->comment('Batas Minimum Stok');
                $table->enum('status', ['aman', 'menipis', 'kritis'])->default('aman')->comment('Status Otomatis: aman, menipis, kritis');
                $table->timestamps();

                $table->index(['status', 'category'], 'idx_inventory_status_category');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
