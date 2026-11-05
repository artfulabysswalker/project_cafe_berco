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
      Schema::create('topping_sizes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('topping_id')->constrained('toppings')->cascadeOnDelete();
    $table->enum('size', ['small', 'medium', 'large']);
    $table->decimal('quantity', 10, 2);
    $table->enum('unit', ['g', 'ml']);
    $table->decimal('price', 12, 2)->default(0);
    $table->timestamps();

    $table->unique(['topping_id', 'size']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topping_sizes');
    }
};
