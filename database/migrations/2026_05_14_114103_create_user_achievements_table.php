<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_achievements', function (Blueprint $table) {

            $table->id();

            // 🔥 FIXED: because users table uses id_user (NOT id)
            $table->foreignId('user_id')
                ->constrained('users', 'id_user')
                ->onDelete('cascade');

            // achievements uses default id → OK
            $table->foreignId('achievement_id')
                ->constrained('achievements')
                ->onDelete('cascade');

            // when user earned it
            $table->timestamp('earned_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
    }
};