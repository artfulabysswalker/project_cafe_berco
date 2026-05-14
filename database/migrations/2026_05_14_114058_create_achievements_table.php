<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {

            $table->id();

            $table->string('name');
            $table->text('description')->nullable();

            // types: orders_count, total_spent, referrals_count
            $table->string('type');

            // requirement to unlock
            $table->integer('threshold')->default(0);

            // reward value (money / points)
            $table->integer('reward_amount')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};