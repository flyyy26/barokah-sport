@extends('layouts.customer')

@section('title', 'Barokah Sport')

@section('content')
<div class="banner_slide">
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <div class="slide_box" style="background-image:url(../images/slide_img.webp); background-position:center; background-size:cover;">
                    <h2>Performa dan Gaya dalam <span>Satu</span> Pilihan.</h2>
                    <p>Tampil sporty dengan jaket dan celana olahraga yang nyaman, stylish, dan siap menemani setiap aktivitas.</p>
                    <div class="banner_button">
                        <a href="#">
                            <button>Belanja Sekarang</button>
                        </a>
                        <a href="#">
                            <button>Hubungi Kami</button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="swiper-slide">
                <div class="slide_box" style="background-image:url(../images/slide_img_2.png); background-position:center; background-size:cover;">
                    <h2>Nyaman <span>Maksimal</span>, Bergerak Bebas.</h2>
                    <p>Dirancang dengan material ringan dan fleksibel, paduan sempurna untuk performa latihan terbaik dan gaya kasual harianmu.</p>
                    <div class="banner_button">
                        <a href="#">
                            <button>Belanja Sekarang</button>
                        </a>
                        <a href="#">
                            <button>Hubungi Kami</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</div>
        <!-- @if ($banners->isNotEmpty())
            <section class="relative overflow-hidden bg-slate-900">
                <div class="relative mx-auto max-w-7xl">
                    @foreach ($banners as $index => $banner)
                        <div class="banner-slide relative h-[300px] sm:h-[400px] lg:h-[500px] {{ $index === 0 ? 'opacity-100' : 'opacity-0 absolute inset-0' }}" 
                            data-index="{{ $index }}">
                            {{-- Cek apakah image ada --}}
                            @if ($banner->image && Storage::disk('public')->exists($banner->image))
                                <img src="{{ Storage::url($banner->image) }}" 
                                    alt="{{ $banner->title }}" 
                                    class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-slate-800 text-4xl text-slate-600">
                                    🖼️ No Image
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent">
                                <div class="flex h-full items-center px-6 sm:px-12 lg:px-16">
                                    <div class="max-w-xl text-white">
                                        @if ($banner->title)
                                            <h2 class="text-2xl font-bold sm:text-4xl lg:text-5xl">{{ $banner->title }}</h2>
                                        @endif
                                        @if ($banner->subtitle)
                                            <p class="mt-2 text-sm text-white/80 sm:text-base">{{ $banner->subtitle }}</p>
                                        @endif
                                        @if ($banner->button_text && $banner->button_url)
                                            <a href="{{ $banner->button_url }}" 
                                            class="mt-4 inline-block rounded-lg bg-white px-6 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">
                                                {{ $banner->button_text }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if ($banners->count() > 1)
                        <div class="banner-dots absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 gap-2">
                            @foreach ($banners as $index => $banner)
                                <button class="h-2 w-2 rounded-full transition-all {{ $index === 0 ? 'w-6 bg-white' : 'bg-white/50 hover:bg-white/70' }}" 
                                        data-slide="{{ $index }}"></button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @endif -->

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
    <div class="kategori_box_layout" style="background-image:url({{ asset('images/jaket_category_img.png') }});">
        <h3>Jaket</h3>
        <a href="#">BELI SEKARANG</a>
    </div>
    <div class="kategori_box_layout" style="background-image:url({{ asset('images/trening_category_img.png') }});">
        <h3>Trening</h3>
        <a href="#">BELI SEKARANG</a>
    </div>
    <div class="kategori_box_layout" style="background-image:url({{ asset('images/oneset_category_img.png') }});">
        <h3>Oneset</h3>
        <a href="#">BELI SEKARANG</a>
    </div>
    <div class="kategori_box_layout" style="background-image:url({{ asset('images/rompi_category_img.png') }});">
        <h3>Rompi</h3>
        <a href="#">BELI SEKARANG</a>
    </div>
    <div class="kategori_box_layout" style="background-image:url({{ asset('images/cargo_category_img.png') }});">
        <h3>Cargo</h3>
        <a href="#">BELI SEKARANG</a>
    </div>
</div>

<div class="product_layout">
    <div class="heading_product_layout">
        <h3>KOLEKSI TERBARU</h3>
        <a href="">LIHAT SEMUA</a>
    </div>
    <div class="product_layout_grid">
        <div class="product_layout_box">
            <div class="product_layout_img">
                <a href="#">
                    <img src="{{ asset('images/product_dummy.png') }}" alt="">
                </a>
            </div>
            <div class="product_layout_content">
                <h5>ONESET SPORT</h5>
                <div class="product_layout_price">
                    <p>Rp. 110.000</p>
                </div>
            </div>
            <div class="product_layout_button">
                <a href="#">
                    <button class="buy_now_btn">BELI SEKARANG</button>
                </a>
                <button class="add_to_cart_btn">
                    <iconify-icon icon="solar:cart-linear"></iconify-icon>
                </button>
                <button class="add_to_wishlist_btn">
                    <iconify-icon icon="solar:heart-linear"></iconify-icon>
                </button>
            </div>
        </div>
        <div class="product_layout_box">
            <div class="product_layout_img">
                <a href="#">
                    <img src="{{ asset('images/product_dummy.png') }}" alt="">
                </a>
            </div>
            <div class="product_layout_content">
                <h5>ONESET SPORT</h5>
                <div class="product_layout_price">
                    <p>Rp. 110.000</p>
                </div>
            </div>
            <div class="product_layout_button">
                <a href="#">
                    <button class="buy_now_btn">BELI SEKARANG</button>
                </a>
                <button class="add_to_cart_btn">
                    <iconify-icon icon="solar:cart-linear"></iconify-icon>
                </button>
                <button class="add_to_wishlist_btn">
                    <iconify-icon icon="solar:heart-linear"></iconify-icon>
                </button>
            </div>
        </div>
        <div class="product_layout_box">
            <div class="product_layout_img">
                <a href="#">
                    <img src="{{ asset('images/product_dummy.png') }}" alt="">
                </a>
            </div>
            <div class="product_layout_content">
                <h5>ONESET SPORT</h5>
                <div class="product_layout_price">
                    <p>Rp. 110.000</p>
                </div>
            </div>
            <div class="product_layout_button">
                <a href="#">
                    <button class="buy_now_btn">BELI SEKARANG</button>
                </a>
                <button class="add_to_cart_btn">
                    <iconify-icon icon="solar:cart-linear"></iconify-icon>
                </button>
                <button class="add_to_wishlist_btn">
                    <iconify-icon icon="solar:heart-linear"></iconify-icon>
                </button>
            </div>
        </div>
        <div class="product_layout_box">
            <div class="product_layout_img">
                <a href="#">
                    <img src="{{ asset('images/product_dummy.png') }}" alt="">
                </a>
            </div>
            <div class="product_layout_content">
                <h5>ONESET SPORT</h5>
                <div class="product_layout_price">
                    <p>Rp. 110.000</p>
                </div>
            </div>
            <div class="product_layout_button">
                <a href="#">
                    <button class="buy_now_btn">BELI SEKARANG</button>
                </a>
                <button class="add_to_cart_btn">
                    <iconify-icon icon="solar:cart-linear"></iconify-icon>
                </button>
                <button class="add_to_wishlist_btn">
                    <iconify-icon icon="solar:heart-linear"></iconify-icon>
                </button>
            </div>
        </div>
        <div class="product_layout_box">
            <div class="product_layout_img">
                <a href="#">
                    <img src="{{ asset('images/product_dummy.png') }}" alt="">
                </a>
            </div>
            <div class="product_layout_content">
                <h5>ONESET SPORT</h5>
                <div class="product_layout_price">
                    <p>Rp. 110.000</p>
                </div>
            </div>
            <div class="product_layout_button">
                <a href="#">
                    <button class="buy_now_btn">BELI SEKARANG</button>
                </a>
                <button class="add_to_cart_btn">
                    <iconify-icon icon="solar:cart-linear"></iconify-icon>
                </button>
                <button class="add_to_wishlist_btn">
                    <iconify-icon icon="solar:heart-linear"></iconify-icon>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="product_layout">
    <div class="heading_product_layout">
        <h3>PRODUK UNGGULAN</h3>
        <a href="">LIHAT SEMUA</a>
    </div>
    <div class="product_layout_grid">
        <div class="product_layout_box">
            <div class="product_layout_img">
                <a href="#">
                    <img src="{{ asset('images/product_dummy.png') }}" alt="">
                </a>
            </div>
            <div class="product_layout_content">
                <h5>ONESET SPORT</h5>
                <div class="product_layout_price">
                    <p>Rp. 110.000</p>
                </div>
            </div>
            <div class="product_layout_button">
                <a href="#">
                    <button class="buy_now_btn">BELI SEKARANG</button>
                </a>
                <button class="add_to_cart_btn">
                    <iconify-icon icon="solar:cart-linear"></iconify-icon>
                </button>
                <button class="add_to_wishlist_btn">
                    <iconify-icon icon="solar:heart-linear"></iconify-icon>
                </button>
            </div>
        </div>
        <div class="product_layout_box">
            <div class="product_layout_img">
                <a href="#">
                    <img src="{{ asset('images/product_dummy.png') }}" alt="">
                </a>
            </div>
            <div class="product_layout_content">
                <h5>ONESET SPORT</h5>
                <div class="product_layout_price">
                    <p>Rp. 110.000</p>
                </div>
            </div>
            <div class="product_layout_button">
                <a href="#">
                    <button class="buy_now_btn">BELI SEKARANG</button>
                </a>
                <button class="add_to_cart_btn">
                    <iconify-icon icon="solar:cart-linear"></iconify-icon>
                </button>
                <button class="add_to_wishlist_btn">
                    <iconify-icon icon="solar:heart-linear"></iconify-icon>
                </button>
            </div>
        </div>
        <div class="product_layout_box">
            <div class="product_layout_img">
                <a href="#">
                    <img src="{{ asset('images/product_dummy.png') }}" alt="">
                </a>
            </div>
            <div class="product_layout_content">
                <h5>ONESET SPORT</h5>
                <div class="product_layout_price">
                    <p>Rp. 110.000</p>
                </div>
            </div>
            <div class="product_layout_button">
                <a href="#">
                    <button class="buy_now_btn">BELI SEKARANG</button>
                </a>
                <button class="add_to_cart_btn">
                    <iconify-icon icon="solar:cart-linear"></iconify-icon>
                </button>
                <button class="add_to_wishlist_btn">
                    <iconify-icon icon="solar:heart-linear"></iconify-icon>
                </button>
            </div>
        </div>
        <div class="product_layout_box">
            <div class="product_layout_img">
                <a href="#">
                    <img src="{{ asset('images/product_dummy.png') }}" alt="">
                </a>
            </div>
            <div class="product_layout_content">
                <h5>ONESET SPORT</h5>
                <div class="product_layout_price">
                    <p>Rp. 110.000</p>
                </div>
            </div>
            <div class="product_layout_button">
                <a href="#">
                    <button class="buy_now_btn">BELI SEKARANG</button>
                </a>
                <button class="add_to_cart_btn">
                    <iconify-icon icon="solar:cart-linear"></iconify-icon>
                </button>
                <button class="add_to_wishlist_btn">
                    <iconify-icon icon="solar:heart-linear"></iconify-icon>
                </button>
            </div>
        </div>
        <div class="product_layout_box">
            <div class="product_layout_img">
                <a href="#">
                    <img src="{{ asset('images/product_dummy.png') }}" alt="">
                </a>
            </div>
            <div class="product_layout_content">
                <h5>ONESET SPORT</h5>
                <div class="product_layout_price">
                    <p>Rp. 110.000</p>
                </div>
            </div>
            <div class="product_layout_button">
                <a href="#">
                    <button class="buy_now_btn">BELI SEKARANG</button>
                </a>
                <button class="add_to_cart_btn">
                    <iconify-icon icon="solar:cart-linear"></iconify-icon>
                </button>
                <button class="add_to_wishlist_btn">
                    <iconify-icon icon="solar:heart-linear"></iconify-icon>
                </button>
            </div>
        </div>
    </div>
</div>

        {{-- KATEGORI --}}
        @if ($categories->isNotEmpty())
            <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-slate-900">Kategori Populer</h2>
                    <a href="{{ route('customer.products.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">Lihat Semua →</a>
                </div>
                <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                    @foreach ($categories as $category)
                        <a href="{{ route('customer.categories.show', $category) }}" 
                           class="group rounded-2xl border border-slate-200 bg-white p-4 text-center transition hover:shadow-md">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-2xl group-hover:bg-blue-100">
                                📁
                            </div>
                            <p class="mt-2 text-sm font-medium text-slate-700 line-clamp-1">{{ $category->name }}</p>
                            <p class="text-xs text-slate-400">{{ $category->products_count }} produk</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- PRODUK TERBARU --}}
        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-slate-900">Produk Terbaru</h2>
                <a href="{{ route('customer.products.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">Lihat Semua →</a>
            </div>
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @forelse ($latestProducts as $product)
                    <div class="group rounded-2xl border border-slate-200 bg-white p-3 transition hover:shadow-lg">
                        <a href="{{ route('customer.products.show', $product) }}" class="block">
                            <div class="aspect-square overflow-hidden rounded-xl bg-slate-100">
                                @if ($product->images->first())
                                    <img src="{{ Storage::url($product->images->first()->image) }}" 
                                        alt="{{ $product->name }}" 
                                        class="h-full w-full object-cover transition group-hover:scale-105">
                                @else
                                    <div class="flex h-full items-center justify-center text-4xl text-slate-300">📦</div>
                                @endif
                            </div>
                            <div class="mt-3">
                                <p class="text-xs text-slate-400">{{ $product->category->name ?? 'Tanpa Kategori' }}</p>
                                <h3 class="text-sm font-semibold text-slate-900 line-clamp-1">{{ $product->name }}</h3>
                                <div class="mt-1 flex items-center justify-between">
                                    <span class="font-bold text-slate-900">{{ $product->price_formatted }}</span>
                                    @if ($product->stock > 0)
                                        <span class="text-xs text-emerald-600">Tersedia</span>
                                    @else
                                        <span class="text-xs text-red-500">Habis</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                        
                        {{-- 🔥 TOMBOL TAMBAH KE KERANJANG --}}
                        @if ($product->stock > 0)
                            <button type="button" 
                                    onclick="openVariantModal({{ $product->id }})"
                                    class="mt-3 w-full rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                                🛒 Tambah ke Keranjang
                            </button>
                        @else
                            <button type="button" 
                                    disabled
                                    class="mt-3 w-full rounded-xl bg-slate-300 px-4 py-2 text-sm font-medium text-slate-500 cursor-not-allowed">
                                Stok Habis
                            </button>
                        @endif
                    </div>
                @empty
                    <p class="col-span-full text-center text-slate-500">Belum ada produk tersedia.</p>
                @endforelse
            </div>
        </section>

        {{-- BEST SELLER --}}
        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-slate-900">Produk Unggulan</h2>
                <a href="{{ route('customer.products.index', ['featured' => 1]) }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">Lihat Semua →</a>
            </div>

            @if ($bestSellers->isNotEmpty())
                <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($bestSellers as $product)
                        <div class="group rounded-2xl border border-slate-200 bg-white p-3 transition hover:shadow-lg relative">
                            {{-- Label Unggulan --}}
                            <div class="absolute left-3 top-3 z-10 rounded-full bg-amber-500 px-2.5 py-0.5 text-xs font-semibold text-white shadow">
                                Unggulan
                            </div>

                            <a href="{{ route('customer.products.show', $product) }}" class="block">
                                <div class="aspect-square overflow-hidden rounded-xl bg-slate-100">
                                    @if ($product->images->first())
                                        <img src="{{ Storage::url($product->images->first()->image) }}" 
                                            alt="{{ $product->name }}" 
                                            class="h-full w-full object-cover transition group-hover:scale-105">
                                    @else
                                        <div class="flex h-full items-center justify-center text-4xl text-slate-300">📦</div>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <p class="text-xs text-slate-400">{{ $product->category->name ?? 'Tanpa Kategori' }}</p>
                                    <h3 class="text-sm font-semibold text-slate-900 line-clamp-1">{{ $product->name }}</h3>
                                    <div class="mt-1 flex items-center justify-between">
                                        <span class="font-bold text-slate-900">{{ $product->price_formatted }}</span>
                                        @if ($product->stock > 0)
                                            <span class="text-xs text-emerald-600">Tersedia</span>
                                        @else
                                            <span class="text-xs text-red-500">Habis</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center">
                    <p class="text-slate-500">Belum ada produk unggulan.</p>
                </div>
            @endif
        </section>

        <script>
            // ============================================
            // TAMBAH KE KERANJANG VIA AJAX
            // ============================================
            function addToCart(productId, variantId = null, quantity = 1) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                
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
                        // Update badge cart
                        const cartCount = document.getElementById('cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.count || 0;
                        }
                        
                        // Buka popup cart
                        if (typeof openCartPopup === 'function') {
                            openCartPopup();
                        }
                        
                        // Tampilkan notifikasi
                        showToast(data.message || 'Produk ditambahkan ke keranjang!', 'success');
                    } else {
                        showToast(data.message || 'Gagal menambahkan produk', 'error');
                    }
                })
                .catch(() => {
                    showToast('Terjadi kesalahan', 'error');
                });
            }
        </script>

@endsection