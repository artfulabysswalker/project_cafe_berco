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
        Schema::create('discount_schemes', function (Blueprint $table) {
            $table->id('id_discount_scheme');
            $table->string('code'); // e.g., "PROMO_MORNING"
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed']); // Persentase atau nominal
            $table->decimal('discount_value', 10, 2); // Nilai diskon
            $table->decimal('min_purchase', 10, 2)->nullable(); // Minimum pembelian
            $table->decimal('max_discount', 10, 2)->nullable(); // Max diskon jika persentase
            $table->integer('max_uses')->nullable(); // Maksimal penggunaan
            $table->integer('times_used')->default(0);
            $table->boolean('is_active')->default(true);
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable();
            $table->foreignId('id_user') // Admin who created
                ->constrained('users', 'id_user')
                ->onDelete('cascade');
            $table->timestamps();

            $table->unique('code');
            $table->index('is_active');
            $table->index('valid_from');
            $table->index('valid_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_schemes');
    }
};
