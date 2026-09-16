<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'hpp_at_sale')) {
                $table->decimal('hpp_at_sale', 12, 2)->default(0)->after('subtotal');
            }
        });

        // Copy existing hpp to hpp_at_sale if available
        if (Schema::hasColumn('order_items', 'hpp')) {
            DB::table('order_items')->where('hpp_at_sale', 0)->update([
                'hpp_at_sale' => DB::raw('hpp'),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'hpp_at_sale')) {
                $table->dropColumn('hpp_at_sale');
            }
        });
    }
};
