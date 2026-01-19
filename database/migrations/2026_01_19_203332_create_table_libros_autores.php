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
                ->constrained('autores')
                ->onDelete('cascade');

            $table->foreignId('libro_id')->constrained()->onDelete('cascade');
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
