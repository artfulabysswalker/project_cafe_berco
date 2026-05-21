<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('playlist_votes', function (Blueprint $table) {

            $table->id();

            // user references users.id_user
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade');

            $table->foreignId('playlist_id')
                ->constrained('playlists')
                ->cascadeOnDelete();

            $table->enum('vote_type', ['upvote', 'downvote']);

            $table->timestamps();

            // one vote per user per playlist
            $table->unique(['user_id', 'playlist_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('playlist_votes');
    }
};