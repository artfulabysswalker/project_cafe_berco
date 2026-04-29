<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('last_visit_at')->nullable()->after('email_verified_at');
            $table->boolean('is_active')->default(true)->after('last_visit_at');
            $table->string('phone')->nullable()->after('email');
            $table->boolean('notification_enabled')->default(true)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_visit_at', 'is_active', 'phone', 'notification_enabled']);
        });
    }
};
