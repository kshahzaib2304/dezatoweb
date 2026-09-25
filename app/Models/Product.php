<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'slug',
        'name',
        'description',
        'price',
        'badge',
        'weight',
        'image',
        'is_active',
        'is_featured',
        'stock',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'stock' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function imageUrl(): string
    {
        return asset($this->publicImagePath());
    }

    public function publicImagePath(): string
    {
        $image = trim((string) $this->image);

        if ($image === '' || str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return 'images/home/hero.jpg';
        }

        if (str_starts_with($image, 'images/') || str_starts_with($image, 'storage/')) {
            return $image;
        }

        return 'storage/'.$image;
    }

    public function isInStock(): bool
    {
        return $this->stock === null || $this->stock > 0;
    }

    /**
     * @return array<string, mixed>
     */
    public function toCatalogArray(): array
    {
        return [
            'id' => $this->slug,
            'name' => $this->name,
            'category' => $this->category?->slug ?? 'cakes',
            'price' => (float) $this->price,
            'badge' => $this->badge,
            'weight' => $this->weight,
            'description' => (string) $this->description,
            'image' => $this->publicImagePath(),
            'stock' => $this->stock,
            'is_featured' => $this->is_featured,
        ];
    }
}
