<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock' => 'required|integer|min:0',
            'active' => 'boolean',
            'featured' => 'boolean', // Campo para marcar produtos em destaque
        ]);

        $imagePath = $request->file('image')->store('products', 'public');

        // Recupera o nome da categoria para manter retrocompatibilidade
        $categoryName = Category::find($request->category_id)->name;

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $categoryName, // Mantendo para retrocompatibilidade
            'category_id' => $request->category_id,
            'image' => $imagePath,
            'stock' => $request->stock,
            'active' => $request->active ?? true,
            'featured' => $request->featured ?? false,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        $product->load('category');
        return response()->json($product);
    }

    /**
     * Mostrar um produto pelo ID
     */
    public function showById(int $id)
    {
        $product = Product::with('category')->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric|min:0',
            'category_id' => 'exists:categories,id',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock' => 'integer|min:0',
            'active' => 'boolean',
            'featured' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        // Tratar explicitamente os valores booleanos
        if ($request->has('active')) {
            $product->active = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);
        }
        
        if ($request->has('featured')) {
            $product->featured = filter_var($request->featured, FILTER_VALIDATE_BOOLEAN);
        }

        // Atualizar category quando category_id for fornecido
        if ($request->has('category_id')) {
            $categoryName = Category::find($request->category_id)->name;
            $product->category = $categoryName;
            $product->category_id = $request->category_id;
        }
        
        // Preencher outros campos
        $product->fill($request->except(['image', 'active', 'featured', 'category_id']));
        
        if ($request->has('name')) {
            $product->slug = Str::slug($request->name);
        }
        
        $product->save();

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return response()->json(null, 204);
    }
} 