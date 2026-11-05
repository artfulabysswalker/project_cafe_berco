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
      Schema::create('menu_toppings', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('menu_id');
    $table->foreign('menu_id')
        ->references('id_menu')
        ->on('menus')
        ->cascadeOnDelete();

    $table->foreignId('topping_id')->constrained('toppings')->cascadeOnDelete();
    $table->timestamps();

    $table->unique(['menu_id', 'topping_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_toppings');
    }
};
