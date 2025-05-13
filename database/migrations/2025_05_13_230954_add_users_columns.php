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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nome_completo', 100);
            $table->string('cpf', 11);
            $table->string('logradouro', 256);
            $table->string('numero_logradouro', 10);
            $table->string('bairro', 64);
            $table->string('municipio', 64);
            $table->string('uf', 2);
            $table->string('cep', 8);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nome_completo', 'cpf', 'logradouro',
                'numero_logradouro', 'numero_logradouro', 'bairro',
                'municipio', 'uf', 'cep'
            ]);
        });
    }
};
