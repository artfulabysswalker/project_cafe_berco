<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add tax and discount tracking
            $table->decimal('subtotal', 12, 2)->default(0)->after('total_harga'); // Subtotal before tax & discount
            $table->decimal('tax_amount', 10, 2)->default(0)->after('subtotal'); // Tax amount
            $table->decimal('discount_amount', 10, 2)->default(0)->after('tax_amount'); // Discount amount
            $table->decimal('final_total', 12, 2)->default(0)->after('discount_amount'); // Final total
            $table->foreignId('id_tax_config')->nullable()->constrained('tax_configurations', 'id_tax_config')->onDelete('set null');
            $table->foreignId('id_discount_scheme')->nullable()->constrained('discount_schemes', 'id_discount_scheme')->onDelete('set null');
            $table->decimal('cost_of_goods', 10, 2)->nullable()->after('final_total'); // COGS for margin calculation
            $table->decimal('profit_margin', 10, 2)->nullable()->after('cost_of_goods'); // Profit = final_total - cost_of_goods
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['id_tax_config']);
            $table->dropForeign(['id_discount_scheme']);
            $table->dropColumn(['subtotal', 'tax_amount', 'discount_amount', 'final_total', 'id_tax_config', 'id_discount_scheme', 'cost_of_goods', 'profit_margin']);
        });
    }
};
