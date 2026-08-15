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
                <a href="#">
                    BANTUAN & DUKUNGAN
                </a>
                |
                <a href="#">
                    LOKASI TOKO
                </a>
            </div>
        </div>
    </div>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            {{-- Logo --}}
            <a href="{{ route('customer.home') }}" class="flex items-center gap-2">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-900 font-bold text-white text-sm">
                    {{ strtoupper(substr(config('app.name'), 0, 1)) }}
                </div>
                <span class="font-bold text-slate-900 hidden sm:inline">{{ config('app.name') }}</span>
            </a>

            {{-- Search --}}
            <div class="hidden flex-1 max-w-md mx-4 lg:block">
                <form action="{{ route('customer.products.index') }}" method="GET" class="relative">
                    <input type="text" name="search" placeholder="Cari produk..." value="{{ request('search') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 pr-10 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>
            </div>

            <div class="flex items-center gap-3 sm:gap-4">
                <button id="wishlist-toggle" class="relative text-slate-600 hover:text-red-500 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span id="wishlist-count" class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">0</span>
                </button>

                <button id="cart-toggle" class="relative text-slate-600 hover:text-slate-900 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span id="cart-count" class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-blue-600 text-[10px] font-bold text-white">0</span>
                </button>

                @auth('customer')
                    <a href="{{ route('customer.account') }}" class="flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-600">
                            {{ strtoupper(substr(auth('customer')->user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden md:inline">{{ auth('customer')->user()->name }}</span>
                    </a>
                @else
                    <a href="{{ route('customer.login') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 hidden sm:inline">Masuk</a>
                    <a href="{{ route('customer.register') }}" class="rounded-lg bg-slate-900 px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-semibold text-white transition hover:bg-slate-800">Daftar</a>
                @endauth
            </div>
        </div>
    </div>
</div>

<script>
    function updateCartCount() {
        fetch('{{ route("customer.cart.count") }}')
            .then(response => response.json())
            .then(data => {
                const cartCount = document.getElementById('cart-count');
                if (cartCount) {
                    cartCount.textContent = data.count || 0;
                }
            })
            .catch(() => {
                // Silent fail
            });
    }

    // Update saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        updateCartCount();
    });

    // Update setiap 30 detik (opsional)
    // setInterval(updateCartCount, 30000);
</script>