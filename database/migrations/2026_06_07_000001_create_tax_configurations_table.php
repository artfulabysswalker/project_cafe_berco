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
        Schema::create('tax_configurations', function (Blueprint $table) {
            $table->id('id_tax_config');
            $table->string('name'); // e.g., "PB1 - Pajak Pertambahan Nilai"
            $table->decimal('tax_percentage', 5, 2); // e.g., 10.00 for 10%
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('effective_from')->nullable();
            $table->timestamp('effective_until')->nullable();
            $table->foreignId('id_user') // Admin who created/updated
                ->constrained('users', 'id_user')
                ->onDelete('cascade');
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('effective_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_configurations');
    }
};
