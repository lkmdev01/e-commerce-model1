<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar se a coluna slug não existe
        if (!Schema::hasColumn('products', 'slug')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('slug')->after('featured');
            });
        }

        // Atualizar registros com slug vazio
        $products = DB::table('products')->whereNull('slug')->orWhere('slug', '')->get();
        foreach ($products as $product) {
            $slug = Str::slug($product->name);
            $originalSlug = $slug;
            $count = 1;
            
            // Garantir que o slug seja único
            while (DB::table('products')->where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            
            DB::table('products')->where('id', $product->id)->update(['slug' => $slug]);
        }

        // Adicionar restrição de unicidade somente se já não existir
        if (!$this->hasUniqueIndex('products', 'products_slug_unique')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unique('slug', 'products_slug_unique');
            });
        }
    }

    /**
     * Verifica se um índice único já existe
     */
    private function hasUniqueIndex($table, $indexName): bool
    {
        $indexes = DB::select("SHOW INDEXES FROM {$table} WHERE Key_name = '{$indexName}'");
        return count($indexes) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }
};
