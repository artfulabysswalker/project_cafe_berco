<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['comeback_reminder', 'voucher_offered', 'order_update', 'promotional'])->default('promotional');
            $table->string('related_url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->dateTime('read_at')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->enum('channel', ['in_app', 'email', 'both'])->default('in_app');
            $table->timestamps();
            
            $table->index(['user_id', 'is_read']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
