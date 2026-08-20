<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
    
    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/popup.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link rel="stylesheet" href="{{ asset('css/variant-modal.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    {{-- Meta Description --}}
    @yield('meta_description')
</head>

<body>

    @include('customer.partials.navbar')
    @include('customer.partials.variant-modal')

    <main>
        @yield('content')
    </main>

    {{-- ============================================ --}}
    {{-- CART POPUP --}}
    {{-- ============================================ --}}
    <div id="cart-popup" class="popup-slide">
        <div class="popup_slide_overlay"></div>
        <div class="popup-slide-box">
            {{-- Header --}}
            <div class="popup-header">
                <h2>
                    <iconify-icon icon="mdi:cart-outline"></iconify-icon>
                    Keranjang Belanja
                </h2>
                <div class="popup-header-actions">
                    <button id="cart-clear" class="btn-clear">
                        <iconify-icon icon="mdi:delete-outline" width="16"></iconify-icon>
                        Kosongkan
                    </button>
                    <button id="cart-close" class="btn-close" aria-label="Tutup">✕</button>
                </div>
            </div>

            {{-- Body --}}
            <div id="cart-body" class="popup-body">
                <div id="cart-content">
                    {{-- Content will be loaded by JavaScript --}}
                    <div class="popup-body-empty">
                        <iconify-icon icon="mdi:cart-outline"></iconify-icon>
                        <p>Keranjang kosong</p>
                        <p>Yuk, mulai belanja!</p>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div id="cart-footer" class="popup-footer">
                <div class="popup-footer-inner">
                    <div>
                        <p class="popup-footer-total-label">Total Belanja</p>
                        <p id="cart-total" class="popup-footer-total">Rp 0</p>
                    </div>
                    <div class="popup-footer-buttons">
                        <a href="{{ route('customer.cart.index') }}" class="btn-outline">
                            <iconify-icon icon="mdi:eye-outline" width="16"></iconify-icon>
                            Lihat
                        </a>
                        <a href="{{ route('customer.checkout.index') }}" class="btn-primary">
                            Checkout
                            <iconify-icon icon="mdi:arrow-right" width="16"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- WISHLIST POPUP --}}
    {{-- ============================================ --}}
    <div id="wishlist-popup" class="popup-slide">
        <div class="popup_slide_overlay"></div>
        <div class="popup-slide-box">
            {{-- Header --}}
            <div class="popup-header">
                <h2>
                    <iconify-icon icon="mdi:heart-outline"></iconify-icon>
                    Wishlist
                </h2>
                <div class="popup-header-actions">
                    <button id="wishlist-clear" class="btn-clear" style="display: none;">
                        <iconify-icon icon="mdi:delete-outline" width="16"></iconify-icon>
                        Kosongkan
                    </button>
                    <button id="wishlist-close" class="btn-close" aria-label="Tutup">✕</button>
                </div>
            </div>

            {{-- Body --}}
            <div id="wishlist-body" class="popup-body">
                <div id="wishlist-content">
                    <div class="popup-body-empty">
                        <iconify-icon icon="mdi:heart-outline"></iconify-icon>
                        <p>Wishlist kosong</p>
                        <p>Simpan produk favoritmu di sini!</p>
                        <a href="{{ route('customer.products.index') }}" class="btn-primary">
                            <iconify-icon icon="mdi:shopping-outline" width="18"></iconify-icon>
                            Mulai Belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('customer.partials.footer')

    {{-- ============================================ --}}
    {{-- SCRIPTS --}}
    {{-- ============================================ --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    {{-- Swiper Initialization --}}
    <script>
        var swiper = new Swiper(".testimonialSwiper", {
            slidesPerView: 1,
            spaceBetween: 10,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            cssMode: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: true,
                pauseOnMouseEnter: true,
            },
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 20 },
                768: { slidesPerView: 2, spaceBetween: 30 },
                1024: { slidesPerView: 3, spaceBetween: 30 },
            },
        });

        var swiper2 = new Swiper(".mySwiper", {
            slidesPerView: 1,
            autoHeight: true,
            loop: true,
            effect: 'fade',
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });
    </script>

    <script>
        var swiperCategories = new Swiper(".categoriesSwiper", {
            slidesPerView: 1,
            spaceBetween: 10,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            cssMode: true,
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 20 },
                768: { slidesPerView: 2, spaceBetween: 30 },
                1024: { slidesPerView: 5, spaceBetween: 30 },
            },
        });
    </script>

    {{-- Routes for JavaScript --}}
    <script>
        window.customerRoutes = {
            cartAdd: '{{ route("customer.cart.add") }}',
            cartPopup: '{{ route("customer.cart.popup") }}',
            cartUpdate: '{{ route("customer.cart.update") }}',
            cartRemove: '{{ route("customer.cart.remove") }}',
            cartClear: '{{ route("customer.cart.clear") }}',
            cartCount: '{{ route("customer.cart.count") }}',
            wishlistPopup: '{{ route("customer.wishlist.popup") }}',
            wishlistStatus: '{{ route("customer.wishlist.status") }}',
            wishlistAdd: '{{ route("customer.wishlist.add") }}',
            wishlistRemove: '{{ route("customer.wishlist.remove") }}',
            wishlistClear: '{{ route("customer.wishlist.clear") }}',
            checkout: '{{ route("customer.checkout.index") }}',
        };
    </script>

    {{-- Popup JavaScript --}}
    <script src="{{ asset('js/cart.js') }}"></script>
    <script src="{{ asset('js/popup.js') }}"></script>
    <script src="{{ asset('js/variant-modal.js') }}"></script>
    

</body>
</html>