<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_requests', function (Blueprint $table) {

            $table->id();

            $table->foreignId('id_user')
                ->constrained('users','id_user')
                ->onDelete('cascade');

            $table->string('full_name');

            $table->string('email');

            $table->text('reason');

            $table->enum('status', [
                'pending',
                'resolved'
            ])->default('pending');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_requests');
    }
};