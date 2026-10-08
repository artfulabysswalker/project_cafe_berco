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
        Schema::create('table_qr_codes', function (Blueprint $table) {
            $table->id('id_qr_code');
            $table->unsignedBigInteger('table_id');
            $table->uuid('token')->unique();
            $table->unsignedInteger('version')->default(1);
            $table->timestamp('expired_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->timestamps();

            $table->string('image_path')->nullable();
            $table->foreign('table_id')->references('id_meja')->on('mejas')->cascadeOnDelete();
            $table->foreign('generated_by')->references('id_user')->on('users')->nullOnDelete();
            $table->index(['table_id', 'is_active']);
            $table->index('expired_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_qr_codes');
    }
};
