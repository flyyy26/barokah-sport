<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Voucher extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'terms_and_conditions',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'min_transaction_amount',
        'usage_limit',
        'used_count',
        'limit_per_user',
        'start_date',
        'end_date',
        'is_active',
        'is_public',
    ];

    protected $casts = [
        'discount_value'         => 'decimal:2',
        'max_discount_amount'    => 'decimal:2',
        'min_transaction_amount' => 'decimal:2',
        'usage_limit'            => 'integer',
        'used_count'             => 'integer',
        'limit_per_user'         => 'integer',
        'start_date'             => 'datetime',
        'end_date'               => 'datetime',
        'is_active'              => 'boolean',
        'is_public'              => 'boolean',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    /**
     * Scope untuk voucher yang aktif dan publik
     */
    public function scopePublicActive($query)
    {
        $now = now();
        return $query->where('is_active', true)
                    ->where('is_public', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now)
                    ->where(function ($q) {
                        $q->whereNull('usage_limit')
                          ->orWhereRaw('used_count < usage_limit');
                    });
    }

    /**
     * Cek apakah voucher valid secara umum (waktu, status, dan kuota global)
     */
    public function isValidNow(): bool
    {
        if (!$this->is_active) return false;

        $now = now();
        if ($now->lt($this->start_date) || $now->gt($this->end_date)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Cek apakah user & nominal belanja memenuhi syarat voucher
     */
    public function checkEligibility(float $subtotal, ?int $userId = null): array
    {
        if (!$this->isValidNow()) {
            return ['eligible' => false, 'message' => 'Voucher sudah tidak aktif atau kuota habis.'];
        }

        if ($subtotal < $this->min_transaction_amount) {
            return [
                'eligible' => false,
                'message'  => 'Minimal transaksi Rp ' . number_format($this->min_transaction_amount, 0, ',', '.')
            ];
        }

        if ($userId && $this->limit_per_user > 0) {
            $userUsage = $this->usages()->where('user_id', $userId)->count();
            if ($userUsage >= $this->limit_per_user) {
                return ['eligible' => false, 'message' => 'Anda sudah mencapai batas penggunaan voucher ini.'];
            }
        }

        return ['eligible' => true, 'message' => 'Voucher dapat digunakan.'];
    }

    /**
     * Hitung nominal potongan berdasarkan subtotal
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->discount_type === 'fixed') {
            return min($this->discount_value, $subtotal);
        }

        // Tipe percentage
        $potongan = $subtotal * ($this->discount_value / 100);

        if ($this->max_discount_amount && $potongan > $this->max_discount_amount) {
            $potongan = $this->max_discount_amount;
        }

        return min(round($potongan, 2), $subtotal);
    }
}