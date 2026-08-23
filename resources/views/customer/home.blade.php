@extends('layouts.customer')

@section('title', 'Barokah Sport')

@section('content')
<div class="banner_slide">
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            @forelse($banners as $banner)
                <div class="swiper-slide">
                    <div class="slide_box" 
                         style="background-image:url('{{ Storage::url($banner->image) }}'); 
                                background-position:center; 
                                background-size:cover;">
                        
                        @if($banner->title)
                            <h2>{{ $banner->title }}</h2>
                        @endif
                        
                        @if($banner->subtitle)
                            <p>{{ $banner->subtitle }}</p>
                        @endif
                        
                        @if($banner->button_text && $banner->button_url)
                            <div class="banner_button">
                                <a href="{{ $banner->button_url }}">
                                    <button>{{ $banner->button_text }}</button>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                {{-- Default banner jika tidak ada data --}}
                <div class="swiper-slide">
                    <div class="slide_box" style="background-image:url({{ asset('images/slide_img.webp') }}); background-position:center; background-size:cover;">
                        <h2>Performa dan Gaya dalam <span>Satu</span> Pilihan.</h2>
                        <p>Tampil sporty dengan jaket dan celana olahraga yang nyaman, stylish, dan siap menemani setiap aktivitas.</p>
                        <div class="banner_button">
                            <a href="{{ route('customer.products.index') }}">
                                <button>Belanja Sekarang</button>
                            </a>
                            <a href="https://api.whatsapp.com/send?phone=6287866291056" target="_blank">
                                <button>Hubungi Kami</button>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="slide_box" style="background-image:url({{ asset('images/slide_img_2.png') }}); background-position:center; background-size:cover;">
                        <h2>Nyaman <span>Maksimal</span>, Bergerak Bebas.</h2>
                        <p>Dirancang dengan material ringan dan fleksibel, paduan sempurna untuk performa latihan terbaik dan gaya kasual harianmu.</p>
                        <div class="banner_button">
                            <a href="{{ route('customer.products.index') }}">
                                <button>Belanja Sekarang</button>
                            </a>
                            <a href="https://api.whatsapp.com/send?phone=6287866291056" target="_blank">
                                <button>Hubungi Kami</button>
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</div>

<div class="keunggulan_layout">
    <div class="keunggulan_box_layout">
        <img src="{{ asset('images/gratis_ongkir.svg') }}" alt="">
        <div class="keunggulan_box_content">
            <h3>GRATIS ONGKIR</h3>
            <p>Pembelian di atas 750.000</p>
        </div>
    </div>
    <div class="keunggulan_box_layout">
        <img src="{{ asset('images/retur_mudah.svg') }}" alt="">
        <div class="keunggulan_box_content">
            <h3>PENGEMBALIAN MUDAH</h3>
            <p>Retur mudah dalam 7 hari.</p>
        </div>
    </div>
    <div class="keunggulan_box_layout">
        <img src="{{ asset('images/safety_pay.svg') }}" alt="">
        <div class="keunggulan_box_content">
            <h3>PEMBAYARAN AMAN</h3>
            <p>Metode pembayaran terpercaya</p>
        </div>
    </div>
    <div class="keunggulan_box_layout">
        <img src="{{ asset('images/cs.svg') }}" alt="">
        <div class="keunggulan_box_content">
            <h3>BANTUAN CEPAT</h3>
            <p>24/7 Support</p>
        </div>
    </div>
</div>

<div class="kategori_layout">
    <div class="swiper categoriesSwiper">
        <div class="swiper-wrapper">
            @foreach($categories as $category)
            <div class="swiper-slide">
                <div class="kategori_box_layout" 
                    style="background-image:url('{{ $category->image ? Storage::url($category->image) : asset('images/default_category.png') }}'); 
                            background-size:100% 100%; 
                            background-position:center; cursor:pointer;"
                    onclick="window.location.href='{{ route('customer.products.index', ['category' => $category->id]) }}'">
                    <h3>{{ $category->name }}</h3>
                    <a href="{{ route('customer.products.index', ['category' => $category->id]) }}" 
                       onclick="event.stopPropagation();">
                       BELI SEKARANG
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</div>

