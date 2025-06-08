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
            $table->string('full_name');
            $table->string('tax_id', 20)->nullable();                       // Documento de identificação
            $table->string('phone', 25)->nullable();
            $table->string('address_line1', 150)->nullable();               // Rua, avenida e número residencia
            $table->string('address_line2', 100)->nullable();               // Complemento (apto, bloco, etc...)         
            $table->string('neighborhood', 100)->nullable();    
            $table->string('city', 100)->nullable();
            $table->string('state', 2)->nullable();                         // SP, MG, etc...
            $table->string('postal_code', 10)->nullable();
            $table->string('country', 2)->nullable();                       // ISO alpha-2
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'full_name',
                'tax_id',
                'phone',
                'address_line1',
                'address_line2',
                'neighborhood',
                'city',
                'state',
                'postal_code',
                'country',
                'gender'
            ]);
        });
    }
};
