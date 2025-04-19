<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário normal se não existir
        if (!User::where('email', 'user@ecommercesol.com')->exists()) {
            User::create([
                'name' => 'Usuário Normal',
                'email' => 'user@ecommercesol.com',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'status' => true,
                'email_verified_at' => now(),
            ]);

            $this->command->info('Usuário normal criado com sucesso!');
        } else {
            $this->command->info('Usuário normal já existe.');
        }
    }
} 