<div class="product_layout">
    <div class="heading_product_layout">
        <h3>KOLEKSI TERBARU</h3>
        <a href="{{ route('customer.products.latest') }}">LIHAT SEMUA</a>
    </div>
    <div class="product_layout_grid">
        @forelse($latestProducts as $product)
        @php
            // 🔥 HITUNG DISKON TERBESAR
            $maxDiscount = 0;
            foreach ($product->variants as $variant) {
                if ($variant->discount_price && $variant->discount_price < $variant->price) {
                    $discount = round((($variant->price - $variant->discount_price) / $variant->price) * 100);
                    if ($discount > $maxDiscount) {
                        $maxDiscount = $discount;
                    }
                }
            }
            
            // 🔥 AMBIL DATA DARI attachDiscountData
            $hasDiscount = $product->has_discount ?? false;
            $displayPrice = $product->display_price ?? '';
            $originalPriceDisplay = $product->original_price_display ?? null;
            $discountLabel = $product->discount_label ?? null;
        @endphp
        <div class="product_layout_box" data-product-id="{{ $product->id }}">
            <div class="product_layout_img">
                <a href="{{ route('customer.products.show', $product->slug) }}">
                    <img src="{{ $product->images->first() ? Storage::url($product->images->first()->image) : asset('images/product_dummy.png') }}" 
                        alt="{{ $product->name }}">
                </a>
                @if($product->isOutOfStock())
                <span class="product_badge out-of-stock">HABIS</span>
                @endif
                @if($maxDiscount > 0)
                    <span class="discount_badge">Diskon {{ $maxDiscount }}%</span>
                @endif
            </div>
            <div class="product_layout_content">
                <h5>{{ $product->name }}</h5>
                <div class="product_layout_price">
                    @php
                        // Harga efektif (diskon jika ada)
                        $prices = [];
                        foreach ($product->variants as $variant) {
                            $prices[] = $variant->discount_price ? (float) $variant->discount_price : (float) $variant->price;
                        }
                        $minEffective = min($prices);
                        $maxEffective = max($prices);
                        
                        // Harga asli
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
        <div class="empty_state">
            <p>Belum ada produk terbaru</p>
        </div>
        @endforelse
    </div>
</div>

<div class="product_layout">
    <div class="heading_product_layout">
        <h3>PRODUK UNGGULAN</h3>
        <a href="{{ route('customer.products.index') }}">LIHAT SEMUA</a>
    </div>
    <div class="product_layout_grid">
        @forelse($featuredProducts as $product)
        @php
            // 🔥 HITUNG DISKON TERBESAR
            $maxDiscount = 0;
            foreach ($product->variants as $variant) {
                if ($variant->discount_price && $variant->discount_price < $variant->price) {
                    $discount = round((($variant->price - $variant->discount_price) / $variant->price) * 100);
                    if ($discount > $maxDiscount) {
                        $maxDiscount = $discount;
                    }
                }
            }
            
            // 🔥 AMBIL DATA DARI attachDiscountData
            $hasDiscount = $product->has_discount ?? false;
            $displayPrice = $product->display_price ?? '';
            $originalPriceDisplay = $product->original_price_display ?? null;
            $discountLabel = $product->discount_label ?? null;
        @endphp
        <div class="product_layout_box" data-product-id="{{ $product->id }}">
            <div class="product_layout_img">
                <a href="{{ route('customer.products.show', $product->slug) }}">
                    <img src="{{ $product->images->first() ? Storage::url($product->images->first()->image) : asset('images/product_dummy.png') }}" 
                        alt="{{ $product->name }}">
                </a>
                @if($product->isOutOfStock())
                <span class="product_badge out-of-stock">HABIS</span>
                @endif
                @if($maxDiscount > 0)
                    <span class="discount_badge">Diskon {{ $maxDiscount }}%</span>
                @endif
            </div>
            <div class="product_layout_content">
                <h5>{{ $product->name }}</h5>
                <div class="product_layout_price">
                    @php
                        // Harga efektif (diskon jika ada)
                        $prices = [];
                        foreach ($product->variants as $variant) {
                            $prices[] = $variant->discount_price ? (float) $variant->discount_price : (float) $variant->price;
                        }
                        $minEffective = min($prices);
                        $maxEffective = max($prices);
                        
                        // Harga asli
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
                <button class="buy_now_btn" onclick="buyNow({{ $product->id }})">BELI SEKARANG</button>
                <button class="add_to_cart_btn" onclick="addToCart({{ $product->id }})">
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
        <div class="empty_state">
            <p>Belum ada produk unggulan</p>
        </div>
        @endforelse
    </div>
</div>


<div class="promo_section" style="background-image:url({{ asset('images/promo_section_bg.png') }}); background-size:cover; background-position:center; background-repeat:no-repeat;">
    <div class="promo_section_content">
        <button><iconify-icon icon="mdi:fire"></iconify-icon> PROMO TERBATAS</button>
    </div>
    <h3>Harga Lebih Hemat<span>.</span></h3>
    <p>Pilihan Sporty, Harga Lebih Hemat <br/>Temukan produk favorit dengan harga spesial.</p>
    <a href="/katalog/promo">
        <button class="promo_button">Lihat Produk Promo</button>
    </a>
</div>

<div class="product_layout">
    <div class="heading_product_layout">
        <h3>PRODUK <span>TERLARIS BULAN</span> INI</h3>
        <a href="{{ route('customer.products.index') }}">LIHAT SEMUA</a>
    </div>
    <div class="product_layout_grid">
        @forelse($bestSellers as $product)
        @php
            // 🔥 HITUNG DISKON TERBESAR
            $maxDiscount = 0;
            foreach ($product->variants as $variant) {
                if ($variant->discount_price && $variant->discount_price < $variant->price) {
                    $discount = round((($variant->price - $variant->discount_price) / $variant->price) * 100);
                    if ($discount > $maxDiscount) {
                        $maxDiscount = $discount;
                    }
                }
            }
            
            // 🔥 AMBIL DATA DARI attachDiscountData
            $hasDiscount = $product->has_discount ?? false;
            $displayPrice = $product->display_price ?? '';
            $originalPriceDisplay = $product->original_price_display ?? null;
            $discountLabel = $product->discount_label ?? null;
        @endphp
        <div class="product_layout_box" data-product-id="{{ $product->id }}">
            <div class="product_layout_img">
                <a href="{{ route('customer.products.show', $product->slug) }}">
                    <img src="{{ $product->images->first() ? Storage::url($product->images->first()->image) : asset('images/product_dummy.png') }}" 
                        alt="{{ $product->name }}">
                </a>
                @if($product->isOutOfStock())
                <span class="product_badge out-of-stock">HABIS</span>
                @endif
                @if($maxDiscount > 0)
                    <span class="discount_badge">Diskon {{ $maxDiscount }}%</span>
                @endif
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
                <button class="buy_now_btn" onclick="buyNow({{ $product->id }})">BELI SEKARANG</button>
                <button class="add_to_cart_btn" onclick="addToCart({{ $product->id }})">
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
        <div class="empty_state">
            <p>Belum ada produk terlaris</p>
        </div>
        @endforelse
    </div>
</div>

