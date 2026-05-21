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

            // user references users.id_user
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id_user')
                ->on('users')
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