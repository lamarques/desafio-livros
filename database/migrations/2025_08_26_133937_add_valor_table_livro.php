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
        Schema::table('Livro', function (Blueprint $table) {
            $table->decimal('Valor', 12, 2)->after('AnoPublicacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Livro', function (Blueprint $table) {
            $table->dropColumn('Valor');
        });
    }
};
