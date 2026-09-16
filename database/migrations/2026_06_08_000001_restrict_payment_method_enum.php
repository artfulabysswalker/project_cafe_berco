<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Restrict payment methods to only cash and qris
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('cash', 'qris') DEFAULT 'cash'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('cash', 'debit', 'credit', 'card', 'e_wallet', 'bank_transfer', 'qris') DEFAULT 'cash'");
        }
    }
};