<div class="testimonial_section">
    <div class="heading_product_layout">
        <h3>MEREKA SUDAH <span>MEMBUKTIKAN</span></h3>
        <a href="">LIHAT SEMUA</a>
    </div>
    <div class="testimonial_layout">
        <div class="swiper testimonialSwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="testimonial_box">
                        <div class="testimonial_box_header">
                            <div class="testimonial_box_header_profile">
                                <div class="testimonial_box_header_profile_name">
                                    <h5>MR</h5>
                                </div>
                                <div class="testimonial_box_header_desc">
                                    <h3>M****** R****</h3>
                                    <div class="testimonial_box_rating">
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial_box_header_img">
                                <img src="images/pap.png" alt="">
                            </div>
                        </div>
                        <div class="testimonial_box_content">
                            <p>Udah 2 kali order jaket dan celana running di Barokah Sport, kualitas bahan emang ga pernah ngecewain. Bahannya adem, jahitan rapi, dan dipakainya nyaman banget buat olahraga harian.</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial_box">
                        <div class="testimonial_box_header">
                            <div class="testimonial_box_header_profile">
                                <div class="testimonial_box_header_profile_name">
                                    <h5>MR</h5>
                                </div>
                                <div class="testimonial_box_header_desc">
                                    <h3>M****** R****</h3>
                                    <div class="testimonial_box_rating">
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial_box_header_img">
                                <img src="images/pap.png" alt="">
                            </div>
                        </div>
                        <div class="testimonial_box_content">
                            <p>Udah 2 kali order jaket dan celana running di Barokah Sport, kualitas bahan emang ga pernah ngecewain. Bahannya adem, jahitan rapi, dan dipakainya nyaman banget buat olahraga harian.</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial_box">
                        <div class="testimonial_box_header">
                            <div class="testimonial_box_header_profile">
                                <div class="testimonial_box_header_profile_name">
                                    <h5>MR</h5>
                                </div>
                                <div class="testimonial_box_header_desc">
                                    <h3>M****** R****</h3>
                                    <div class="testimonial_box_rating">
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial_box_header_img">
                                <img src="images/pap.png" alt="">
                            </div>
                        </div>
                        <div class="testimonial_box_content">
                            <p>Udah 2 kali order jaket dan celana running di Barokah Sport, kualitas bahan emang ga pernah ngecewain. Bahannya adem, jahitan rapi, dan dipakainya nyaman banget buat olahraga harian.</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial_box">
                        <div class="testimonial_box_header">
                            <div class="testimonial_box_header_profile">
                                <div class="testimonial_box_header_profile_name">
                                    <h5>MR</h5>
                                </div>
                                <div class="testimonial_box_header_desc">
                                    <h3>M****** R****</h3>
                                    <div class="testimonial_box_rating">
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial_box_header_img">
                                <img src="images/pap.png" alt="">
                            </div>
                        </div>
                        <div class="testimonial_box_content">
                            <p>Udah 2 kali order jaket dan celana running di Barokah Sport, kualitas bahan emang ga pernah ngecewain. Bahannya adem, jahitan rapi, dan dipakainya nyaman banget buat olahraga harian.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>

