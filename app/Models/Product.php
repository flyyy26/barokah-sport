<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'gender',
        'material',
        'is_featured',
        'is_best_seller', 
        'is_active',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_best_seller' => 'boolean',
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
    // 🔥 ACCESSORS UNTUK HARGA
    // ============================================

    /**
     * Mendapatkan harga termurah dari semua varian
     */
    public function getMinPriceAttribute()
    {
        return (float) $this->variants->min('discount_price') ?: (float) $this->variants->min('price') ?: 0;
    }

    /**
     * Mendapatkan harga termahal dari semua varian
     */
    public function getMaxPriceAttribute()
    {
        return (float) $this->variants->max('price') ?: 0;
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'product_features')
                    ->withTimestamps();
    }

    /**
     * Mendapatkan harga dari varian pertama (untuk single price)
     */
    public function getPriceAttribute()
    {
        $firstVariant = $this->variants->first();
        return $firstVariant ? (float) $firstVariant->price : 0;
    }

    /**
     * Mendapatkan harga diskon dari varian pertama (untuk single price)
     */
    public function getDiscountPriceAttribute()
    {
        $firstVariant = $this->variants->first();
        return $firstVariant ? (float) $firstVariant->discount_price : null;
    }

    /**
     * Mendapatkan harga efektif (diskon jika ada) dari varian pertama
     */
    public function getEffectivePriceAttribute()
    {
        $firstVariant = $this->variants->first();
        if ($firstVariant) {
            return $firstVariant->discount_price ? (float) $firstVariant->discount_price : (float) $firstVariant->price;
        }
        return 0;
    }

    /**
     * 🔥 Mendapatkan range harga sebagai string
     * Contoh: "Rp 58.400 - Rp 64.800"
     * Atau jika harga sama: "Rp 58.400"
     */
    public function getPriceRangeAttribute()
    {
        $minPrice = $this->min_price;
        $maxPrice = $this->max_price;

        // Jika tidak ada varian
        if ($minPrice == 0 && $maxPrice == 0) {
            return 'Rp 0';
        }

        // Jika harga min dan max sama
        if ($minPrice == $maxPrice) {
            return 'Rp ' . number_format($minPrice, 0, ',', '.');
        }

        // Jika ada diskon, cek harga diskon terkecil dan terbesar
        $minDiscount = (float) $this->variants->min('discount_price');
        $maxDiscount = (float) $this->variants->max('discount_price');
        
        // Jika semua varian memiliki diskon
        if ($minDiscount > 0 && $this->variants->every(function($v) { return $v->discount_price !== null; })) {
            $minDisplay = $minDiscount;
            $maxDisplay = $maxDiscount;
        } else {
            // Cari harga terendah (bisa diskon atau normal)
            $prices = [];
            foreach ($this->variants as $variant) {
                $prices[] = $variant->discount_price ? (float) $variant->discount_price : (float) $variant->price;
            }
            $minDisplay = min($prices);
            $maxDisplay = max($prices);
        }

        if ($minDisplay == $maxDisplay) {
            return 'Rp ' . number_format($minDisplay, 0, ',', '.');
        }

        return 'Rp ' . number_format($minDisplay, 0, ',', '.') . ' - Rp ' . number_format($maxDisplay, 0, ',', '.');
    }

    /**
     * 🔥 Mendapatkan range harga dengan label diskon
     * Contoh: "Rp 58.400 - Rp 64.800"
     */
    public function getPriceRangeLabelAttribute()
    {
        return $this->price_range;
    }

    /**
     * Mendapatkan harga terendah dengan format Rupiah
     */
    public function getMinPriceFormattedAttribute()
    {
        return 'Rp ' . number_format($this->min_price, 0, ',', '.');
    }

    /**
     * Mendapatkan harga tertinggi dengan format Rupiah
     */
    public function getMaxPriceFormattedAttribute()
    {
        return 'Rp ' . number_format($this->max_price, 0, ',', '.');
    }

    // ============================================
    // ACCESSORS LAINNYA
    // ============================================

    public function getStockAttribute()
    {
        return (int) $this->variants->sum('stock');
    }

    public function getWeightAttribute()
    {
        $firstVariant = $this->variants->first();
        return $firstVariant ? $firstVariant->weight : 1000;
    }

    public function getOriginalPriceRangeAttribute()
    {
        $minPrice = $this->variants->min('price');
        $maxPrice = $this->variants->max('price');

        if ($minPrice == $maxPrice) {
            return 'Rp ' . number_format($minPrice, 0, ',', '.');
        }

        return 'Rp ' . number_format($minPrice, 0, ',', '.') . ' - Rp ' . number_format($maxPrice, 0, ',', '.');
    }

    public function getOriginalMinPriceAttribute()
    {
        return (float) $this->variants->min('price') ?: 0;
    }

    public function getOriginalMaxPriceAttribute()
    {
        return (float) $this->variants->max('price') ?: 0;
    }

    public function hasStock(): bool
    {
        return $this->variants->sum('stock') > 0;
    }

    public function isOutOfStock(): bool
    {
        return !$this->hasStock();
    }

    public function getTotalStockAttribute(): int
    {
        return (int) $this->variants->sum('stock');
    }
}