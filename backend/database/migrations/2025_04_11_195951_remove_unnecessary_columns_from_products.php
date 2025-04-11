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
        Schema::table('products', function (Blueprint $table) {
            // Remover colunas que não deveriam estar no banco
            if (Schema::hasColumn('products', 'image_url')) {
                $table->dropColumn('image_url');
            }
            
            // Remover a coluna status que está duplicando a funcionalidade de active
            if (Schema::hasColumn('products', 'status')) {
                $table->dropColumn('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Não vamos recriar essas colunas, pois elas não devem existir
        });
    }
};
