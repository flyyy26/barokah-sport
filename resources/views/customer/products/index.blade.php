@extends('layouts.customer')

@section('title', 'Katalog Produk - Barokah Sport')

@section('content')

<style>
    /* ============================================
       CATALOG CONTAINER
       ============================================ */
    .catalog-container {
        width: 100%;
        margin: 0 auto;
        border-top: 0.1vw solid #076694;
    }

    .catalog-header {
        margin-bottom: 1.5vw;
    }

    .catalog-header h1 {
        font-size: 2.3vw;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        font-family: heading, sans-serif;
        text-transform: uppercase;
    }

    .catalog-header p {
        font-size: 0.85vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }

    /* ============================================
       FILTER ROW
       ============================================ */
    .katalog_top_container {
        width: 100%;
        padding: 1.3vw 7.54vw;
        background: #f9fafb;
    }

    .filter-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.8vw;
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.8vw;
        padding: 0.8vw 1.2vw;
    }

    .filter-row .filter-label {
        font-size: 0.75vw;
        font-weight: 600;
        color: #475569;
        margin-right: 0.2vw;
        white-space: nowrap;
    }

    /* ============================================
       CUSTOM SELECT
       ============================================ */
    .custom-select-wrapper {
        position: relative;
        display: inline-block;
        min-width: 8vw;
    }

    .custom-select-wrapper .custom-select-trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.4vw .8vw 0.4vw 0.8vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.5vw;
        font-size: 0.75vw;
        color: #0f172a;
        background: #f8fafc;
        cursor: pointer;
        transition: border-color 0.2s;
        user-select: none;
        min-height: 2.2vw;
        gap: 0.5vw;
    }

    .custom-select-wrapper .custom-select-trigger:hover {
        border-color: #94a3b8;
    }

    .custom-select-wrapper .custom-select-trigger.open {
        border-color: #076694;
        box-shadow: 0 0 0 0.15vw rgba(7, 102, 148, 0.2);
    }

    .custom-select-wrapper .custom-select-trigger .trigger-text {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .custom-select-wrapper .custom-select-trigger .trigger-arrow {
        font-size: 0.6vw;
        color: #94a3b8;
        transition: transform 0.3s ease;
        flex-shrink: 0;
    }

    .custom-select-wrapper .custom-select-trigger.open .trigger-arrow {
        transform: rotate(180deg);
    }

    .custom-select-wrapper .custom-select-dropdown {
        position: absolute;
        top: calc(100% + 0.2vw);
        left: 0;
        right: 0;
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.5vw;
        box-shadow: 0 0.5vw 2vw rgba(0, 0, 0, 0.1);
        max-height: 20vw;
        overflow-y: auto;
        z-index: 999;
        display: none;
        min-width: 12vw;
    }

    .custom-select-wrapper .custom-select-dropdown.open {
        display: block;
    }

    .custom-select-wrapper .custom-select-dropdown::-webkit-scrollbar {
        width: 0.2vw;
    }

    .custom-select-wrapper .custom-select-dropdown::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 0.2vw;
    }

    .custom-select-wrapper .custom-select-dropdown::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 0.2vw;
    }

    .custom-select-wrapper .custom-select-dropdown .dropdown-item {
        padding: 0.4vw 1vw;
        font-size: 0.75vw;
        color: #0f172a;
        cursor: pointer;
        transition: background 0.15s;
        display: flex;
        align-items: center;
        gap: 0.4vw;
    }

    .custom-select-wrapper .custom-select-dropdown .dropdown-item:hover {
        background: #f1f5f9;
    }

    .custom-select-wrapper .custom-select-dropdown .dropdown-item.active {
        background: #eff6ff;
        color: #076694;
        font-weight: 600;
    }

    .custom-select-wrapper .custom-select-dropdown .dropdown-item .check-icon {
        margin-left: auto;
        color: #076694;
        font-size: 0.7vw;
        opacity: 0;
    }

    .custom-select-wrapper .custom-select-dropdown .dropdown-item.active .check-icon {
        opacity: 1;
    }

    .custom-select-wrapper .custom-select-dropdown .dropdown-divider {
        height: 0.05vw;
        background: #e2e8f0;
        margin: 0.2vw 0;
    }

    /* ============================================
       FILTER DIVIDER
       ============================================ */
    .filter-divider {
        width: 0.05vw;
        height: 1.8vw;
        background: #e2e8f0;
        flex-shrink: 0;
    }

    .filter-reset {
        font-size: 0.7vw;
        color: #ef4444;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
        white-space: nowrap;
    }

    .filter-reset:hover {
        color: #dc2626;
        text-decoration: underline;
    }

    .filter-count {
        font-size: 0.75vw;
        color: #94a3b8;
        margin-left: auto;
        white-space: nowrap;
    }

    /* ============================================
       PRODUCT GRID
       ============================================ */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        grid-gap:2.3vw;
        padding: 2vw 7.54vw;
        padding-bottom:5vw;
    }

    /* ============================================
       PRODUCT CARD
       ============================================ */
    .product-card {
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.8vw;
        padding: 0.8vw;
        transition: all 0.3s ease;
        position: relative;
    }

    .product-card:hover {
        box-shadow: 0 0.3vw 1.5vw rgba(0, 0, 0, 0.08);
        transform: translateY(-0.2vw);
    }

    .product-card .product-image {
        aspect-ratio: 1;
        overflow: hidden;
        border-radius: 0.5vw;
        background: #f8fafc;
        position: relative;
    }

    .product-card .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .product_badge{
        position: absolute;
        right: .6vw;
        top: .6vw;
        z-index: 2;
        font-size: .65vw;
        background-color: #DE161F;
        color: white;
        padding: .3vw .7vw;
        border-radius: 100vw;
    }

    .product-card:hover .product-image img {
        transform: scale(1.05);
    }

    .product-card .product-image .placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-size: 3vw;
        color: #cbd5e1;
    }

    .product-card .product-badge {
        position: absolute;
        top: 0.5vw;
        left: 0.5vw;
        padding: 0.15vw 0.6vw;
        border-radius: 0.3vw;
        font-size: 0.55vw;
        font-weight: 700;
        text-transform: uppercase;
        color: #ffffff;
        z-index: 2;
    }

    .product-card .product-badge.discount {
        background: #ef4444;
    }

    .product-card .product-badge.featured {
        background: #f59e0b;
    }

    .product-card .product-badge.best-seller {
        background: #8b5cf6;
    }

    .product-card .product-content {
        padding-top: 0.6vw;
    }

    .product-card .product-category {
        font-size: 0.6vw;
        color: #94a3b8;
        margin-bottom: 0.1vw;
    }

    .product-card .product-name {
        font-size: 0.85vw;
        font-weight: 600;
        color: #0f172a;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-card .product-price {
        display: flex;
        align-items: center;
        gap: 0.4vw;
        flex-wrap: wrap;
        margin-top: 0.3vw;
    }

    .product-card .product-price .price-current {
        font-size: 0.9vw;
        font-weight: 700;
        color: #076694;
    }

    .product-card .product-price .price-current.discounted {
        color: #ef4444;
    }

    .product-card .product-price .price-original {
        font-size: 0.65vw;
        color: #94a3b8;
        text-decoration: line-through;
    }

    .product-card .product-actions {
        display: flex;
        gap: 0.3vw;
        margin-top: 0.5vw;
        padding-top: 0.5vw;
        border-top: 0.05vw solid #f1f5f9;
    }

    .product-card .product-actions .btn-buy {
        flex: 1;
        padding: 0.4vw 0.8vw;
        border-radius: 0.4vw;
        border: none;
        background: #076694;
        color: #ffffff;
        font-size: 0.6vw;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-transform: uppercase;
        letter-spacing: 0.05vw;
    }

    .product-card .product-actions .btn-buy:hover {
        background: #055a7a;
    }

    .product-card .product-actions .btn-cart {
        padding: 0.4vw 0.6vw;
        border-radius: 0.4vw;
        border: 0.1vw solid #e2e8f0;
        background: #ffffff;
        color: #0f172a;
        font-size: 0.8vw;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-card .product-actions .btn-cart:hover {
        border-color: #076694;
        background: #f0f9ff;
    }

    .product-card .product-actions .btn-wishlist {
        padding: 0.4vw 0.6vw;
        border-radius: 0.4vw;
        border: 0.1vw solid #e2e8f0;
        background: #ffffff;
        color: #94a3b8;
        font-size: 0.8vw;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-card .product-actions .btn-wishlist:hover {
        border-color: #ef4444;
        background: #fef2f2;
        color: #ef4444;
    }

    .product-card .product-actions .btn-wishlist.active {
        color: #ef4444;
        border-color: #ef4444;
    }

    /* ============================================
       PAGINATION
       ============================================ */
    .pagination-wrapper {
        margin-top: 2vw;
        display: flex;
        justify-content: center;
        padding-bottom: 2vw;
    }

    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4vw 0;
        color: #94a3b8;
    }

    .empty-state .empty-icon {
        font-size: 4vw;
        margin-bottom: 1vw;
    }

    .empty-state p {
        font-size: 1.2vw;
    }

    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 1200px) {
        .product-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 1024px) {
        .katalog_top_container {
            padding: 1.5vw 3vw;
        }

        .filter-row {
            padding: 1vw 1.5vw;
            gap: 1vw;
        }

        .custom-select-wrapper {
            min-width: 10vw;
        }
    }

    @media (max-width: 768px) {
        .katalog_top_container {
            padding: 2vw 3vw;
        }

        .catalog-header h1 {
            font-size: 2.8vw;
        }

        .catalog-header p {
            font-size: 1.4vw;
        }

        .filter-row {
            padding: 1.2vw 1.8vw;
            gap: 1.2vw;
            flex-wrap: wrap;
        }

        .filter-row .filter-label {
            font-size: 1.2vw;
        }

        .custom-select-wrapper {
            min-width: 14vw;
        }

        .custom-select-wrapper .custom-select-trigger {
            font-size: 1.2vw;
            padding: 0.6vw 2.5vw 0.6vw 1.2vw;
            min-height: 3vw;
        }

        .custom-select-wrapper .custom-select-dropdown .dropdown-item {
            font-size: 1.2vw;
            padding: 0.6vw 1.2vw;
        }

        .filter-divider {
            height: 2.5vw;
        }

        .filter-count {
            font-size: 1.2vw;
        }

        .filter-reset {
            font-size: 1.2vw;
        }

        .product-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5vw;
            padding: 2vw 3vw;
        }

        .product_layout_content h5 {
            font-size: 1.2vw;
        }

        .product_layout_price .price-discount,
        .product_layout_price p {
            font-size: 1.3vw;
        }

        .product_layout_button .buy_now_btn {
            font-size: 0.9vw;
            padding: 0.6vw 1.2vw;
        }
    }

    @media (max-width: 480px) {
        .katalog_top_container {
            padding: 2vw 2vw;
        }

        .catalog-header h1 {
            font-size: 3.6vw;
        }

        .catalog-header p {
            font-size: 1.8vw;
        }

        .filter-row {
            padding: 1.5vw 2vw;
            gap: 1.5vw;
            border-radius: 1vw;
        }

        .filter-row .filter-label {
            font-size: 1.6vw;
        }

        .custom-select-wrapper {
            min-width: 20vw;
            flex: 1;
        }

        .custom-select-wrapper .custom-select-trigger {
            font-size: 1.6vw;
            padding: 0.8vw 3.5vw 0.8vw 1.5vw;
            min-height: 4vw;
            border-radius: 0.8vw;
        }

        .custom-select-wrapper .custom-select-dropdown .dropdown-item {
            font-size: 1.6vw;
            padding: 0.8vw 1.5vw;
        }

        .custom-select-wrapper .custom-select-dropdown {
            min-width: 100%;
            border-radius: 0.8vw;
        }

        .filter-divider {
            display: none;
        }

        .filter-count {
            font-size: 1.6vw;
            width: 100%;
            text-align: center;
            margin-top: 0.5vw;
        }

        .filter-reset {
            font-size: 1.6vw;
        }

        .product-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 2vw;
            padding: 2vw 2vw;
        }

        .product_layout_box {
            padding: 1.2vw;
            border-radius: 1.2vw;
        }

        .product_layout_content h5 {
            font-size: 1.6vw;
        }

        .product_layout_price .price-discount,
        .product_layout_price p {
            font-size: 1.8vw;
        }

        .product_layout_price .price-original {
            font-size: 1.2vw;
        }

        .product_layout_button {
            gap: 0.5vw;
            flex-wrap: wrap;
        }

        .product_layout_button .buy_now_btn {
            font-size: 1.2vw;
            padding: 0.8vw 1.5vw;
        }

        .product_layout_button .add_to_cart_btn,
        .product_layout_button .add_to_wishlist_btn {
            font-size: 1.2vw;
            padding: 0.6vw 0.8vw;
        }
    }

    @media (max-width: 360px) {
        .product-grid {
            grid-template-columns: 1fr;
        }

        .custom-select-wrapper {
            min-width: 100%;
        }
    }
