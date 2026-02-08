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
    // Tabel Kategori (Makanan, Minuman, Snack)
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });

    // Tabel Menu (Nasi Goreng, Es Teh, dll)
    Schema::create('menus', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->constrained();
        $table->string('name');
        $table->integer('price');
        $table->string('image_path'); // Link ke MinIO
        $table->boolean('is_available')->default(true);
        $table->timestamps();
    });

    // Tabel Meja
    Schema::create('tables', function (Blueprint $table) {
        $table->id();
        $table->string('table_number');
        $table->enum('status', ['available', 'occupied'])->default('available');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_tables');
    }
};
