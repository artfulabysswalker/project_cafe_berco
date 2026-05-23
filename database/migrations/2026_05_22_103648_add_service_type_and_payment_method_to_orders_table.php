<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('service_type', ['dine_in', 'take_away'])->default('dine_in')->after('status_pembayaran');
            $table->enum('payment_method', ['cash', 'debit', 'credit'])->default('cash')->after('service_type');
            $table->text('notes')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['service_type', 'payment_method', 'notes']);
        });
    }
};
