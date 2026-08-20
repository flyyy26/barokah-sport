{{-- customer/cart/popup.blade.php --}}
@if (empty($cart))
    <div class="popup-body-empty">
        <iconify-icon icon="mdi:cart-outline"></iconify-icon>
        <p>Keranjang kosong</p>
        <p>Yuk, mulai belanja!</p>
    </div>
@else
    <div class="space-y-3">
        @foreach ($cart as $key => $item)
            <div class="cart-item" data-key="{{ $key }}">
                {{-- Image --}}
                <div class="cart-item-image">
                    @if (!empty($item['image']) && Storage::disk('public')->exists($item['image']))
                        <img src="{{ Storage::url($item['image']) }}" 
                             alt="{{ $item['product_name'] }}">
                    @elseif (!empty($item['image']) && filter_var($item['image'], FILTER_VALIDATE_URL))
                        <img src="{{ $item['image'] }}" 
                             alt="{{ $item['product_name'] }}">
                    @else
                        <span class="placeholder">📦</span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="cart-item-info">
                    <p class="cart-item-name">{{ $item['product_name'] }}</p>
                    @if (!empty($item['variant_name']))
                        <p class="cart-item-variant">{{ $item['variant_name'] }}</p>
                    @endif
                    
                    {{-- 🔥 TAMPILKAN HARGA ASLI DAN DISKON --}}
                    @php
                        // Cari varian berdasarkan ID
                        $variant = null;
                        if (!empty($item['variant_id'])) {
                            $variant = \App\Models\ProductVariant::find($item['variant_id']);
                        }
                        // Jika tidak ada varian, cari product untuk ambil harga asli
                        if (!$variant && !empty($item['product_id'])) {
                            $product = \App\Models\Product::with('variants')->find($item['product_id']);
                            if ($product && $product->variants->isNotEmpty()) {
                                $variant = $product->variants->first();
                            }
                        }
                        
                        $originalPrice = $item['price']; // default
                        $discountPrice = null;
                        
                        if ($variant) {
                            $originalPrice = $variant->price;
                            $discountPrice = $variant->discount_price;
                        }
                        
                        // Cek apakah ada diskon
                        $hasDiscount = $discountPrice && $discountPrice < $originalPrice;
                    @endphp
                    
                    <div class="cart-item-price-wrapper">
                        @if($hasDiscount)
                            {{-- Tampilkan harga diskon (merah) dan harga asli (coret) --}}
                            <p class="cart-item-price cart-item-price-discount">
                                Rp {{ number_format($discountPrice, 0, ',', '.') }}
                            </p>
                            <span class="cart-item-price-original">
                                Rp {{ number_format($originalPrice, 0, ',', '.') }}
                            </span>
                        @else
                            {{-- Tampilkan harga normal --}}
                            <p class="cart-item-price">
                                Rp {{ number_format($originalPrice, 0, ',', '.') }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Quantity & Remove --}}
                <div class="cart-item-actions">
                    <div class="qty-wrapper">
                        <button class="qty-btn" data-action="decrease" data-key="{{ $key }}" aria-label="Kurangi">−</button>
                        <input type="number" class="qty-input" value="{{ $item['quantity'] }}" 
                               min="1" data-key="{{ $key }}" aria-label="Jumlah">
                        <button class="qty-btn" data-action="increase" data-key="{{ $key }}" aria-label="Tambah">+</button>
                    </div>
                    <button class="btn-remove" data-key="{{ $key }}" aria-label="Hapus item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif