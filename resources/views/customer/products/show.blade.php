@extends('layouts.customer')

@section('title', 'Barokah Sport')

@section('content')

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .variant-option {
        transition: all 0.2s ease;
        cursor: pointer;
        border-color: #e2e8f0;
        background-color: #ffffff;
        color: #1e293b;
    }
    .variant-option:hover:not(.disabled):not(:disabled) {
        border-color: #3b82f6;
        background-color: #f8fafc;
    }
    .variant-option.active {
        border-color: #3b82f6;
        background-color: #eff6ff;
        box-shadow: 0 0 0 2px #3b82f6;
        color: #1e40af;
    }
    .variant-option.disabled,
    .variant-option:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #f1f5f9;
    }
    .qty-btn {
        transition: all 0.2s ease;
    }
    .qty-btn:hover {
        background-color: #f1f5f9;
    }
    .qty-btn:active {
        transform: scale(0.95);
    }
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
    .image-thumb {
        transition: all 0.2s ease;
        cursor: pointer;
        border: 2px solid transparent;
        border-radius: 12px;
        overflow: hidden;
        aspect-ratio: 1;
        background-color: #f1f5f9;
        position: relative;
    }
    .image-thumb:hover {
        border-color: #3b82f6;
    }
    .image-thumb.active {
        border-color: #3b82f6;
        border-width: 2px;
    }
    .image-thumb.active::after {
        content: '✓';
        position: absolute;
        bottom: 4px;
        right: 4px;
        background: #3b82f6;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    .image-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .main-image-container {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        background-color: #f1f5f9;
        height: 450px;
    }
    .main-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .thumbnail-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 8px;
        margin-top: 12px;
    }
    @media (max-width: 640px) {
        .thumbnail-grid {
            grid-template-columns: repeat(5, 1fr);
        }
    }
    .product-badge {
        position: absolute;
        top: 12px;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: white;
        z-index: 10;
    }
    .product-badge.sale {
        left: 12px;
        background-color: #ef4444;
    }
    .product-badge.sold-out {
        right: 12px;
        background-color: #ef4444;
    }
    .variant-image-indicator {
        position: absolute;
        bottom: 8px;
        left: 8px;
        background: rgba(0,0,0,0.7);
        color: white;
        font-size: 9px;
        padding: 2px 8px;
        border-radius: 4px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .image-thumb:hover .variant-image-indicator {
        opacity: 1;
    }
</style>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="mb-6 text-sm">
            <ol class="flex items-center gap-2 text-slate-500 flex-wrap">
                <li><a href="{{ route('customer.home') }}" class="hover:text-slate-700">Beranda</a></li>
                <li>/</li>
                <li><a href="{{ route('customer.products.index') }}" class="hover:text-slate-700">Produk</a></li>
                <li>/</li>
                @if ($product->category)
                    <li><a href="{{ route('customer.categories.show', $product->category) }}" class="hover:text-slate-700">{{ $product->category->name }}</a></li>
                    <li>/</li>
                @endif
                <li class="text-slate-900 font-medium">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="grid gap-8 lg:grid-cols-2">

            {{-- ============================================ --}}
            {{-- PRODUCT IMAGES --}}
            {{-- ============================================ --}}
            <div>
                {{-- Main Image --}}
                <div class="main-image-container">
                    @php
                        $mainImage = $product->images->first();
                        $mainImageUrl = $mainImage ? Storage::url($mainImage->image) : null;
                    @endphp

                    @if ($mainImageUrl)
                        <img src="{{ $mainImageUrl }}"
                             alt="{{ $product->name }}"
                             id="main-image"
                             class="fade-in">
                    @else
                        <div class="flex h-full items-center justify-center text-6xl text-slate-300">📦</div>
                    @endif

                    @if ($product->variants->min('discount_price') && $product->variants->min('discount_price') < $product->variants->min('price'))
                        <span class="product-badge sale">SALE!</span>
                    @endif
                    @if ($product->variants->sum('stock') <= 0)
                        <span class="product-badge sold-out">HABIS</span>
                    @endif
                </div>

                {{-- Thumbnail Images --}}
                <div class="thumbnail-grid" id="thumbnail-container">
                    @php
                        $allThumbnails = [];
                        $usedImages = [];

                        // 1. Product images
                        foreach ($product->images as $image) {
                            $url = Storage::url($image->image);
                            if (!in_array($url, $usedImages)) {
                                $usedImages[] = $url;
                                $allThumbnails[] = [
                                    'type' => 'product',
                                    'url' => $url,
                                    'variant_id' => null,
                                    'option_value_id' => null,
                                    'label' => 'Produk',
                                    'sort' => $image->sort_order ?? 0,
                                ];
                            }
                        }

                        // 2. Option value images (unique)
                        foreach ($product->options as $option) {
                            foreach ($option->values as $value) {
                                if ($value->image) {
                                    $url = Storage::url($value->image);
                                    if (!in_array($url, $usedImages)) {
                                        $usedImages[] = $url;
                                        $allThumbnails[] = [
                                            'type' => 'option',
                                            'url' => $url,
                                            'variant_id' => null,
                                            'option_value_id' => $value->id,
                                            'label' => $value->value,
                                            'sort' => 100 + ($value->sort_order ?? 0),
                                        ];
                                    }
                                }
                            }
                        }

                        usort($allThumbnails, function($a, $b) {
                            return $a['sort'] <=> $b['sort'];
                        });
                    @endphp

                    @foreach ($allThumbnails as $thumbnail)
                        <button type="button"
                                class="image-thumb {{ $loop->first ? 'active' : '' }}"
                                data-image="{{ $thumbnail['url'] }}"
                                data-type="{{ $thumbnail['type'] }}"
                                data-option-value-id="{{ $thumbnail['option_value_id'] }}"
                                onclick="handleThumbnailClick('{{ $thumbnail['url'] }}', {{ $thumbnail['option_value_id'] ?? 'null' }}, this)">
                            <img src="{{ $thumbnail['url'] }}"
                                alt="{{ $product->name }}"
                                loading="lazy">
                            @if ($thumbnail['type'] === 'option')
                                <span class="variant-image-indicator">{{ $thumbnail['label'] }}</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- PRODUCT INFO --}}
            {{-- ============================================ --}}
            <div>

                {{-- Category & Stock --}}
                <div class="mb-2 flex items-center gap-2 flex-wrap">
                    <span class="text-xs text-slate-400">{{ $product->category->name ?? 'Tanpa Kategori' }}</span>
                    @php $totalStock = $product->variants->sum('stock'); @endphp
                    @if ($totalStock > 0)
                        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Tersedia</span>
                    @else
                        <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">Habis</span>
                    @endif
                </div>

                {{-- Product Name --}}
                <h1 class="text-3xl font-bold text-slate-900">{{ $product->name }}</h1>

                {{-- Price --}}
                <div class="mt-4 flex items-end gap-3" id="price-display">
                    @php
                        $firstVariant = $product->variants->first();
                        $minPrice = $product->variants->min('price');
                        $maxPrice = $product->variants->max('price');
                        $minDiscount = $product->variants->min('discount_price');
                    @endphp

                    @if ($minDiscount && $minDiscount < $minPrice)
                        <span class="text-3xl font-bold text-red-500" id="display-price">
                            Rp {{ number_format($minDiscount, 0, ',', '.') }}
                        </span>
                        @if ($minPrice != $maxPrice)
                            <span class="text-sm text-slate-400 line-through">
                                Rp {{ number_format($minPrice, 0, ',', '.') }} - Rp {{ number_format($maxPrice, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-sm text-slate-400 line-through">
                                Rp {{ number_format($minPrice, 0, ',', '.') }}
                            </span>
                        @endif
                    @else
                        @if ($minPrice != $maxPrice)
                            <span class="text-3xl font-bold text-slate-900" id="display-price">
                                Rp {{ number_format($minPrice, 0, ',', '.') }} - Rp {{ number_format($maxPrice, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-3xl font-bold text-slate-900" id="display-price">
                                Rp {{ number_format($minPrice, 0, ',', '.') }}
                            </span>
                        @endif
                    @endif

                    <span class="text-xs text-slate-400" id="stock-display">Stok: {{ $totalStock }}</span>
                </div>

                {{-- Description --}}
                <div class="mt-6 border-t border-slate-200 pt-6">
                    <h3 class="font-semibold text-slate-900">Deskripsi Produk</h3>
                    <div class="mt-2 text-sm text-slate-600 whitespace-pre-line leading-relaxed">
                        {{ $product->description ?? 'Tidak ada deskripsi untuk produk ini.' }}
                    </div>
                </div>

                {{-- ============================================ --}}
                {{-- VARIANTS --}}
                {{-- ============================================ --}}
                @if ($product->options->isNotEmpty())
                <div class="mt-6 border-t border-slate-200 pt-6">
                    <h3 class="font-semibold text-slate-900 mb-4">Pilih Varian</h3>

                    @foreach ($product->options as $option)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2">{{ $option->name }}</label>
                            <div class="flex flex-wrap gap-2" data-option-id="{{ $option->id }}">
                                @php $values = $option->values->sortBy('sort_order'); @endphp
                                @foreach ($values as $value)
                                    @php
                                        $hasStock = false;
                                        foreach ($product->variants as $variant) {
                                            if ($variant->stock > 0) {
                                                foreach ($variant->variantValues as $vv) {
                                                    if ($vv->product_option_value_id == $value->id) {
                                                        $hasStock = true;
                                                        break 2;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    <button type="button"
                                            class="variant-option rounded-lg border-2 px-4 py-2 text-sm font-medium transition
                                                {{ $loop->first && $hasStock ? 'active' : '' }}
                                                {{ $hasStock ? 'border-slate-200 bg-white hover:border-blue-300' : 'border-slate-200 bg-slate-100 opacity-50 cursor-not-allowed' }}"
                                            data-option-id="{{ $option->id }}"
                                            data-value-id="{{ $value->id }}"
                                            data-value-name="{{ $value->value }}"
                                            {{ !$hasStock ? 'disabled' : '' }}>
                                        {{ $value->value }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

                {{-- ============================================ --}}
                {{-- ACTION BUTTONS --}}
                {{-- ============================================ --}}
                <div class="mt-8">
                    <form action="{{ route('customer.cart.add') }}" method="POST" id="add-to-cart-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="variant_id" id="selected-variant" value="{{ $firstVariant?->id }}">
                        <input type="hidden" name="variant_values" id="selected-variant-values" value="">

                        <div class="flex flex-col gap-4">
                            {{-- Quantity & Add to Cart --}}
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                                <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden flex-shrink-0">
                                    <button type="button" class="qty-btn px-4 py-3 text-slate-600 hover:bg-slate-100" data-action="decrease">−</button>
                                    <input type="number" name="quantity" id="qty-input" value="1" min="1"
                                        max="{{ $totalStock > 0 ? $totalStock : 1 }}"
                                        class="w-16 text-center border-0 py-3 text-sm focus:ring-0">
                                    <button type="button" class="qty-btn px-4 py-3 text-slate-600 hover:bg-slate-100" data-action="increase">+</button>
                                </div>

                                <button type="submit"
                                        id="add-to-cart-btn"
                                        class="flex-1 rounded-xl bg-slate-900 px-6 py-3 font-semibold text-white transition hover:bg-slate-800 disabled:bg-slate-400 disabled:cursor-not-allowed"
                                        {{ $totalStock <= 0 ? 'disabled' : '' }}>
                                    {{ $totalStock > 0 ? '🛒 Tambah ke Keranjang' : 'Stok Habis' }}
                                </button>

                                {{-- Wishlist Button --}}
                                <button type="button"
                                        id="wishlist-toggle-product"
                                        class="rounded-xl border border-slate-200 px-4 py-3 font-semibold text-slate-700 transition hover:bg-slate-50 hover:border-red-300 flex-shrink-0"
                                        data-product-id="{{ $product->id }}"
                                        onclick="toggleWishlist({{ $product->id }})">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- 🔥 BUY NOW BUTTON --}}
                            <button type="button"
                                    id="buy-now-btn"
                                    class="w-full rounded-xl bg-blue-600 px-6 py-3.5 font-semibold text-white transition hover:bg-blue-700 disabled:bg-slate-400 disabled:cursor-not-allowed"
                                    {{ $totalStock <= 0 ? 'disabled' : '' }}
                                    onclick="buyNow()">
                                {{ $totalStock > 0 ? '⚡ Beli Sekarang' : 'Stok Habis' }}
                            </button>
                        </div>
                    </form>

                    @if ($totalStock <= 0)
                        <p class="mt-2 text-sm text-red-500">Maaf, produk ini sedang habis.</p>
                    @endif
                </div>

                {{-- Product Meta --}}
                <div class="mt-6 border-t border-slate-200 pt-6 text-xs text-slate-400 grid grid-cols-2 gap-1">
                    <p><span class="font-medium text-slate-600">SKU:</span> {{ $firstVariant?->sku ?? $product->sku ?? '-' }}</p>
                    <p><span class="font-medium text-slate-600">Kategori:</span> {{ $product->category->name ?? '-' }}</p>
                    <p><span class="font-medium text-slate-600">Berat:</span> {{ $firstVariant?->weight ?? '-' }} gram</p>
                    <p><span class="font-medium text-slate-600">Status:</span> {{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}</p>
                </div>

                {{-- Share --}}
                <div class="mt-4 flex items-center gap-3 text-xs text-slate-400">
                    <span>Bagikan:</span>
                    <button onclick="shareProduct()" class="text-slate-500 hover:text-blue-500">🔗</button>
                </div>
            </div>

        </div>

        {{-- ============================================ --}}
        {{-- RELATED PRODUCTS --}}
        {{-- ============================================ --}}
        @if ($relatedProducts->isNotEmpty())
            <section class="mt-16">
                <h2 class="text-2xl font-bold text-slate-900">Produk Terkait</h2>
                <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($relatedProducts as $related)
                        <div class="group rounded-2xl border border-slate-200 bg-white p-3 transition hover:shadow-lg">
                            <a href="{{ route('customer.products.show', $related) }}" class="block">
                                <div class="aspect-square overflow-hidden rounded-xl bg-slate-100">
                                    @if ($related->images->first())
                                        <img src="{{ Storage::url($related->images->first()->image) }}"
                                             alt="{{ $related->name }}"
                                             loading="lazy"
                                             class="h-full w-full object-cover transition group-hover:scale-105">
                                    @else
                                        <div class="flex h-full items-center justify-center text-4xl text-slate-300">📦</div>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <h3 class="text-sm font-semibold text-slate-900 line-clamp-1">{{ $related->name }}</h3>
                                    <div class="mt-1 flex items-center justify-between">
                                        @php
                                            $relMinPrice = $related->variants->min('discount_price') ?? $related->variants->min('price');
                                        @endphp
                                        <span class="font-bold text-slate-900">Rp {{ number_format($relMinPrice, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const productVariants = @json($variantData ?? []);
            const productImages = @json($product->images);

            const selectedVariantInput = document.getElementById('selected-variant');
            const variantValuesInput = document.getElementById('selected-variant-values');
            const displayPrice = document.getElementById('display-price');
            const stockDisplay = document.getElementById('stock-display');
            const addToCartBtn = document.getElementById('add-to-cart-btn');
            const qtyInput = document.getElementById('qty-input');
            const mainImage = document.getElementById('main-image');

            let selectedValues = {};
            let currentVariantId = null;

            // ============================================
            // 🔥 DETEKSI OPSI UKURAN
            // ============================================
            function detectSizeOption() {
                document.querySelectorAll('[data-option-id]').forEach(function(group) {
                    const optionLabel = group.closest('.mb-4')?.querySelector('label');
                    if (optionLabel) {
                        const labelText = optionLabel.textContent.toLowerCase();
                        if (labelText === 'ukuran' || labelText === 'size') {
                            group.dataset.isSize = 'true';
                        }
                    }
                });
            }
            detectSizeOption();

            // ============================================
            // FIND VARIANT BY VALUES
            // ============================================
            function findVariantByValues(values) {
                const selectedValueIds = Object.values(values).map(Number).sort();

                return productVariants.find(function(variant) {
                    const variantValueIds = variant.values.map(Number).sort();
                    return JSON.stringify(variantValueIds) === JSON.stringify(selectedValueIds);
                });
            }

            // ============================================
            // FIND VARIANT BY ID
            // ============================================
            function findVariantById(id) {
                return productVariants.find(function(v) { return v.id == id; });
            }

            // ============================================
            // GET OPTION NAME BY VALUE ID
            // ============================================
            function getOptionNameByValueId(valueId) {
                const btn = document.querySelector(`.variant-option[data-value-id="${valueId}"]`);
                if (btn) {
                    const group = btn.closest('[data-option-id]');
                    const label = group?.closest('.mb-4')?.querySelector('label');
                    return label ? label.textContent : '';
                }
                return '';
            }

            function checkWishlistStatus() {
                const productId = {{ $product->id }};
                const wishlistData = @json(session('wishlist', []));
                
                if (wishlistData[productId]) {
                    const btn = document.getElementById('wishlist-toggle-product');
                    if (btn) {
                        btn.classList.add('text-red-500', 'border-red-300');
                        btn.classList.remove('text-slate-700');
                        const svg = btn.querySelector('svg');
                        if (svg) {
                            svg.setAttribute('fill', 'currentColor');
                        }
                    }
                }
            }

            // ============================================
            // CHANGE MAIN IMAGE
            // ============================================
            window.changeMainImage = function(imageUrl, element) {
                if (!imageUrl) return;

                if (mainImage) {
                    mainImage.src = imageUrl;
                    mainImage.classList.remove('fade-in');
                    void mainImage.offsetWidth;
                    mainImage.classList.add('fade-in');
                }

                if (element) {
                    document.querySelectorAll('.image-thumb').forEach(function(thumb) {
                        thumb.classList.remove('active');
                    });
                    element.classList.add('active');
                } else {
                    document.querySelectorAll('.image-thumb').forEach(function(thumb) {
                        if (thumb.dataset.image === imageUrl) {
                            thumb.classList.add('active');
                        } else {
                            thumb.classList.remove('active');
                        }
                    });
                }
            }

            // ============================================
            // BUY NOW FUNCTION
            // ============================================
            window.buyNow = function() {
                const variantId = selectedVariantInput.value;
                const quantity = parseInt(qtyInput.value) || 1;

                if (!variantId) {
                    alert('Pilih varian produk terlebih dahulu!');
                    return;
                }

                const variant = productVariants.find(function(v) { return v.id == variantId; });
                if (variant && quantity > variant.stock) {
                    alert('Stok tidak mencukupi! Stok tersedia: ' + variant.stock);
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const btn = document.getElementById('buy-now-btn');
                
                btn.disabled = true;
                btn.textContent = 'Memproses...';
                btn.classList.add('opacity-50');

                fetch('{{ route("customer.buy-now") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: {{ $product->id }},
                        variant_id: variantId,
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect || '{{ route("customer.checkout.index") }}';
                    } else {
                        alert(data.message || 'Gagal memproses pesanan');
                        btn.disabled = false;
                        btn.textContent = '⚡ Beli Sekarang';
                        btn.classList.remove('opacity-50');
                    }
                })
                .catch(() => {
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                    btn.disabled = false;
                    btn.textContent = '⚡ Beli Sekarang';
                    btn.classList.remove('opacity-50');
                });
            };

            window.toggleWishlist = function(productId) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const btn = document.getElementById('wishlist-toggle-product');
                
                // Toggle loading state
                btn.disabled = true;
                btn.classList.add('opacity-50');
                
                fetch('{{ route("customer.wishlist.add") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI
                        if (data.in_wishlist) {
                            btn.classList.add('text-red-500', 'border-red-300');
                            btn.classList.remove('text-slate-700');
                            const svg = btn.querySelector('svg');
                            if (svg) {
                                svg.setAttribute('fill', 'currentColor');
                            }
                            showToast('❤️ ' + data.message, 'success');
                        } else {
                            btn.classList.remove('text-red-500', 'border-red-300');
                            btn.classList.add('text-slate-700');
                            const svg = btn.querySelector('svg');
                            if (svg) {
                                svg.removeAttribute('fill');
                            }
                            showToast('💔 ' + data.message, 'info');
                        }
                        
                        // Update wishlist count di navbar
                        const wishlistCount = document.getElementById('wishlist-count');
                        if (wishlistCount) {
                            wishlistCount.textContent = data.count || 0;
                        }
                        
                        // 🔥 BUKA POPUP WISHLIST JIKA PRODUK DITAMBAHKAN
                        if (data.in_wishlist && typeof openWishlistPopup === 'function') {
                            setTimeout(function() {
                                openWishlistPopup();
                            }, 500);
                        }
                    } else {
                        showToast(data.message || 'Gagal menambahkan ke wishlist', 'error');
                    }
                })
                .catch(() => {
                    showToast('Terjadi kesalahan', 'error');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.classList.remove('opacity-50');
                });
            }

            setTimeout(checkWishlistStatus, 400);

            // ============================================
            // CARI GAMBAR UNTUK WARNA
            // ============================================
            function findColorImage(variant) {
                if (!variant) return null;

                const variantValueIds = variant.values.map(Number);
                
                for (let i = 0; i < variantValueIds.length; i++) {
                    const valueId = variantValueIds[i];
                    const optionName = getOptionNameByValueId(valueId);
                    
                    if (optionName.toLowerCase() === 'warna' || optionName.toLowerCase() === 'color') {
                        const thumb = document.querySelector(`.image-thumb[data-option-value-id="${valueId}"]`);
                        if (thumb) {
                            return thumb.dataset.image;
                        }
                    }
                }

                return null;
            }

            // ============================================
            // TAMPILKAN GAMBAR BERDASARKAN VARIAN
            // ============================================
            function updateVariantImage(variant) {
                if (!variant) return;

                const colorImage = findColorImage(variant);
                if (colorImage) {
                    changeMainImage(colorImage);
                    return;
                }

                if (variant.image) {
                    changeMainImage(variant.image);
                    return;
                }

                const firstImage = productImages.length > 0 ? 
                    '{{ $product->images->first() ? Storage::url($product->images->first()->image) : '' }}' : null;
                if (firstImage) {
                    changeMainImage(firstImage);
                }
            }

            // ============================================
            // 🔥 UPDATE ACTIVE STATE VARIAN
            // ============================================
            function updateVariantActiveState(variant) {
                if (!variant) return;

                const variantValueIds = variant.values.map(Number);

                document.querySelectorAll('.variant-option').forEach(function(btn) {
                    const valueId = parseInt(btn.dataset.valueId);
                    
                    // 🔥 HAPUS SEMUA CLASS ACTIVE TERLEBIH DAHULU
                    btn.classList.remove('active');
                    
                    // 🔥 TAMBAHKAN CLASS ACTIVE HANYA JIKA VALUE ADA DI VARIAN
                    if (variantValueIds.includes(valueId)) {
                        btn.classList.add('active');
                        btn.classList.remove('border-slate-200', 'bg-white');
                    } else {
                        btn.classList.add('border-slate-200', 'bg-white');
                    }
                });
            }

            // ============================================
            // SELECT VARIANT AND UPDATE UI
            // ============================================
            function selectVariant(variant) {
                if (!variant) return;

                currentVariantId = variant.id;
                selectedVariantInput.value = variant.id;

                // Update price
                var price = variant.discount_price ?? variant.price;
                displayPrice.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(price);

                // Update stock
                stockDisplay.textContent = 'Stok: ' + variant.stock;
                qtyInput.max = variant.stock > 0 ? variant.stock : 1;

                // Update button
                if (variant.stock > 0) {
                    addToCartBtn.disabled = false;
                    addToCartBtn.textContent = 'Tambah ke Keranjang';
                } else {
                    addToCartBtn.disabled = true;
                    addToCartBtn.textContent = 'Stok Habis';
                }

                // 🔥 UPDATE ACTIVE STATE SEMUA VARIAN
                updateVariantActiveState(variant);
            }

            // ============================================
            // HANDLE THUMBNAIL CLICK
            // ============================================
            window.handleThumbnailClick = function(imageUrl, optionValueId, element) {
                changeMainImage(imageUrl, element);

                if (optionValueId) {
                    var foundVariant = null;
                    for (var i = 0; i < productVariants.length; i++) {
                        var variant = productVariants[i];
                        if (variant.values.includes(parseInt(optionValueId))) {
                            foundVariant = variant;
                            break;
                        }
                    }

                    if (foundVariant) {
                        var variantValues = {};
                        var valueIds = foundVariant.values;

                        document.querySelectorAll('[data-option-id]').forEach(function(group) {
                            var optionId = group.dataset.optionId;
                            var optionButtons = group.querySelectorAll('.variant-option');
                            optionButtons.forEach(function(btn) {
                                var valId = parseInt(btn.dataset.valueId);
                                if (valueIds.includes(valId)) {
                                    variantValues[optionId] = valId;
                                }
                            });
                        });

                        selectedValues = variantValues;
                        variantValuesInput.value = JSON.stringify(Object.values(variantValues));
                        selectVariant(foundVariant);
                        updateAvailableVariants();
                    }
                }
            }

            // ============================================
            // UPDATE UI BERDASARKAN VARIAN TERPILIH
            // ============================================
            function updateVariantUI(variant) {
                if (!variant) {
                    displayPrice.textContent = 'Pilih varian';
                    stockDisplay.textContent = 'Stok: 0';
                    addToCartBtn.disabled = true;
                    addToCartBtn.textContent = 'Pilih Varian';
                    selectedVariantInput.value = '';
                    return;
                }

                selectVariant(variant);
            }

            // ============================================
            // CEK VARIAN YANG TERSEDIA
            // ============================================
            function updateAvailableVariants() {
                var optionButtons = document.querySelectorAll('.variant-option');

                optionButtons.forEach(function(button) {
                    var optionId = button.dataset.optionId;
                    var valueId = button.dataset.valueId;

                    var tempValues = Object.assign({}, selectedValues);
                    tempValues[optionId] = parseInt(valueId);

                    var variant = findVariantByValues(tempValues);
                    var isAvailable = variant && variant.stock > 0;

                    if (!isAvailable) {
                        button.classList.add('disabled');
                        button.disabled = true;
                    } else {
                        button.classList.remove('disabled');
                        button.disabled = false;
                    }
                });
            }

            // ============================================
            // 🔥 HANDLE KLIK VARIAN
            // ============================================
            document.querySelectorAll('.variant-option').forEach(function(button) {
                button.addEventListener('click', function() {
                    if (this.disabled) return;

                    var optionId = this.dataset.optionId;
                    var valueId = this.dataset.valueId;

                    // 🔥 HAPUS SEMUA CLASS ACTIVE DI GROUP INI
                    var group = this.closest('[data-option-id]');
                    group.querySelectorAll('.variant-option').forEach(function(btn) {
                        btn.classList.remove('active');
                    });

                    // 🔥 TAMBAHKAN CLASS ACTIVE KE BUTTON YANG DIKLIK
                    this.classList.add('active');
                    this.classList.remove('border-slate-200', 'bg-white');

                    // Simpan pilihan
                    selectedValues[optionId] = parseInt(valueId);

                    // Update variant values input
                    var values = Object.values(selectedValues);
                    variantValuesInput.value = JSON.stringify(values);

                    // Cari varian
                    var variant = findVariantByValues(selectedValues);
                    
                    if (variant) {
                        // 🔥 UPDATE SEMUA UI
                        selectVariant(variant);
                        
                        // 🔥 UPDATE GAMBAR HANYA JIKA BUKAN UKURAN
                        var isSize = group.dataset.isSize === 'true';
                        if (!isSize) {
                            updateVariantImage(variant);
                        }
                    }

                    updateAvailableVariants();
                });
            });

            // ============================================
            // QUANTITY BUTTONS
            // ============================================
            document.querySelectorAll('.qty-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    var value = parseInt(qtyInput.value) || 1;
                    var max = parseInt(qtyInput.max) || 999;

                    if (this.dataset.action === 'increase' && value < max) {
                        value += 1;
                    } else if (this.dataset.action === 'decrease' && value > 1) {
                        value -= 1;
                    }
                    qtyInput.value = value;
                });
            });

            // ============================================
            // SHARE PRODUCT
            // ============================================
            window.shareProduct = function() {
                var url = window.location.href;
                var title = '{{ $product->name }}';

                if (navigator.share) {
                    navigator.share({ title: title, url: url }).catch(function() {});
                } else {
                    navigator.clipboard.writeText(url).then(function() {
                        alert('Link produk telah disalin!');
                    }).catch(function() {
                        prompt('Salin link ini:', url);
                    });
                }
            }

            // ============================================
            // VALIDASI ADD TO CART
            // ============================================
            document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
                var variantId = selectedVariantInput.value;
                if (!variantId) {
                    e.preventDefault();
                    alert('Pilih varian produk terlebih dahulu!');
                    return;
                }

                var quantity = parseInt(qtyInput.value) || 1;
                var variant = productVariants.find(function(v) { return v.id == variantId; });
                if (variant && quantity > variant.stock) {
                    e.preventDefault();
                    alert('Stok tidak mencukupi! Stok tersedia: ' + variant.stock);
                    return;
                }
            });

            // ============================================
            // INISIALISASI
            // ============================================
            function initVariantSelection() {
                // Cari variant pertama yang tersedia
                var firstVariant = productVariants.find(function(v) { return v.stock > 0; });
                
                if (firstVariant) {
                    var valueIds = firstVariant.values.map(Number);
                    
                    // Set selected values
                    document.querySelectorAll('[data-option-id]').forEach(function(group) {
                        var optionButtons = group.querySelectorAll('.variant-option');
                        optionButtons.forEach(function(btn) {
                            var valId = parseInt(btn.dataset.valueId);
                            if (valueIds.includes(valId)) {
                                selectedValues[group.dataset.optionId] = valId;
                            }
                        });
                    });

                    variantValuesInput.value = JSON.stringify(Object.values(selectedValues));
                    
                    // 🔥 SELECT VARIANT (INI AKAN UPDATE ACTIVE STATE)
                    selectVariant(firstVariant);
                    
                    // Set gambar
                    updateVariantImage(firstVariant);
                    
                    updateAvailableVariants();
                } else {
                    // Jika tidak ada stok, pilih yang pertama
                    document.querySelectorAll('[data-option-id]').forEach(function(group) {
                        var first = group.querySelector('.variant-option');
                        if (first) {
                            first.click();
                        }
                    });
                }
            }

            setTimeout(initVariantSelection, 300);

        });
    </script>

    @endsection