@if($articles->isNotEmpty())
    <div class="artikel_section">
        <div class="heading_product_layout">
            <h3>ARTIKEL <span>TERBARU</span> KAMI</h3>
            <a href="{{ route('customer.articles.index') }}">LIHAT SEMUA</a>
        </div>
        <div class="artikel_section_layout">
            @foreach($articles as $article)
                <div class="artikel_section_box">
                    <div class="artikel_section_box_img">
                        <a href="{{ route('customer.articles.show', $article->slug) }}">
                            @if($article->image)
                                <img src="{{ Storage::url($article->image) }}" alt="{{ $article->title }}">
                            @else
                                <img src="{{ asset('images/default-article.jpg') }}" alt="{{ $article->title }}">
                            @endif
                        </a>
                    </div>
                    <div class="artikel_section_content">
                        <div class="artikel_section_meta">
                            <div class="artikel_section_meta_box">
                                <iconify-icon icon="mdi:user"></iconify-icon>
                                <span>{{ $article->author ?? 'Admin' }}</span>
                            </div>
                            <div class="artikel_section_meta_box">
                                <iconify-icon icon="lets-icons:date-fill"></iconify-icon>
                                <span>{{ $article->formatted_published_at }}</span>
                            </div>
                            {{-- 🔥 GANTI category dengan articleCategory->name --}}
                            @if($article->articleCategory)
                                <div class="artikel_section_meta_box">
                                    <iconify-icon icon="material-symbols:category"></iconify-icon>
                                    <span>{{ $article->articleCategory->name }}</span>
                                </div>
                            @endif
                        </div>
                        <h3>{{ $article->title }}</h3>
                        <p>{{ Str::limit(strip_tags($article->excerpt ?: $article->content), 120) }}</p>
                        <a href="{{ route('customer.articles.show', $article->slug) }}">
                            <button>Baca Selengkapnya</button>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<script>
    // ============================================
    // OVERRIDE FUNGSI WISHLIST UNTUK HOME PAGE
    // ============================================
    
    /**
     * 🔥 UPDATE ALL WISHLIST BUTTONS FOR SAME PRODUCT
     */
    function updateAllWishlistButtons(productId, inWishlist) {
        // Cari semua tombol wishlist untuk produk ini di seluruh halaman
        const allButtons = document.querySelectorAll(`.add_to_wishlist_btn[data-product-id="${productId}"]`);
        
        console.log(`❤️ Updating ${allButtons.length} wishlist buttons for product ${productId}`);
        
        allButtons.forEach(function(btn) {
            if (inWishlist) {
                btn.innerHTML = '<iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>';
                btn.classList.add('active');
                btn.dataset.inWishlist = 'true';
            } else {
                btn.innerHTML = '<iconify-icon icon="solar:heart-linear"></iconify-icon>';
                btn.classList.remove('active');
                btn.dataset.inWishlist = 'false';
            }
            btn.disabled = false;
        });
    }

    /**
     * 🔥 OVERRIDE addToWishlist UNTUK HOME PAGE
     */
    if (typeof window.addToWishlist === 'function') {
        const originalAddToWishlist = window.addToWishlist;
        
        window.addToWishlist = function(productId) {
            console.log('❤️ addToWishlist called from home page for product:', productId);
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            
            // Disable semua tombol untuk produk ini
            const allButtons = document.querySelectorAll(`.add_to_wishlist_btn[data-product-id="${productId}"]`);
            allButtons.forEach(function(btn) {
                btn.disabled = true;
                btn.innerHTML = '⏳';
            });
            
            fetch(window.customerRoutes.wishlistAdd, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const count = data.count || 0;
                    const inWishlist = data.in_wishlist || false;
                    
                    console.log('❤️ Wishlist response:', { count, inWishlist });
                    
                    // 🔥 UPDATE SEMUA TOMBOL UNTUK PRODUK INI
                    updateAllWishlistButtons(productId, inWishlist);
                    
                    // 🔥 UPDATE WISHLIST COUNT
                    if (typeof window.updateNavbarWishlistCount === 'function') {
                        window.updateNavbarWishlistCount(count);
                    } else {
                        const wishlistCountEl = document.getElementById('wishlist-count');
                        if (wishlistCountEl) {
                            wishlistCountEl.textContent = count;
                            wishlistCountEl.style.display = count > 0 ? 'inline-flex' : 'none';
                        }
                    }
                    
                    // 🔥 TRIGGER EVENT
                    document.dispatchEvent(new CustomEvent('wishlist-updated', {
                        detail: { 
                            count: count, 
                            product_id: productId,
                            in_wishlist: inWishlist
                        }
                    }));
                    
                    showToast(data.message || (inWishlist ? 'Produk ditambahkan ke wishlist!' : 'Produk dihapus dari wishlist!'), 'success');
                    
                    if (typeof loadWishlistPopup === 'function') {
                        loadWishlistPopup();
                    }
                } else {
                    showToast(data.message || 'Gagal menambahkan ke wishlist', 'error');
                    // Reset tombol
                    allButtons.forEach(function(btn) {
                        const currentState = btn.dataset.inWishlist === 'true';
                        if (currentState) {
                            btn.innerHTML = '<iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>';
                            btn.classList.add('active');
                        } else {
                            btn.innerHTML = '<iconify-icon icon="solar:heart-linear"></iconify-icon>';
                            btn.classList.remove('active');
                        }
                        btn.disabled = false;
                    });
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                showToast('Terjadi kesalahan', 'error');
                // Reset tombol
                allButtons.forEach(function(btn) {
                    const currentState = btn.dataset.inWishlist === 'true';
                    if (currentState) {
                        btn.innerHTML = '<iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>';
                        btn.classList.add('active');
                    } else {
                        btn.innerHTML = '<iconify-icon icon="solar:heart-linear"></iconify-icon>';
                        btn.classList.remove('active');
                    }
                    btn.disabled = false;
                });
            });
        };
    }
    
    console.log('✅ Home page wishlist functions initialized');
