<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Orders: public tagihan/antrean code, waktu konfirmasi pembayaran,
        // dan status proses yang lebih kaya (pending -> processing -> ready).
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_code', 32)->nullable()->after('notes');
            $table->timestamp('paid_at')->nullable()->after('order_code');
        });

        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->enum('status_order', [
                    'pending',
                    'confirmed',
                    'processing',
                    'ready',
                    'completed',
                    'cancelled',
                ])->default('pending')->change();
            });
        } catch (Throwable $e) {
            // Fallback driver (MySQL/MariaDB) bila `change()` tidak didukung.
            Schema::getConnection()->statement(
                "ALTER TABLE `orders` MODIFY `status_order` ENUM('pending','confirmed','processing','ready','completed','cancelled') NOT NULL DEFAULT 'pending'"
            );
        }

        // Cart item: catatan khusus per item (mis. level pedas, ekstra topping).
        Schema::table('cart_items', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('quantity');
        });

        // Order item: snapshot catatan per item saat order dibuat.
        Schema::table('order_items', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_code', 'paid_at']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('notes');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
