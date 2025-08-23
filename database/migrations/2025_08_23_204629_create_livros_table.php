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
        Schema::create('Livro', function (Blueprint $table) {
            $table->increments('Codl');
            $table->string('Titulo', 100);
            $table->string('Editora', 50);
            $table->integer('Edicao');
            $table->string('AnoPublicacao', 4);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livros');
    }
};
