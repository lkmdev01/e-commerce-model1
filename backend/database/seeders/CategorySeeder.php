<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Eletrônicos',
                'description' => 'Produtos eletrônicos e tecnológicos',
                'active' => true
            ],
            [
                'name' => 'Roupas',
                'description' => 'Vestuário e moda',
                'active' => true
            ],
            [
                'name' => 'Acessórios',
                'description' => 'Acessórios diversos para complementar seu estilo',
                'active' => true
            ],
            [
                'name' => 'Casa e Decoração',
                'description' => 'Itens para sua casa e decoração',
                'active' => true
            ],
            [
                'name' => 'Esportes',
                'description' => 'Artigos esportivos',
                'active' => true
            ]
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'active' => $category['active']
            ]);
        }
    }
} 