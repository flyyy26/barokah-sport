<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'discount_price',
        'stock',
        'weight',
        'is_active',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'stock' => 'integer',
        'weight' => 'integer',
        'is_active' => 'boolean',
    ];

    // ============================================
    // RELATIONS
    // ============================================

    /**
     * Relasi ke Product (belongs to)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke ProductVariantValue (has many)
     * Melalui tabel product_variant_values
     */
    public function variantValues(): HasMany
    {
        return $this->hasMany(ProductVariantValue::class, 'product_variant_id');
    }

    /**
     * 🔥 RELASI KE PRODUCT_OPTION_VALUES (MANY-TO-MANY)
     * Melalui tabel product_variant_values
     * 
     * Digunakan untuk mendapatkan nilai-nilai option dari varian ini
     * Contoh: $variant->values->pluck('value') -> ['Merah', 'M']
     */
    public function values(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductOptionValue::class,
            'product_variant_values',
            'product_variant_id',
            'product_option_value_id'
        )->withTimestamps();
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductOption::class,
            'product_variant_values',
            'product_variant_id',
            'product_option_value_id'
        );
    }

    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class, 'product_variant_id');
    }

    public function updateStock(int $newStock, string $reason = 'adjustment', ?string $note = null): bool
    {
        $oldStock = $this->stock;
        
        if ($oldStock == $newStock) {
            return true;
        }

        $quantityChange = $newStock - $oldStock;

        // 🔥 UPDATE STOK
        $this->stock = $newStock;
        $saved = $this->save();

        if ($saved) {
            // 🔥 CATAT HISTORY
            $this->recordHistory($oldStock, $newStock, $quantityChange, $reason, $note);
        }

        return $saved;
    }

    public function addStock(int $quantity, string $reason = 'restock', ?string $note = null): bool
    {
        $newStock = $this->stock + $quantity;
        return $this->updateStock($newStock, $reason, $note);
    }

    public function reduceStock(int $quantity, string $reason = 'sale', ?string $note = null): bool
    {
        if ($this->stock < $quantity) {
            return false;
        }
        $newStock = $this->stock - $quantity;
        return $this->updateStock($newStock, $reason, $note);
    }

    public function recordHistory(int $oldStock, int $newStock, int $quantityChange, string $reason, ?string $note = null): void
    {
        $this->stockHistories()->create([
            'product_id' => $this->product_id,
            'product_variant_id' => $this->id,
            'user_id' => auth()->id() ?? 1, // Default admin jika tidak login
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'quantity_change' => $quantityChange,
            'reason' => $reason,
            'note' => $note,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // ============================================
    // ACCESSORS
    // ============================================

    /**
     * Mendapatkan harga efektif (harga diskon jika ada, selain itu harga normal)
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->discount_price ?? $this->price;
    }

    /**
     * Mendapatkan label harga untuk ditampilkan
     */
    public function getPriceLabelAttribute(): string
    {
        if ($this->discount_price && $this->discount_price < $this->price) {
            return 'Rp ' . number_format($this->discount_price, 0, ',', '.') . 
                   ' (diskon ' . number_format((1 - $this->discount_price / $this->price) * 100, 0) . '%)';
        }
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Mendapatkan persentase diskon
     */
    public function getDiscountPercentAttribute(): float
    {
        if ($this->discount_price && $this->price > 0 && $this->discount_price < $this->price) {
            return round((1 - $this->discount_price / $this->price) * 100, 0);
        }
        return 0;
    }

    public function getDiscountLabelAttribute(): string
    {
        if ($this->discount_price && $this->price > 0 && $this->discount_price < $this->price) {
            return 'Diskon ' . $this->discount_percent . '%';
        }
        return '';
    }
    /**
     * Mendapatkan status stok (in_stock / out_of_stock / low_stock)
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'out_of_stock';
        }
        if ($this->stock <= 5) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    /**
     * Mendapatkan label status stok
     */
    public function getStockStatusLabelAttribute(): string
    {
        return [
            'out_of_stock' => 'Habis',
            'low_stock' => 'Stok Terbatas',
            'in_stock' => 'Tersedia',
        ][$this->stock_status] ?? 'Tersedia';
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope untuk varian yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk varian yang memiliki stok
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope untuk varian yang sedang diskon
     */
    public function scopeOnSale($query)
    {
        return $query->whereNotNull('discount_price')
                     ->whereColumn('discount_price', '<', 'price');
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Cek apakah varian ini memiliki stok
     */
    public function hasStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Cek apakah varian ini sedang diskon
     */
    public function isOnSale(): bool
    {
        return $this->discount_price && $this->discount_price < $this->price;
    }

    /**
     * Mendapatkan kombinasi option values sebagai string
     * Contoh: "Merah / XL"
     */
    public function getOptionCombinationAttribute(): string
    {
        if (!$this->relationLoaded('values')) {
            $this->load('values');
        }

        return $this->values->pluck('value')->implode(' / ');
    }

    /**
     * Mendapatkan option values sebagai array
     */
    public function getOptionValuesArray(): array
    {
        if (!$this->relationLoaded('values')) {
            $this->load('values');
        }

        return $this->values->pluck('id')->toArray();
    }

    /**
     * Cek apakah varian ini memiliki kombinasi option values tertentu
     */
    public function hasOptionValues(array $valueIds): bool
    {
        if (!$this->relationLoaded('values')) {
            $this->load('values');
        }

        $variantValueIds = $this->values->pluck('id')->toArray();
        return empty(array_diff($valueIds, $variantValueIds));
    }

    /**
     * Kurangi stok
     */
    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock < $quantity) {
            return false;
        }

        $this->stock -= $quantity;
        return $this->save();
    }

    /**
     * Tambah stok
     */
    public function increaseStock(int $quantity): bool
    {
        $this->stock += $quantity;
        return $this->save();
    }
}