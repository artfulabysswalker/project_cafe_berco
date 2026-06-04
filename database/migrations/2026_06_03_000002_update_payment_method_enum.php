<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the payment_method enum to include new payment types
        DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('cash', 'debit', 'credit', 'card', 'e_wallet', 'bank_transfer', 'qris') DEFAULT 'cash'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('cash', 'debit', 'credit') DEFAULT 'cash'");
    }
};
