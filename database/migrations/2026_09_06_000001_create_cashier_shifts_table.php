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
        if (! Schema::hasTable('cashier_shifts')) {
            Schema::create('cashier_shifts', function (Blueprint $table) {
                $table->id('id_shift');

                // Foreign key ke tabel users (id_user)
                $table->foreignId('user_id')->constrained('users', 'id_user')->onDelete('cascade');

                // Tipe Shift: shift_1 (07:00 - 15:00), shift_2 (15:00 - 23:00)
                $table->enum('shift_type', ['shift_1', 'shift_2'])->comment('shift_1: Pagi (1 Kasir), shift_2: Sore/Malam (2 Kasir)');

                // Drawer Kas & Rekonsiliasi Keuangan
                $table->decimal('starting_cash', 15, 2)->comment('Modal kas awal di laci');
                $table->decimal('cash_sales', 15, 2)->default(0)->comment('Total omzet cash masuk');
                $table->decimal('non_cash_sales', 15, 2)->default(0)->comment('Total omzet QRIS / non-tunai');
                $table->decimal('expected_cash', 15, 2)->nullable()->comment('starting_cash + cash_sales');
                $table->decimal('actual_cash', 15, 2)->nullable()->comment('Fisik uang di laci saat closing');
                $table->decimal('difference', 15, 2)->nullable()->comment('actual_cash - expected_cash');

                // Status & Waktu Operasional
                $table->enum('status', ['open', 'closed'])->default('open');
                $table->timestamp('opened_at');
                $table->timestamp('closed_at')->nullable();
                $table->text('notes')->nullable()->comment('Catatan selisih / hand-over kasir');

                $table->timestamps();

                // Compound Index untuk performa & isolasi query
                $table->index(['user_id', 'status'], 'idx_user_shift_status');
                $table->index(['shift_type', 'status', 'opened_at'], 'idx_shift_type_reporting');
            });
        }

        // Hubungkan tabel orders ke cashier_shifts jika belum ada id_shift
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (! Schema::hasColumn('orders', 'id_shift')) {
                    $table->foreignId('id_shift')->nullable()->after('id_user')
                        ->constrained('cashier_shifts', 'id_shift')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'id_shift')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropConstrainedForeignId('id_shift');
            });
        }
        Schema::dropIfExists('cashier_shifts');
    }
};
