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
        Schema::create('table_libros_autores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('autor_id')
                ->constrained('table_autores')
                ->onDelete('cascade');
            $table->integer('orden_autor')->default(1);
            $table->foreignId('libro_id')->constrained('table_libros')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['autor_id', 'libro_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_libros_autores');
    }
};
