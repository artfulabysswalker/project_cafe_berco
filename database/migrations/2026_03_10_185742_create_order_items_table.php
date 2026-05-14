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
    Schema::create('order_items', function (Illuminate\Database\Schema\Blueprint $table) {
    $table->id();
    $table->foreignId('id_order')->constrained('orders', 'id_order')->onDelete('cascade');
    $table->foreignId('id_menu')->constrained('menus', 'id_menu');
    $table->integer('quantity');
    $table->integer('subtotal');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
