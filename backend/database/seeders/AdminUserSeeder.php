<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário admin se não existir
        if (!User::where('email', 'admin@ecommercesol.com')->exists()) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@ecommercesol.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => true,
                'email_verified_at' => now(),
            ]);

            $this->command->info('Usuário administrador criado com sucesso!');
        } else {
            $this->command->info('Usuário administrador já existe.');
        }
    }
} 