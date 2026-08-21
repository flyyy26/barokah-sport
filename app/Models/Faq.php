<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'category',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    // Scope untuk FAQ aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk diurutkan
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    // Accessor untuk kategori yang lebih rapi
    public function getCategoryLabelAttribute()
    {
        $labels = [
            'umum' => 'Umum',
            'produk' => 'Produk',
            'pengiriman' => 'Pengiriman',
            'pembayaran' => 'Pembayaran',
            'garansi' => 'Garansi & Retur',
        ];

        return $labels[$this->category] ?? $this->category;
    }

    // Badge warna untuk kategori
    public function getCategoryBadgeAttribute()
    {
        $colors = [
            'umum' => 'bg-blue-100 text-blue-800',
            'produk' => 'bg-green-100 text-green-800',
            'pengiriman' => 'bg-yellow-100 text-yellow-800',
            'pembayaran' => 'bg-purple-100 text-purple-800',
            'garansi' => 'bg-red-100 text-red-800',
        ];

        return $colors[$this->category] ?? 'bg-gray-100 text-gray-800';
    }
}