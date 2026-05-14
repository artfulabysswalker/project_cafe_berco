<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {

            $table->id();

            $table->string('referral_code');

            // 🔥 FIXED: match users.id_user
            $table->foreignId('referrer_id')
                ->constrained('users', 'id_user')
                ->cascadeOnDelete();

            $table->foreignId('referee_id')
                ->nullable()
                ->constrained('users', 'id_user')
                ->nullOnDelete();

            $table->enum('status', ['pending', 'completed', 'active'])
    ->default('pending');

            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};