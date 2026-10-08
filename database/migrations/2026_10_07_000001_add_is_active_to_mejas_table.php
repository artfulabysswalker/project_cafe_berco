<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mejas', function (Blueprint $table) {
            if (! Schema::hasColumn('mejas', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('area');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mejas', function (Blueprint $table) {
            if (Schema::hasColumn('mejas', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
