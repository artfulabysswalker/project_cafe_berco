<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_vouchers', function (Blueprint $table) {

            $table->id();

            // 🔥 IMPORTANT FIX: point to id_user
            $table->foreignId('user_id')
                ->constrained('users', 'id_user')
                ->onDelete('cascade');

            // vouchers still uses default id → OK
            $table->foreignId('voucher_id')
                ->constrained('vouchers')
                ->onDelete('cascade');

            $table->enum('status', ['active', 'used', 'expired'])
                ->default('active');

            $table->timestamp('notified_at')->nullable();
            $table->timestamp('used_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_vouchers');
    }
};