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
        Schema::create('Livro_Assunto', function (Blueprint $table) {
            $table->integer('Livro_Codl');
            $table->integer('Assunto_CodAs');
            $table->unique(['Livro_Codl', 'Assunto_CodAs']);
            $table->index('Livro_Codl');
            $table->index('Assunto_CodAs');
            $table->foreign('Livro_Codl', 'Livro_Assunto_FKIndex1')
                ->references('Codl')->on('Livro')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('Assunto_CodAs', 'Livro_Assunto_FKIndex2')
                ->references('CodAs')->on('Assunto')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livro_assuntos');
    }
};