</script>

{{-- GANTI DENGAN INI --}}
<script>
    // ============================================
    // FUNGSI UNTUK MEMASTIKAN COUNTER UPDATE
    // ============================================
    
    // Override fungsi addToCartDirect dari cart.js untuk memastikan counter update
    if (typeof window.addToCartDirect === 'function') {
        const originalAddToCartDirect = window.addToCartDirect;
        
        window.addToCartDirect = function(productId, variantId = null, quantity = 1) {
            console.log('🔥 addToCartDirect called from home page');
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            
            // Tampilkan loading
            const btn = document.querySelector(`.product_layout_box[data-product-id="${productId}"] .add_to_cart_btn`);
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '⏳';
            }
            
            fetch(window.customerRoutes.cartAdd, {
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
                    const count = data.count || data.cart_count || 0;
                    console.log('✅ Cart add success, count:', count);
                    
                    // 🔥 UPDATE CART COUNT - PAKAI FUNGSI DARI CART.JS
                    if (typeof window.updateNavbarCartCount === 'function') {
                        window.updateNavbarCartCount(count);
                    } else {
                        // Fallback manual
                        const cartCountEl = document.getElementById('cart-count');
                        if (cartCountEl) {
                            cartCountEl.textContent = count;
                            cartCountEl.style.display = count > 0 ? 'inline-flex' : 'none';
                        }
                    }
                    
                    // 🔥 TRIGGER EVENT
                    document.dispatchEvent(new CustomEvent('cart-updated', {
                        detail: { count: count, message: data.message }
                    }));
                    
                    showToast(data.message || 'Produk ditambahkan ke keranjang!', 'success');
                    
                    if (typeof loadCartPopup === 'function') {
                        loadCartPopup();
                    }
                    
                    if (window._buyNowMode) {
                        window._buyNowMode = false;
                        setTimeout(() => {
                            window.location.href = window.customerRoutes.checkout;
                        }, 500);
                    }
                } else {
                    showToast(data.message || 'Gagal menambahkan produk', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan', 'error');
            })
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<iconify-icon icon="solar:cart-linear"></iconify-icon>';
                }
            });
        };
    }
    
    // Override fungsi addToWishlist dari cart.js
    if (typeof window.addToWishlist === 'function') {
        const originalAddToWishlist = window.addToWishlist;
        
        window.addToWishlist = function(productId) {
            console.log('❤️ addToWishlist called from home page');
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            
            const btn = document.querySelector(`.product_layout_box[data-product-id="${productId}"] .add_to_wishlist_btn`);
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '⏳';
            }
            
            fetch(window.customerRoutes.wishlistAdd, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 🔥 UPDATE WISHLIST COUNT
                    if (typeof window.updateNavbarWishlistCount === 'function') {
                        window.updateNavbarWishlistCount(data.count || 0);
                    } else {
                        const wishlistCountEl = document.getElementById('wishlist-count');
                        if (wishlistCountEl) {
                            wishlistCountEl.textContent = data.count || 0;
                            wishlistCountEl.style.display = data.count > 0 ? 'inline-flex' : 'none';
                        }
                    }
                    
                    if (btn) {
                        btn.innerHTML = '<iconify-icon icon="solar:heart-bold"></iconify-icon>';
                        btn.classList.add('active');
                    }
                    
                    showToast(data.message || 'Produk ditambahkan ke wishlist!', 'success');
                    
                    if (typeof loadWishlistPopup === 'function') {
                        loadWishlistPopup();
                    }
                } else {
                    showToast(data.message || 'Gagal menambahkan ke wishlist', 'error');
                }
            })
            .catch(() => {
                showToast('Terjadi kesalahan', 'error');
            })
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                }
            });
        };
    }
    
    console.log('✅ Home page cart functions initialized');
</script>

@endsection