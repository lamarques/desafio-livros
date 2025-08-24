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
        Schema::create('Livro_Autor', function (Blueprint $table) {
            $table->integer('Livro_Codl');
            $table->integer('Autor_CodAu');
            $table->unique(['Livro_Codl', 'Autor_CodAu']);
            $table->index('Livro_Codl');
            $table->index('Autor_CodAu');
            $table->foreign('Livro_Codl', 'Livro_Autor_FKIndex1')
                ->references('Codl')->on('Livro')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('Autor_CodAu', 'Livro_Autor_FKIndex2')
                ->references('CodAu')->on('Autor')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livro_autors');
    }
};
