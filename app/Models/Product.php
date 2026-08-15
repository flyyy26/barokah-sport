<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getPriceAttribute()
    {
        $firstVariant = $this->variants->first();
        return $firstVariant ? (float) $firstVariant->price : 0;
    }

    public function getDiscountPriceAttribute()
    {
        $firstVariant = $this->variants->first();
        return $firstVariant ? (float) $firstVariant->discount_price : null;
    }

    public function getStockAttribute()
    {
        return (int) $this->variants->sum('stock');
    }

    public function getMinPriceAttribute()
    {
        return (float) $this->variants->min('price') ?? 0;
    }

    public function getMaxPriceAttribute()
    {
        return (float) $this->variants->max('price') ?? 0;
    }

    public function getPriceFormattedAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getPriceRangeAttribute()
    {
        $min = $this->variants->min('price');
        $max = $this->variants->max('price');

        if ($min == $max) {
            return 'Rp ' . number_format($min, 0, ',', '.');
        }

        return 'Rp ' . number_format($min, 0, ',', '.') . ' - Rp ' . number_format($max, 0, ',', '.');
    }

    public function getWeightAttribute()
    {
        $firstVariant = $this->variants->first();
        return $firstVariant ? $firstVariant->weight : 1000;
    }
}