{{-- customer/wishlist/popup.blade.php --}}
@if (empty($products) || $products->isEmpty())
    <div class="popup-body-empty">
        <iconify-icon icon="mdi:heart-outline"></iconify-icon>
        <p>Wishlist kosong</p>
        <p>Simpan produk favoritmu di sini!</p>
        <a href="{{ route('customer.products.index') }}" class="btn-primary" style="margin-top: 16px; display: inline-flex;">
            <iconify-icon icon="mdi:shopping-outline" width="18"></iconify-icon>
            Mulai Belanja
        </a>
    </div>
@else
    <div class="wishlist-items">
        @foreach ($products as $product)
            <div class="wishlist-item" data-product-id="{{ $product->id }}">
                {{-- Image --}}
                <div class="wishlist-item-image">
                    @if ($product->images->first())
                        <img src="{{ Storage::url($product->images->first()->image) }}" 
                             alt="{{ $product->name }}">
                    @else
                        <span class="placeholder">📦</span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="wishlist-item-info">
                    <a href="{{ route('customer.products.show', $product) }}" 
                       class="wishlist-item-name hover:text-blue-600" 
                       target="_blank">
                        {{ $product->name }}
                    </a>
                    @if ($product->category)
                        <p class="wishlist-item-category">{{ $product->category->name }}</p>
                    @endif
                    {{-- 🔥 TAMPILKAN HARGA ASLI (RANGE HARGA) --}}
                    @php
                        $minPrice = $product->variants->min('price');
                        $maxPrice = $product->variants->max('price');
                        
                        // Jika harga min dan max sama
                        if ($minPrice == $maxPrice) {
                            $priceDisplay = 'Rp ' . number_format($minPrice, 0, ',', '.');
                        } else {
                            $priceDisplay = 'Rp ' . number_format($minPrice, 0, ',', '.') . ' - Rp ' . number_format($maxPrice, 0, ',', '.');
                        }
                    @endphp
                    <p class="wishlist-item-price">{{ $priceDisplay }}</p>
                </div>

                {{-- Actions --}}
                <div class="wishlist-item-actions">
                    @if ($product->stock > 0)
                        <button class="btn-sm btn-sm-primary add-to-cart-wishlist" 
                                data-product-id="{{ $product->id }}"
                                data-variant-id="{{ $product->variants->first()?->id }}"
                                title="Tambah ke Keranjang">
                            <iconify-icon icon="mdi:cart-plus" width="16"></iconify-icon>
                        </button>
                    @endif
                    <button class="btn-sm btn-sm-danger remove-wishlist" 
                            data-product-id="{{ $product->id }}"
                            title="Hapus dari Wishlist">
                        <iconify-icon icon="mdi:heart-broken" width="16"></iconify-icon>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif