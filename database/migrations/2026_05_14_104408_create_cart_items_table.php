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
        Schema::create('cart_items', function (Blueprint $table) {

            $table->id();

            // 👤 USER (custom PK: id_user)
            $table->unsignedBigInteger('user_id');

            $table->foreign('user_id')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade');

            // 🍔 MENU (custom PK: id_menu)
            $table->unsignedBigInteger('menu_id');

            $table->foreign('menu_id')
                ->references('id_menu')
                ->on('menus')
                ->onDelete('cascade');

            // 🔢 Quantity
            $table->integer('quantity')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};