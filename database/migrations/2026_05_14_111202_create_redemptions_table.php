<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redemptions', function (Blueprint $table) {

            $table->id();

            // 👤 USER (your custom PK: id_user)
            $table->unsignedBigInteger('user_id');

            $table->foreign('user_id')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade');

            // 🎁 REWARD (STANDARD Laravel ID = id)
            $table->unsignedBigInteger('reward_id');

            $table->foreign('reward_id')
                ->references('id')
                ->on('rewards')
                ->onDelete('cascade');

            // ⚡ EXP used
            $table->integer('exp_used');

            // 📌 status
            $table->string('status');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redemptions');
    }
};