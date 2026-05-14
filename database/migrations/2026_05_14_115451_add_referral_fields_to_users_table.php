<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('referral_code')->nullable()->unique();

            // 🔥 FIXED: must match users.id_user
            $table->foreignId('referred_by')
                ->nullable()
                ->constrained('users', 'id_user')
                ->nullOnDelete();

            $table->integer('referral_balance')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // drop FK first (important)
            $table->dropForeign(['referred_by']);

            $table->dropColumn([
                'referral_code',
                'referred_by',
                'referral_balance'
            ]);
        });
    }
};