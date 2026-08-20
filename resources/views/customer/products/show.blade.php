@extends('layouts.customer')

@section('title', $product->name . ' - Barokah Sport')

@section('content')

<div class="product_show_layout">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb">
        <ul>
            <li><a href="{{ route('customer.home') }}">Beranda</a></li>
            <li>/</li>
            <li><a href="{{ route('customer.products.index') }}">Produk</a></li>
            <li>/</li>
            @if ($product->category)
                <li><a href="{{ route('customer.categories.show', $product->category) }}">{{ $product->category->name }}</a></li>
                <li>/</li>
            @endif
            <li>{{ $product->name }}</li>
        </ul>
    </nav>

    <div class="product_main_layout">

        {{-- ============================================ --}}
        {{-- LEFT COLUMN - GAMBAR & DESKRIPSI --}}
        {{-- ============================================ --}}
        <div class="product_first_container">

            {{-- GAMBAR --}}
            <div class="product_photo_grid">
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
                        </button>
                    @endforeach
                </div>

                <div class="main-image-container" id="main-image-container">
                    @php
                        $mainImage = $product->images->first();
                        $mainImageUrl = $mainImage ? Storage::url($mainImage->image) : null;
                    @endphp

                    @if ($mainImageUrl)
                        <img src="{{ $mainImageUrl }}"
                            alt="{{ $product->name }}"
                            id="main-image"
                            class="fade-in">
                        
                        <div class="magnifier-glass" id="magnifier-glass"></div>
                        
                        <div class="zoom-indicator">
                            <iconify-icon icon="mdi:magnify-plus-outline" width="16"></iconify-icon>
                            Hover untuk zoom
                        </div>
                    @else
                        <div class="placeholder">📦</div>
                    @endif
                </div>
            </div>

            <div class="product-accordion">
                <div class="accordion-item">
                    <button class="accordion-header" onclick="toggleAccordion(this)">
                        Deskripsi Produk
                        <span class="accordion-icon open">
                            <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                        </span>
                    </button>
                    <div class="accordion-body open">
                        <div class="accordion-body-inner product-description">
                            @if($product->description)
                                {!! $product->description !!}
                            @else
                                <p style="color: #94a3b8;">Tidak ada deskripsi untuk produk ini.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 2. DETAIL TEKNIS --}}
                <div class="accordion-item">
                    <button class="accordion-header" onclick="toggleAccordion(this)">
                        Detail Teknis
                        <span class="accordion-icon open">
                            <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                        </span>
                    </button>
                    <div class="accordion-body open">
                        <div class="accordion-body-inner">
                            <div class="detail-item">
                                <span class="label">Kategori</span>
                                <span>{{ $product->category->name ?? '-' }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Berat</span>
                                <span>{{ $firstVariant?->weight ?? '500' }} gram</span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Bahan</span>
                                <span>{{ $product->material ?? 'Poliester, Diadora' }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Jenis Kelamin</span>
                                <span>{{ $product->gender ? ucfirst($product->gender) : 'Unisex' }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Warna</span>
                                <span>
                                    @if(!empty($colors))
                                        {{ implode(', ', $colors) }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Ukuran</span>
                                <span>
                                    @if(!empty($sizes))
                                        {{ implode(', ', $sizes) }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. FITUR --}}
                <div class="accordion-item">
                    <button class="accordion-header" onclick="toggleAccordion(this)">
                        Fitur
                        <span class="accordion-icon open">
                            <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                        </span>
                    </button>
                    <div class="accordion-body open">
                        <div class="accordion-body-inner">
                            @php
                                $features = $product->features;
                            @endphp
                            @if($features->isNotEmpty())
                                <ul>
                                    @foreach($features as $feature)
                                        <li>
                                            • {{ $feature->name }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p style="color: #94a3b8;">Belum ada fitur untuk produk ini.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 4. ULASAN PEMBELI --}}
                <div class="accordion-item">
                    <button class="accordion-header" onclick="toggleAccordion(this)">
                        Ulasan Pembeli
                        <span class="accordion-icon open">
                            <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                        </span>
                    </button>
                    <div class="accordion-body open">
                        <div class="accordion-body-inner">
                            <p style="color: #94a3b8;">Belum ada ulasan untuk produk ini.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ============================================ --}}
        {{-- RIGHT COLUMN - INFO & PEMBELIAN --}}
        {{-- ============================================ --}}
        <div class="product_second_container">
            <div class="product_info_layout">

                {{-- Kategori --}}
                <span class="product_category_badge">{{ $product->category->name ?? 'Tanpa Kategori' }}</span>

                {{-- Nama Produk --}}
                <h1 class="product_name">{{ $product->name }}</h1>

                {{-- Harga --}}
                @php
                    $firstVariant = $product->variants->first();
                    $minPrice = $product->variants->min('price');
                    $maxPrice = $product->variants->max('price');
                    $minDiscount = $product->variants->min('discount_price');
                    $totalStock = $product->variants->sum('stock');
                @endphp

                <div class="product_price_display">
                    @if ($minDiscount && $minDiscount < $minPrice)
                        <span class="price-current discounted" id="display-price">
                            Rp {{ number_format($minDiscount, 0, ',', '.') }}
                        </span>
                        <span class="price-original">
                            Rp {{ number_format($minPrice, 0, ',', '.') }}
                        </span>
                    @else
                        @if ($minPrice != $maxPrice)
                            <span class="price-current" id="display-price">
                                Rp {{ number_format($minPrice, 0, ',', '.') }} - Rp {{ number_format($maxPrice, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="price-current" id="display-price">
                                Rp {{ number_format($minPrice, 0, ',', '.') }}
                            </span>
                        @endif
                    @endif
                </div>

                <div class="product_stock_status {{ $totalStock > 0 ? 'in-stock' : 'out-of-stock' }}" id="stock-display">
                    {{ $totalStock > 0 ? 'Stok: ' . $totalStock : 'Stok Habis' }}
                </div>

                {{-- ============================================ --}}
                {{-- VARIAN --}}
                {{-- ============================================ --}}
                @if ($product->options->isNotEmpty())
                    <div class="variant-section">
                        @foreach ($product->options as $option)
                            <div class="variant_choose_box">
                                <div class="variant-label-wrapper">
                                    <label class="variant-label">{{ $option->name }}</label>
                                    
                                    @if (strtolower($option->name) === 'ukuran' || strtolower($option->name) === 'size')
                                        <button type="button" class="size-guide-btn" onclick="openSizeGuide()">
                                            Panduan Ukuran
                                        </button>
                                    @endif
                                </div>
                                <div class="variant-options" data-option-id="{{ $option->id }}">
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
                                                class="variant-option {{ $loop->first && $hasStock ? 'active' : '' }}"
                                                data-option-id="{{ $option->id }}"
                                                data-option-name="{{ $option->name }}"
                                                data-value-id="{{ $value->id }}"
                                                data-value-name="{{ $value->value }}"
                                                data-image="{{ $value->image ? Storage::url($value->image) : '' }}"
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
                {{-- TOMBOL AKSI --}}
                {{-- ============================================ --}}
                <div class="action-buttons">
                    <form action="{{ route('customer.cart.add') }}" method="POST" id="add-to-cart-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="variant_id" id="selected-variant" value="{{ $firstVariant?->id }}">
                        <input type="hidden" name="variant_values" id="selected-variant-values" value="">

                        <div class="action-row">
                            <div class="quantity-wrapper">
                                <button type="button" class="qty-btn" data-action="decrease">−</button>
                                <input type="number" name="quantity" id="qty-input" value="1" min="1"
                                    max="{{ $totalStock > 0 ? $totalStock : 1 }}"
                                    class="qty-input">
                                <button type="button" class="qty-btn" data-action="increase">+</button>
                            </div>

                            <button type="submit"
                                    id="add-to-cart-btn"
                                    class="btn-add-to-cart"
                                    {{ $totalStock <= 0 ? 'disabled' : '' }}>
                                {{ $totalStock > 0 ? 'Tambah ke Keranjang' : 'Stok Habis' }}
                            </button>

                            <button type="button"
                                    id="wishlist-toggle-product"
                                    class="btn-wishlist"
                                    data-product-id="{{ $product->id }}"
                                    onclick="toggleWishlist({{ $product->id }})">
                                @if(in_array($product->id, array_keys(session()->get('wishlist', []))))
                                    ❤️
                                @else
                                    🤍
                                @endif
                            </button>
                        </div>

                        <button type="button"
                                id="buy-now-btn"
                                class="btn-buy-now"
                                {{ $totalStock <= 0 ? 'disabled' : '' }}
                                onclick="buyNow()">
                            {{ $totalStock > 0 ? 'Beli Sekarang' : 'Stok Habis' }}
                        </button>
                    </form>

                    @if ($totalStock <= 0)
                        <p style="margin-top: 0.5vw; font-size: 0.7vw; color: #ef4444;">Maaf, produk ini sedang habis.</p>
                    @endif
                </div>

                {{-- SHARE --}}
                <div class="share-section">
                    <span>Bagikan:</span>
                    <button onclick="shareProduct()" class="share-btn">🔗</button>
                </div>

            </div>
        </div>

    </div>

    <div id="size-guide-overlay" class="size-guide-overlay" onclick="closeSizeGuide()">
        <div class="size-guide-modal" onclick="event.stopPropagation()">
            <div class="size-guide-header">
                <h3>Panduan Ukuran</h3>
                <button type="button" class="size-guide-close" onclick="closeSizeGuide()">✕</button>
            </div>

            <div class="size-guide-body">
                <p class="size-guide-subtitle">Panduan ukuran dalam centimeter (cm).</p>

                <div class="size-guide-table-wrapper">
                    <table class="size-guide-table">
                        <thead>
                            <tr>
                                <th>Ukuran</th>
                                @php
                                    $sizeGuides = $product->category ? $product->category->sizeGuides : collect();
                                    $dimensionLabels = $product->category ? $product->category->dimension_labels : [];
                                @endphp
                                @foreach($dimensionLabels as $label)
                                    <th>{{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @if($sizeGuides->isNotEmpty())
                                @foreach($sizeGuides as $guide)
                                    <tr>
                                        <td><strong>{{ $guide->size }}</strong></td>
                                        @foreach($dimensionLabels as $label)
                                            <td>{{ $guide->dimensions[$label] ?? '-' }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @else
                                {{-- Fallback hardcode --}}
                                <tr><td><strong>S</strong></td><td>57</td><td>58</td><td>68</td></tr>
                                <tr><td><strong>M</strong></td><td>61</td><td>59</td><td>70</td></tr>
                                <tr><td><strong>L</strong></td><td>65</td><td>60</td><td>72</td></tr>
                                <tr><td><strong>XL</strong></td><td>69</td><td>61</td><td>74</td></tr>
                                <tr><td><strong>XXL</strong></td><td>73</td><td>62</td><td>76</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="size-guide-note">
                    <p><strong>Tips memilih ukuran:</strong></p>
                    <ul>
                        <li>Ukur menggunakan meteran kain pada posisi yang tepat</li>
                        <li>Pilih ukuran yang sesuai dengan ukuran tubuh Anda</li>
                        <li>Jika di antara dua ukuran, pilih ukuran yang lebih besar</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- RELATED PRODUCTS --}}
    {{-- ============================================ --}}
    @if ($relatedProducts->isNotEmpty())
        <section class="related-products">
            <h2>Produk Terkait</h2>
            <div class="related-grid">
                @foreach ($relatedProducts as $related)
                    <a href="{{ route('customer.products.show', $related) }}" class="related-item">
                        <div class="image-wrapper">
                            @if ($related->images->first())
                                <img src="{{ Storage::url($related->images->first()->image) }}"
                                     alt="{{ $related->name }}"
                                     loading="lazy">
                            @else
                                <span class="placeholder">📦</span>
                            @endif
                        </div>
                        <div class="related-name">{{ $related->name }}</div>
                        <div class="related-price">
                            @php
                                $relMinPrice = $related->variants->min('discount_price') ?? $related->variants->min('price');
                            @endphp
                            Rp {{ number_format($relMinPrice, 0, ',', '.') }}
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

</div>

{{-- ============================================ --}}
{{-- JAVASCRIPT --}}
{{-- ============================================ --}}
<script>
// ============================================
// JAVASCRIPT LENGKAP - PRODUCT DETAIL
// ============================================

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
    // FUNGSI BUKA POPUP
    // ============================================
    function openCartPopup() {
        if (typeof loadCartPopup === 'function') {
            loadCartPopup();
            const popup = document.getElementById('cart-popup');
            if (popup) {
                popup.classList.add('active');
                document.body.classList.add('popup-open');
            }
        }
    }

    function openWishlistPopup() {
        if (typeof loadWishlistPopup === 'function') {
            loadWishlistPopup();
            const popup = document.getElementById('wishlist-popup');
            if (popup) {
                popup.classList.add('active');
                document.body.classList.add('popup-open');
            }
        }
    }

    window.openCartPopup = openCartPopup;
    window.openWishlistPopup = openWishlistPopup;

    // ============================================
    // DETEKSI OPSI UKURAN
    // ============================================
    function detectSizeOption() {
        document.querySelectorAll('.variant-options[data-option-id]').forEach(function(group) {
            const optionLabel = group.closest('.variant-section')?.querySelector('.variant-label');
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

    function findVariantForSelectedOption(optionId, valueId) {
        const candidateValues = Object.assign({}, selectedValues, {
            [optionId]: parseInt(valueId)
        });

        const exactMatch = findVariantByValues(candidateValues);
        if (exactMatch) {
            return exactMatch;
        }

        const targetValueId = parseInt(valueId);
        const variantsWithValue = productVariants.filter(function(variant) {
            return variant.values.includes(targetValueId) && variant.stock > 0;
        });

        if (variantsWithValue.length === 0) {
            return productVariants.find(function(variant) {
                return variant.values.includes(targetValueId);
            }) || null;
        }

        const selectedIds = Object.values(candidateValues).map(Number);

        return variantsWithValue.find(function(variant) {
            const variantValueIds = variant.values.map(Number);
            return selectedIds.every(function(id) {
                return variantValueIds.includes(id);
            });
        }) || variantsWithValue[0];
    }

    function findVariantById(id) {
        return productVariants.find(function(v) { return v.id == id; });
    }

    function getOptionNameByValueId(valueId) {
        const btn = document.querySelector(`.variant-option[data-value-id="${valueId}"]`);
        if (btn) {
            return (btn.dataset.optionName || '').trim();
        }
        return '';
    }

    function getValueImageByValueId(valueId) {
        const btn = document.querySelector(`.variant-option[data-value-id="${valueId}"]`);
        if (btn) {
            return btn.dataset.image || '';
        }
        return '';
    }

    // ============================================
    // CHANGE MAIN IMAGE
    // ============================================
    window.changeMainImage = function(imageUrl, element) {
        if (!imageUrl || !mainImage) return;

        mainImage.src = imageUrl;
        mainImage.classList.remove('fade-in');
        void mainImage.offsetWidth;
        mainImage.classList.add('fade-in');

        // 🔥 RESET ZOOM SAAT GANTI GAMBAR
        const container = document.getElementById('main-image-container');
        const magnifier = document.getElementById('magnifier-glass');
        if (container) {
            container.classList.remove('zoomed');
        }
        if (magnifier) {
            magnifier.classList.remove('active');
            magnifier.style.backgroundImage = '';
        }

        document.querySelectorAll('.image-thumb').forEach(function(thumb) {
            thumb.classList.remove('active');
        });

        if (element) {
            element.classList.add('active');
        } else {
            document.querySelectorAll('.image-thumb').forEach(function(thumb) {
                if (thumb.dataset.image === imageUrl) {
                    thumb.classList.add('active');
                }
            });
        }
    };

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

                document.querySelectorAll('.variant-options[data-option-id]').forEach(function(group) {
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

    function selectVariant(variant) {
        if (!variant) return;

        currentVariantId = variant.id;
        selectedVariantInput.value = variant.id;

        var price = variant.discount_price ?? variant.price;
        displayPrice.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(price);

        stockDisplay.textContent = variant.stock > 0 ? 'Stok: ' + variant.stock : 'Stok Habis';
        stockDisplay.className = 'product_stock_status ' + (variant.stock > 0 ? 'in-stock' : 'out-of-stock');

        qtyInput.max = variant.stock > 0 ? variant.stock : 1;

        if (variant.stock > 0) {
            addToCartBtn.disabled = false;
            addToCartBtn.textContent = 'Tambah ke Keranjang';
            document.getElementById('buy-now-btn').disabled = false;
            document.getElementById('buy-now-btn').textContent = 'Beli Sekarang';
        } else {
            addToCartBtn.disabled = true;
            addToCartBtn.textContent = 'Stok Habis';
            document.getElementById('buy-now-btn').disabled = true;
            document.getElementById('buy-now-btn').textContent = 'Stok Habis';
        }

        updateVariantActiveState(variant);
        updateVariantImage(variant);
    }

    function updateVariantActiveState(variant) {
        if (!variant) return;

        const variantValueIds = variant.values.map(Number);

        document.querySelectorAll('.variant-option').forEach(function(btn) {
            const valueId = parseInt(btn.dataset.valueId);
            btn.classList.remove('active');

            if (variantValueIds.includes(valueId)) {
                btn.classList.add('active');
            }
        });
    }

    function updateAvailableVariants() {
        document.querySelectorAll('.variant-option').forEach(function(button) {
            var optionId = button.dataset.optionId;
            var valueId = button.dataset.valueId;

            var tempValues = Object.assign({}, selectedValues);
            tempValues[optionId] = parseInt(valueId);

            var variant = findVariantByValues(tempValues);
            var isAvailable = variant && variant.stock > 0;

            if (!isAvailable) {
                button.disabled = true;
                button.classList.add('disabled');
            } else {
                button.disabled = false;
                button.classList.remove('disabled');
            }
        });
    }

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

    function updateVariantImage(variant) {
        if (!variant) return;

        const variantValueIds = variant.values.map(Number);
        let colorImage = null;

        for (let i = 0; i < variantValueIds.length; i++) {
            const valueId = variantValueIds[i];
            const optionName = getOptionNameByValueId(valueId);
            const valueImage = getValueImageByValueId(valueId);

            if ((optionName || '').toLowerCase() === 'warna' || (optionName || '').toLowerCase() === 'color') {
                if (valueImage) {
                    colorImage = valueImage;
                    break;
                }
            }

            const thumb = document.querySelector(`.image-thumb[data-option-value-id="${valueId}"]`);
            if (thumb && thumb.dataset.image) {
                colorImage = thumb.dataset.image;
            }
        }

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
    // SHOW TOAST
    // ============================================
    function showToast(message, type = 'info') {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
            return;
        }

        const toast = document.createElement('div');
        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            background: ${colors[type] || colors.info};
            color: white;
            border-radius: 8px;
            font-size: 14px;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideInRight 0.3s ease;
            max-width: 400px;
        `;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(function() {
            toast.style.animation = 'slideOutRight 0.3s ease forwards';
            setTimeout(function() {
                toast.remove();
            }, 300);
        }, 3000);

        if (!document.getElementById('toast-styles')) {
            const style = document.createElement('style');
            style.id = 'toast-styles';
            style.textContent = `
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOutRight {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
    }

    // ============================================
    // BUY NOW
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
        btn.textContent = '⏳ Memproses...';

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
                btn.textContent = 'Beli Sekarang';
            }
        })
        .catch(() => {
            alert('Terjadi kesalahan. Silakan coba lagi.');
            btn.disabled = false;
            btn.textContent = 'Beli Sekarang';
        });
    };

    // ============================================
    // TOGGLE WISHLIST
    // ============================================
    window.toggleWishlist = function(productId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const btn = document.getElementById('wishlist-toggle-product');

        btn.disabled = true;
        btn.style.opacity = '0.5';

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
                if (data.in_wishlist) {
                    btn.classList.add('active');
                    btn.textContent = '❤️';
                    showToast('❤️ ' + data.message, 'success');
                    
                    // 🔥 BUKA POPUP WISHLIST
                    if (typeof loadWishlistPopup === 'function') {
                        setTimeout(function() {
                            loadWishlistPopup();
                            const popup = document.getElementById('wishlist-popup');
                            if (popup) {
                                popup.classList.add('active');
                                document.body.classList.add('popup-open');
                            }
                        }, 400);
                    }
                } else {
                    btn.classList.remove('active');
                    btn.textContent = '🤍';
                    showToast('💔 ' + data.message, 'info');
                }

                // 🔥 UPDATE WISHLIST COUNT
                const wishlistCount = document.getElementById('wishlist-count');
                if (wishlistCount) {
                    wishlistCount.textContent = data.count || 0;
                    wishlistCount.style.display = (data.count > 0) ? 'inline-flex' : 'none';
                }
                
                if (typeof window.updateNavbarWishlistCount === 'function') {
                    window.updateNavbarWishlistCount(data.count);
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
            btn.style.opacity = '1';
        });
    };

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
    };

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
    // HANDLE KLIK VARIAN
    // ============================================
    document.querySelectorAll('.variant-option').forEach(function(button) {
        button.addEventListener('click', function() {
            if (this.disabled) return;

            var optionId = this.dataset.optionId;
            var valueId = this.dataset.valueId;

            var group = this.closest('[data-option-id]');
            group.querySelectorAll('.variant-option').forEach(function(btn) {
                btn.classList.remove('active');
            });

            this.classList.add('active');

            selectedValues[optionId] = parseInt(valueId);

            var values = Object.values(selectedValues);
            variantValuesInput.value = JSON.stringify(values);

            var variant = findVariantForSelectedOption(optionId, valueId);

            if (variant) {
                selectVariant(variant);
                updateVariantImage(variant);
            }

            updateAvailableVariants();
        });
    });

    // ============================================
    // ADD TO CART - AJAX
    // ============================================
    document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const variantId = selectedVariantInput.value;
        const productId = {{ $product->id }};
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
        const btn = document.getElementById('add-to-cart-btn');

        btn.disabled = true;
        btn.textContent = '⏳ Memproses...';

        fetch('{{ route("customer.cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                variant_id: variantId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // 🔥 UPDATE CART COUNT
                const cartCountEl = document.getElementById('cart-count');
                if (cartCountEl) {
                    cartCountEl.textContent = data.count || 0;
                    cartCountEl.style.display = (data.count > 0) ? 'inline-flex' : 'none';
                }
                
                if (typeof window.updateNavbarCartCount === 'function') {
                    window.updateNavbarCartCount(data.count);
                }
                
                // 🔥 BUKA POPUP CART
                if (typeof loadCartPopup === 'function') {
                    setTimeout(function() {
                        loadCartPopup();
                        const popup = document.getElementById('cart-popup');
                        if (popup) {
                            popup.classList.add('active');
                            document.body.classList.add('popup-open');
                        }
                    }, 300);
                }
                
                showToast(data.message, 'success');
            } else {
                showToast(data.message || 'Gagal menambahkan ke keranjang', 'error');
            }
        })
        .catch(() => {
            showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Tambah ke Keranjang';
        });
    });

    // ============================================
    // CHECK WISHLIST STATUS
    // ============================================
    function checkWishlistStatus() {
        const productId = {{ $product->id }};
        const wishlistData = @json(session('wishlist', []));

        if (wishlistData[productId]) {
            const btn = document.getElementById('wishlist-toggle-product');
            if (btn) {
                btn.classList.add('active');
                btn.textContent = '❤️';
            }
        }
    }

    // ============================================
    // INITIALIZATION
    // ============================================
    function initVariantSelection() {
        var firstVariant = productVariants.find(function(v) { return v.stock > 0; });

        if (firstVariant) {
            var valueIds = firstVariant.values.map(Number);

            document.querySelectorAll('.variant-options[data-option-id]').forEach(function(group) {
                var optionButtons = group.querySelectorAll('.variant-option');
                optionButtons.forEach(function(btn) {
                    var valId = parseInt(btn.dataset.valueId);
                    if (valueIds.includes(valId)) {
                        selectedValues[group.dataset.optionId] = valId;
                    }
                });
            });

            variantValuesInput.value = JSON.stringify(Object.values(selectedValues));
            selectVariant(firstVariant);
            updateAvailableVariants();
        } else {
            document.querySelectorAll('.variant-options[data-option-id]').forEach(function(group) {
                var first = group.querySelector('.variant-option:not(:disabled)');
                if (first) {
                    first.click();
                }
            });
        }
    }

    window.toggleAccordion = function(button) {
        const item = button.closest('.accordion-item');
        const body = item ? item.querySelector('.accordion-body') : button.nextElementSibling;
        const icon = button.querySelector('.accordion-icon');

        if (!body) return;

        const isOpen = body.classList.contains('open');

        if (isOpen) {
            body.classList.remove('open');
            body.style.maxHeight = '0px';
            if (icon) {
                icon.classList.remove('open');
            }
        } else {
            body.classList.add('open');
            body.style.maxHeight = body.scrollHeight + 'px';
            if (icon) {
                icon.classList.add('open');
            }
        }
    };

    function initAccordionState() {
        document.querySelectorAll('.accordion-item').forEach(function(item) {
            const body = item.querySelector('.accordion-body');
            const icon = item.querySelector('.accordion-icon');

            if (!body) return;

            const isOpen = body.classList.contains('open');
            body.style.maxHeight = isOpen ? body.scrollHeight + 'px' : '0px';

            if (icon) {
                icon.classList.toggle('open', isOpen);
            }
        });
    }

    (function() {
        const container = document.getElementById('main-image-container');
        const image = document.getElementById('main-image');
        const magnifier = document.getElementById('magnifier-glass');

        // 🔥 CEK APAKAH ELEMEN TERSEDIA
        if (!container || !image || !magnifier) return;

        let isZoomed = false;
        container.addEventListener('mousemove', function(e) {
            if (!isZoomed) return;

            const rect = container.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const glassSize = magnifier.offsetWidth;
            const glassX = x - glassSize / 2;
            const glassY = y - glassSize / 2;

            const maxX = rect.width - glassSize;
            const maxY = rect.height - glassSize;
            
            magnifier.style.left = Math.max(0, Math.min(glassX, maxX)) + 'px';
            magnifier.style.top = Math.max(0, Math.min(glassY, maxY)) + 'px';

            const percentX = (x / rect.width) * 100;
            const percentY = (y / rect.height) * 100;

            magnifier.style.backgroundImage = `url('${image.src}')`;
            magnifier.style.backgroundPosition = `${percentX}% ${percentY}%`;
            magnifier.style.backgroundSize = `${rect.width * 2}px ${rect.height * 2}px`;
        });

        // ============================================
        // 🔥 MOUSE ENTER - AKTIFKAN ZOOM
        // ============================================
        container.addEventListener('mouseenter', function() {
            isZoomed = true;
            container.classList.add('zoomed');
            magnifier.classList.add('active');
        });

        // ============================================
        // 🔥 MOUSE LEAVE - NONAKTIFKAN ZOOM
        // ============================================
        container.addEventListener('mouseleave', function() {
            isZoomed = false;
            container.classList.remove('zoomed');
            magnifier.classList.remove('active');
            magnifier.style.backgroundImage = '';
        });

        // ============================================
        // 🔥 KLIK UNTUK ZOOM TOGGLE (ALTERNATIF)
        // ============================================
        container.addEventListener('click', function() {
            if (isZoomed) {
                // Jika sudah zoom, nonaktifkan
                isZoomed = false;
                container.classList.remove('zoomed');
                magnifier.classList.remove('active');
                magnifier.style.backgroundImage = '';
            } else {
                // Aktifkan zoom
                isZoomed = true;
                container.classList.add('zoomed');
                magnifier.classList.add('active');
                
                // Set posisi magnifier di tengah
                const rect = container.getBoundingClientRect();
                const glassSize = magnifier.offsetWidth;
                const centerX = (rect.width - glassSize) / 2;
                const centerY = (rect.height - glassSize) / 2;
                
                magnifier.style.left = centerX + 'px';
                magnifier.style.top = centerY + 'px';
                
                // Set background image
                magnifier.style.backgroundImage = `url('${image.src}')`;
                magnifier.style.backgroundSize = `${rect.width * 2}px ${rect.height * 2}px`;
                magnifier.style.backgroundPosition = `50% 50%`;
            }
        });

        // ============================================
        // 🔥 UPDATE SAAT GAMBAR BERUBAH
        // ============================================
        window.updateZoomImage = function(newImageUrl) {
            if (!newImageUrl) return;
            image.src = newImageUrl;
            
            // Reset zoom
            isZoomed = false;
            container.classList.remove('zoomed');
            magnifier.classList.remove('active');
            magnifier.style.backgroundImage = '';
        };

        // ============================================
        // 🔥 RESIZE - UPDATE MAGNIFIER SIZE
        // ============================================
        window.addEventListener('resize', function() {
            if (isZoomed) {
                const rect = container.getBoundingClientRect();
                magnifier.style.backgroundSize = `${rect.width * 2}px ${rect.height * 2}px`;
            }
        });

    })();

    document.addEventListener('DOMContentLoaded', function() {
        initAccordionState();
    });

    window.addEventListener('resize', function() {
        document.querySelectorAll('.accordion-body.open').forEach(function(body) {
            body.style.maxHeight = body.scrollHeight + 'px';
        });
    });

    setTimeout(initVariantSelection, 500);
    setTimeout(checkWishlistStatus, 400);

});

function openSizeGuide() {
    const overlay = document.getElementById('size-guide-overlay');
    if (overlay) {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Tutup popup panduan ukuran
 */
function closeSizeGuide() {
    const overlay = document.getElementById('size-guide-overlay');
    if (overlay) {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// 🔥 Tutup popup dengan tombol ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSizeGuide();
    }
});
</script>

@endsection