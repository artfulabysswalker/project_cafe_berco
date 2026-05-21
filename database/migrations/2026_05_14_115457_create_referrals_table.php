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

            // referrer/referee reference users.id_user
            $table->unsignedBigInteger('referrer_id');
            $table->foreign('referrer_id')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade');

            $table->unsignedBigInteger('referee_id')->nullable();
            $table->foreign('referee_id')
                ->references('id_user')
                ->on('users')
                ->onDelete('set null');

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