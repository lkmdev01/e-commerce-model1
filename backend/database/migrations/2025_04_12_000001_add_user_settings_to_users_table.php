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
            $table->boolean('newsletter')->default(true)->after('status'); // Opção para receber newsletter
            $table->json('preferences')->nullable()->after('newsletter'); // Preferências gerais do usuário em formato JSON
            $table->timestamp('last_login_at')->nullable()->after('preferences'); // Data/hora do último login
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['newsletter', 'preferences', 'last_login_at']);
        });
    }
}; 