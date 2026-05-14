<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {

            $table->id('id_menu');

            $table->string('nama_menu');
            $table->integer('harga');

            $table->boolean('status_tersedia')->default(true);

            $table->string('foto')->nullable();

            $table->decimal('rating', 2, 1)->default(0);

            $table->text('deskripsi')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};