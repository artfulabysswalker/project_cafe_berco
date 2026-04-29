<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 8, 2);
            $table->integer('quantity')->nullable(); // null = unlimited
            $table->integer('quantity_used')->default(0);
            $table->decimal('minimum_purchase', 10, 2)->nullable();
            $table->dateTime('valid_from');
            $table->dateTime('valid_until');
            $table->boolean('is_active')->default(true);
            $table->enum('voucher_type', ['automatic', 'manual'])->default('manual');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
