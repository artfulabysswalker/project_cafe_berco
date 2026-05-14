<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('playlists', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('artist');

            $table->text('description')->nullable();

            $table->string('spotify_url')->nullable();
            $table->string('image_url')->nullable();

            $table->enum('status', ['active', 'inactive', 'completed'])
                ->default('active');

            $table->integer('vote_count')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('playlists');
    }
};