<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'category_id',
        'image',
        'stock',
        'active',
        'featured',
        'slug'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
        'featured' => 'boolean',
    ];

    protected $appends = ['image_url'];

    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the image URL attribute.
     *
     * @return string|null
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        return asset('storage/' . $this->image);
    }
} 