</style>

<div class="catalog-container">
    <div class="katalog_top_container">
        <div class="catalog-header">
            <h1>
                @if(request('search'))
                    Hasil Pencarian: "{{ request('search') }}"
                @else
                    Katalog Produk
                @endif
            </h1>
            <p>
                @if(request('search'))
                    Menampilkan {{ $products->total() }} produk untuk "{{ request('search') }}"
                @else
                    Temukan produk terbaik dari Barokah Sport
                @endif
            </p>
            
            @if(request('search') && $products->total() > 0)
                <div style="margin-top: 0.5vw;">
                    <a href="{{ route('customer.products.index') }}" 
                    style="font-size:0.7vw;color:#ef4444;text-decoration:none;"
                    onmouseover="this.style.textDecoration='underline'"
                    onmouseout="this.style.textDecoration='none'">
                        ✕ Hapus pencarian
                    </a>
                </div>
            @endif
        </div>

        {{-- ============================================ --}}
        {{-- FILTER ROW - CUSTOM SELECT --}}
        {{-- ============================================ --}}
        <form id="filter-form" method="GET" action="{{ route('customer.products.index') }}" class="filter-row">
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            {{-- KATEGORI --}}
            <span class="filter-label">Kategori</span>
            <div class="custom-select-wrapper" data-name="category">
                <div class="custom-select-trigger">
                    <span class="trigger-text">
                        {{ request('category') ? $categories->firstWhere('id', request('category'))->name ?? 'Semua' : 'Semua' }}
                    </span>
                    <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                </div>
                <div class="custom-select-dropdown">
                    <div class="dropdown-item {{ !request('category') ? 'active' : '' }}" data-value="">
                        <span>Semua</span>
                        <span class="check-icon">✓</span>
                    </div>
                    @foreach ($categories as $category)
                        <div class="dropdown-item {{ request('category') == $category->id ? 'active' : '' }}" 
                             data-value="{{ $category->id }}">
                            <span>{{ $category->name }}</span>
                            <span class="check-icon">✓</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="filter-divider"></div>

            {{-- GENDER --}}
            <span class="filter-label">Gender</span>
            <div class="custom-select-wrapper" data-name="gender">
                <div class="custom-select-trigger">
                    <span class="trigger-text">
                        {{ request('gender') ? ucfirst(request('gender')) : 'Semua' }}
                    </span>
                    <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                </div>
                <div class="custom-select-dropdown">
                    <div class="dropdown-item {{ !request('gender') ? 'active' : '' }}" data-value="">
                        <span>Semua</span>
                        <span class="check-icon">✓</span>
                    </div>
                    @foreach ($genders as $gender)
                        <div class="dropdown-item {{ request('gender') == $gender ? 'active' : '' }}" 
                             data-value="{{ $gender }}">
                            <span>{{ ucfirst($gender) }}</span>
                            <span class="check-icon">✓</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="filter-divider"></div>

            {{-- UKURAN --}}
            <span class="filter-label">Ukuran</span>
            <div class="custom-select-wrapper" data-name="size">
                <div class="custom-select-trigger">
                    <span class="trigger-text">
                        {{ request('size') ?: 'Semua' }}
                    </span>
                    <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                </div>
                <div class="custom-select-dropdown">
                    <div class="dropdown-item {{ !request('size') ? 'active' : '' }}" data-value="">
                        <span>Semua</span>
                        <span class="check-icon">✓</span>
                    </div>
                    @foreach ($sizes as $size)
                        <div class="dropdown-item {{ request('size') == $size ? 'active' : '' }}" 
                             data-value="{{ $size }}">
                            <span>{{ $size }}</span>
                            <span class="check-icon">✓</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="filter-divider"></div>

            {{-- WARNA --}}
            <span class="filter-label">Warna</span>
            <div class="custom-select-wrapper" data-name="color">
                <div class="custom-select-trigger">
                    <span class="trigger-text">
                        {{ request('color') ?: 'Semua' }}
                    </span>
                    <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                </div>
                <div class="custom-select-dropdown">
                    <div class="dropdown-item {{ !request('color') ? 'active' : '' }}" data-value="">
                        <span>Semua</span>
                        <span class="check-icon">✓</span>
                    </div>
                    @foreach ($colors as $color)
                        <div class="dropdown-item {{ request('color') == $color ? 'active' : '' }}" 
                             data-value="{{ $color }}">
                            <span>{{ $color }}</span>
                            <span class="check-icon">✓</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="filter-divider"></div>

            {{-- SORT BY --}}
            <span class="filter-label">Urutkan</span>
            <div class="custom-select-wrapper" data-name="sort">
                <div class="custom-select-trigger">
                    <span class="trigger-text">
                        @php
                            $sortOptions = [
                                'newest' => 'Terbaru',
                                'price_asc' => 'Harga: Rendah → Tinggi',
                                'price_desc' => 'Harga: Tinggi → Rendah',
                                'name' => 'Nama (A-Z)'
                            ];
                        @endphp
                        {{ $sortOptions[request('sort', 'newest')] ?? 'Terbaru' }}
                    </span>
                    <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                </div>
                <div class="custom-select-dropdown">
                    <div class="dropdown-item {{ request('sort', 'newest') == 'newest' ? 'active' : '' }}" data-value="newest">
                        <span>Terbaru</span>
                        <span class="check-icon">✓</span>
                    </div>
                    <div class="dropdown-item {{ request('sort') == 'price_asc' ? 'active' : '' }}" data-value="price_asc">
                        <span>Harga: Rendah → Tinggi</span>
                        <span class="check-icon">✓</span>
                    </div>
                    <div class="dropdown-item {{ request('sort') == 'price_desc' ? 'active' : '' }}" data-value="price_desc">
                        <span>Harga: Tinggi → Rendah</span>
                        <span class="check-icon">✓</span>
                    </div>
                    <div class="dropdown-item {{ request('sort') == 'name' ? 'active' : '' }}" data-value="name">
                        <span>Nama (A-Z)</span>
                        <span class="check-icon">✓</span>
                    </div>
                </div>
            </div>

            {{-- RESET FILTER --}}
            @if(request()->anyFilled(['category', 'gender', 'size', 'color', 'sort']))
                <a href="{{ route('customer.products.index') }}" class="filter-reset">
                    ✕ Reset
                </a>
            @endif

            <span class="filter-count">{{ $products->total() }} produk</span>
        </form>
    </div>

    {{-- ============================================ --}}
    {{-- PRODUCT GRID --}}
    {{-- ============================================ --}}
    <div class="product-grid">
        @forelse ($products as $product)
            <div class="product_layout_box" data-product-id="{{ $product->id }}">
                <div class="product_layout_img">
                    <a href="{{ route('customer.products.show', $product->slug) }}">
                        <img src="{{ Storage::url($product->images->first()->image) }}" 
                            alt="{{ $product->name }}">
                            @if($product->isOutOfStock())
                        <span class="product_badge out-of-stock">HABIS</span>
                        @endif
                    </a>
                </div>
                <div class="product_layout_content">
                    <h5>{{ $product->name }}</h5>
                    <div class="product_layout_price">
                        @php
                            $prices = [];
                            foreach ($product->variants as $variant) {
                                $prices[] = $variant->discount_price ? (float) $variant->discount_price : (float) $variant->price;
                            }
                            $minEffective = min($prices);
                            $maxEffective = max($prices);
                            
                            $minPrice = $product->variants->min('price');
                            $maxPrice = $product->variants->max('price');
                            
                            $hasDiscount = $product->variants->contains(function($v) {
                                return $v->discount_price !== null && $v->discount_price < $v->price;
                            });
                        @endphp
                        
                        @if($hasDiscount)
                            <div class="product_layout_price_box">
                                @if($minEffective == $maxEffective)
                                    <p class="price-discount">Rp {{ number_format($minEffective, 0, ',', '.') }}</p>
                                @else
                                    <p class="price-discount">Rp {{ number_format($minEffective, 0, ',', '.') }} - Rp {{ number_format($maxEffective, 0, ',', '.') }}</p>
                                @endif
                                @if($minPrice == $maxPrice)
                                    <span class="price-original">Rp {{ number_format($minPrice, 0, ',', '.') }}</span>
                                @else
                                    <span class="price-original">Rp {{ number_format($minPrice, 0, ',', '.') }} - Rp {{ number_format($maxPrice, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        @else
                            @if($minEffective == $maxEffective)
                                <p>Rp {{ number_format($minEffective, 0, ',', '.') }}</p>
                            @else
                                <p>Rp {{ number_format($minEffective, 0, ',', '.') }} - Rp {{ number_format($maxEffective, 0, ',', '.') }}</p>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="product_layout_button">
                    <button class="buy_now_btn {{ $product->isOutOfStock() ? 'disabled' : '' }}" 
                            onclick="{{ $product->isOutOfStock() ? '' : 'buyNow(' . $product->id . ')' }}"
                            {{ $product->isOutOfStock() ? 'disabled' : '' }}>
                        {{ $product->isOutOfStock() ? 'HABIS' : 'BELI SEKARANG' }}
                    </button>
                    <button class="add_to_cart_btn {{ $product->isOutOfStock() ? 'disabled' : '' }}" 
                            onclick="{{ $product->isOutOfStock() ? '' : 'addToCart(' . $product->id . ')' }}"
                            {{ $product->isOutOfStock() ? 'disabled' : '' }}>
                        <iconify-icon icon="solar:cart-linear"></iconify-icon>
                    </button>
                    <button class="add_to_wishlist_btn" 
                            data-product-id="{{ $product->id }}"
                            data-in-wishlist="{{ in_array($product->id, array_keys(session()->get('wishlist', []))) ? 'true' : 'false' }}"
                            onclick="addToWishlist({{ $product->id }})">
                        @if(in_array($product->id, array_keys(session()->get('wishlist', []))))
                            <iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>
                        @else
                            <iconify-icon icon="solar:heart-linear"></iconify-icon>
                        @endif
                    </button>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">🔍</div>
                <p>Belum ada produk yang tersedia.</p>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if ($products->hasPages())
        <div class="pagination-wrapper">
            {{ $products->links() }}
        </div>
    @endif

</div>

{{-- ============================================ --}}
{{-- JAVASCRIPT - CUSTOM SELECT --}}
{{-- ============================================ --}}
<script>
// ============================================
// CUSTOM SELECT - TOGGLE DROPDOWN
// ============================================

// 🔥 FUNGSI TOGGLE DROPDOWN
function toggleDropdown(trigger) {
    var wrapper = trigger.closest('.custom-select-wrapper');
    if (!wrapper) return;
    
    var dropdown = wrapper.querySelector('.custom-select-dropdown');
    if (!dropdown) return;
    
    var isOpen = dropdown.classList.contains('open');

    // Tutup semua dropdown lain
    var allDropdowns = document.querySelectorAll('.custom-select-dropdown.open');
    allDropdowns.forEach(function(d) {
        if (d !== dropdown) {
            d.classList.remove('open');
            var t = d.closest('.custom-select-wrapper')?.querySelector('.custom-select-trigger');
            if (t) t.classList.remove('open');
        }
    });

    if (isOpen) {
        dropdown.classList.remove('open');
        trigger.classList.remove('open');
    } else {
        dropdown.classList.add('open');
        trigger.classList.add('open');
    }
}

// 🔥 TUTUP DROPDOWN SAAT KLIK DI LUAR
function closeAllDropdowns() {
    var allDropdowns = document.querySelectorAll('.custom-select-dropdown.open');
    allDropdowns.forEach(function(dropdown) {
        dropdown.classList.remove('open');
        var trigger = dropdown.closest('.custom-select-wrapper')?.querySelector('.custom-select-trigger');
        if (trigger) {
            trigger.classList.remove('open');
        }
    });
}

// ============================================
// INITIALIZE
// ============================================
document.addEventListener('DOMContentLoaded', function() {

    // 🔥 EVENT LISTENER UNTUK TRIGGER
    var triggers = document.querySelectorAll('.custom-select-trigger');
    triggers.forEach(function(trigger) {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleDropdown(this);
        });
    });

    // 🔥 SELECT ITEM
    var items = document.querySelectorAll('.dropdown-item');
    items.forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.stopPropagation();
            
            var wrapper = this.closest('.custom-select-wrapper');
            if (!wrapper) return;
            
            var trigger = wrapper.querySelector('.custom-select-trigger');
            var dropdown = wrapper.querySelector('.custom-select-dropdown');
            var name = wrapper.dataset.name;
            var value = this.dataset.value;
            var text = this.querySelector('span')?.textContent || '';

            // Update trigger text
            var triggerText = trigger?.querySelector('.trigger-text');
            if (triggerText) {
                triggerText.textContent = text;
            }

            // Update active state
            if (dropdown) {
                var allItems = dropdown.querySelectorAll('.dropdown-item');
                allItems.forEach(function(d) {
                    d.classList.remove('active');
                });
            }
            this.classList.add('active');

            // Close dropdown
            if (dropdown) dropdown.classList.remove('open');
            if (trigger) trigger.classList.remove('open');

            // 🔥 UPDATE FORM DAN SUBMIT
            var form = document.getElementById('filter-form');
            if (!form) return;
            
            // Hapus input lama jika ada
            var oldInput = form.querySelector('input[name="' + name + '"]');
            if (oldInput) {
                oldInput.remove();
            }

            // Buat input baru
            if (value !== '') {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            }

            // Submit form
            form.submit();
        });
    });

    // 🔥 TUTUP DROPDOWN SAAT KLIK DI LUAR
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-select-wrapper')) {
            closeAllDropdowns();
        }
    });

    // 🔥 BUY NOW
    window.buyNow = function(productId) {
        fetch('/api/products/' + productId + '/variants', {
            headers: { 'Accept': 'application/json' }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success && data.variants && data.variants.length > 0) {
                if (typeof openVariantModal === 'function') {
                    window._buyNowMode = true;
                    openVariantModal(productId, 'buy_now');
                }
            } else {
                window._buyNowMode = true;
                if (typeof addToCartDirect === 'function') {
                    addToCartDirect(productId);
                }
            }
        })
        .catch(function() {
            window._buyNowMode = true;
            if (typeof addToCartDirect === 'function') {
                addToCartDirect(productId);
            }
        });
    };

    console.log('🛒 Katalog produk siap dengan custom select');
});
</script>

@endsection