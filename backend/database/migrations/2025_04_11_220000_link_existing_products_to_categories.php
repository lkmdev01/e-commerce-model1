<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Obter todos os produtos que possuem uma categoria definida mas não possuem category_id
        $products = DB::table('products')
            ->whereNotNull('category')
            ->whereNull('category_id')
            ->get();

        // Para cada produto, encontre ou crie a categoria correspondente
        foreach ($products as $product) {
            // Tenta encontrar a categoria pelo nome
            $category = DB::table('categories')
                ->where('name', 'like', "%{$product->category}%")
                ->first();

            // Se não encontrou, cria a categoria
            if (!$category) {
                $categoryId = DB::table('categories')->insertGetId([
                    'name' => $product->category,
                    'slug' => \Illuminate\Support\Str::slug($product->category),
                    'description' => "Produtos de {$product->category}",
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $categoryId = $category->id;
            }

            // Atualiza o produto com o ID da categoria
            DB::table('products')
                ->where('id', $product->id)
                ->update(['category_id' => $categoryId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter as alterações não é realmente necessário para esta migração
        // já que os dados originais de category permanecem intactos
    }
}; 