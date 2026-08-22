<div class="header_section">
    <div class="top_section">
        <div class="text_top_section">
            <p>GRATIS ONGKIR UNTUK PESANAN DI ATAS Rp750.000</p>
        </div>
        <div class="text_top_section">
            <p>DISKON 20% UNTUK PESANAN PERTAMA | KODE: BAROKAH01</p>
        </div>
        <div class="text_top_section">
            <div class="text_top_section_link">
                <a href="{{ route('customer.help') }}">
                    BANTUAN & DUKUNGAN
                </a>
                |
                <a href="{{ route('customer.contact') }}">
                    LOKASI TOKO
                </a>
            </div>
        </div>
    </div>
    <div class="bottom_section">
        {{-- Logo --}}
        <a href="{{ route('customer.home') }}">
            <img src="{{ asset('images/logo.png') }}" alt="">
        </a>

        <ul>
            <a href="/" class="{{ request()->is('/') ? 'active' : '' }}">
                <li>Beranda</li>
            </a>
            <a href="/katalog" class="{{ request()->is('katalog') ? 'active' : '' }}">
                <li>Katalog</li>
            </a>
            <a href="{{ route('customer.products.latest') }}" class="{{ request()->is('katalog/terbaru') ? 'active' : '' }}">
                <li>Produk Terbaru</li>
            </a>
            <a href="{{ route('customer.products.promo') }}" class="{{ request()->is('katalog/promo') ? 'active' : '' }}">
                <li>Promo</li>
            </a>
            <a href="/testimoni" class="{{ request()->is('testimoni') ? 'active' : '' }}">
                <li>Testimoni</li>
            </a>
            <a href="{{ route('customer.articles.index') }}" class="{{ request()->is('artikel') ? 'active' : '' }}">
                <li>Artikel</li>
            </a>
            <a href="{{ route('customer.cara-pesan') }}" class="{{ request()->is('cara-pesan') ? 'active' : '' }}">
                <li>Cara Pesan</li>
            </a>
        </ul>

        {{-- Search --}}
        <!-- <div class="hidden flex-1 max-w-md mx-4 lg:block">
            <form action="{{ route('customer.products.index') }}" method="GET" class="relative">
                <input type="text" name="search" placeholder="Cari produk..." value="{{ request('search') }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 pr-10 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </form>
        </div> -->

        <div class="btn_header_bottom">
            <button onclick="openSearchPopup()">
                <iconify-icon icon="mingcute:search-line"></iconify-icon>
            </button>

            @auth
                @if(auth()->user()->role === 'customer' || auth()->user()->role === null)
                    <a href="{{ route('customer.account') }}">
                        <button>
                            <iconify-icon icon="iconamoon:profile-light"></iconify-icon>
                        </button>
                    </a>
                @else
                    <a href="{{ route('admin.dashboard') }}">
                        <button>
                            <iconify-icon icon="iconamoon:profile-light"></iconify-icon>
                        </button>
                    </a>
                @endif
            @else
                <a href="{{ route('customer.login') }}">
                    <button>
                        <iconify-icon icon="iconamoon:profile-light"></iconify-icon>
                    </button>
                </a>
            @endauth

            <button id="wishlist-toggle" style="position:relative;">
                <iconify-icon icon="mynaui:heart"></iconify-icon>
                <span id="wishlist-count" style="display:none;">0</span>
            </button>

            <button id="cart-toggle" style="position:relative;">
                <iconify-icon icon="solar:cart-linear"></iconify-icon>
                <span id="cart-count" style="display:none;">0</span>
            </button>
        </div>
    </div>
</div>

<script>

    document.addEventListener('DOMContentLoaded', function() {
        // Panggil fungsi dari cart.js
        if (typeof window.loadCartCount === 'function') {
            window.loadCartCount();
        }
        
        if (typeof window.loadWishlistCount === 'function') {
            window.loadWishlistCount();
        }

        if (typeof window.loadWishlistStatus === 'function') {
            setTimeout(function() {
                window.loadWishlistStatus();
            }, 500);
        }
        
        // Jika cart.js belum load, gunakan fallback
        if (typeof window.loadCartCount !== 'function') {
            // Fallback: fetch langsung
            fetch('{{ route("customer.cart.count") }}')
                .then(response => response.json())
                .then(data => {
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.count || 0;
                        cartCount.style.display = (data.count > 0) ? 'inline-flex' : 'none';
                    }
                })
                .catch(() => {});
        }
        
        if (typeof window.loadWishlistCount !== 'function') {
            fetch('{{ route("customer.wishlist.popup") }}')
                .then(response => response.json())
                .then(data => {
                    const wishlistCount = document.getElementById('wishlist-count');
                    if (wishlistCount && data.count !== undefined) {
                        wishlistCount.textContent = data.count || 0;
                        wishlistCount.style.display = (data.count > 0) ? 'inline-flex' : 'none';
                    }
                })
                .catch(() => {});
        }
    });
</script>
