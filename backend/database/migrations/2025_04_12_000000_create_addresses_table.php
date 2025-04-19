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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Identificação do endereço (ex: Casa, Trabalho)
            $table->string('street'); // Rua/Avenida
            $table->string('number'); // Número
            $table->string('complement')->nullable(); // Complemento
            $table->string('district'); // Bairro
            $table->string('city'); // Cidade
            $table->string('state', 2); // Estado (UF)
            $table->string('zipcode'); // CEP
            $table->boolean('is_default')->default(false); // Indica se é o endereço padrão
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
}; 