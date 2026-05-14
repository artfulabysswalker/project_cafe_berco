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
        Schema::create('receipt_settings', function (Blueprint $table) {
            $table->id();

            $table->string('cafe_name')->default('Cafe Barco');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();

            $table->text('footer_message')->nullable();

            $table->string('wifi_name')->nullable();
            $table->string('wifi_password')->nullable();

            $table->string('logo')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_settings');
    }
};
