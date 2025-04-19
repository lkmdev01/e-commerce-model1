<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Obtém todas as categorias existentes
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            $this->command->info('Nenhuma categoria encontrada. Execute o CategorySeeder primeiro.');
            return;
        }

        $products = [
            [
                'name' => 'Smartphone XYZ',
                'description' => 'Um smartphone incrível com câmera de alta resolução',
                'price' => 1999.99,
                'stock' => 10,
                'image' => 'products/smartphone.jpg',
                'active' => true,
                'featured' => true,
            ],
            [
                'name' => 'Notebook ABC',
                'description' => 'Notebook potente para trabalho e jogos',
                'price' => 4999.99,
                'stock' => 5,
                'image' => 'products/notebook.jpg',
                'active' => true,
                'featured' => false,
            ],
            [
                'name' => 'Fone de Ouvido 123',
                'description' => 'Fone de ouvido sem fio com cancelamento de ruído',
                'price' => 299.99,
                'stock' => 20,
                'image' => 'products/headphone.jpg',
                'active' => true,
                'featured' => true,
            ]
        ];

        foreach ($products as $productData) {
            // Seleciona uma categoria aleatória
            $category = $categories->random();
            
            // Adiciona a categoria e o slug ao produto
            $productData['category'] = $category->name;
            $productData['category_id'] = $category->id;
            $productData['slug'] = Str::slug($productData['name']);
            
            Product::create($productData);
        }
    }
} 