<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Smartphone XYZ',
                'description' => 'Um smartphone incrível com câmera de alta resolução',
                'price' => 1999.99,
                'stock' => 10,
                'image_url' => 'https://example.com/smartphone.jpg',
                'category' => 'Eletrônicos',
                'status' => 'active'
            ],
            [
                'name' => 'Notebook ABC',
                'description' => 'Notebook potente para trabalho e jogos',
                'price' => 4999.99,
                'stock' => 5,
                'image_url' => 'https://example.com/notebook.jpg',
                'category' => 'Eletrônicos',
                'status' => 'active'
            ],
            [
                'name' => 'Fone de Ouvido 123',
                'description' => 'Fone de ouvido sem fio com cancelamento de ruído',
                'price' => 299.99,
                'stock' => 20,
                'image_url' => 'https://example.com/headphone.jpg',
                'category' => 'Acessórios',
                'status' => 'active'
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
} 