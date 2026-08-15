
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    {{-- Meta Description --}}
    @yield('meta_description')

    @stack('head')

    <style>
        /* ============================================
           RESET & BASE
           ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f8fafc;
        }

        /* ============================================
           TOAST CONTAINER
           ============================================ */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
            width: 100%;
        }

        .toast {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            background: #1e293b;
            color: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            animation: slideInRight 0.4s ease forwards;
            font-size: 14px;
        }

        .toast.hide {
            animation: slideOutRight 0.3s ease forwards;
        }

        .toast-success {
            background: #10b981;
        }
        .toast-error {
            background: #ef4444;
        }
        .toast-warning {
            background: #f59e0b;
        }
        .toast-info {
            background: #3b82f6;
        }

        .toast-close {
            margin-left: auto;
            cursor: pointer;
            font-size: 20px;
            line-height: 1;
            opacity: 0.7;
            transition: opacity 0.2s;
        }
        .toast-close:hover {
            opacity: 1;
        }

        @keyframes slideInRight {
            0% {
                transform: translateX(100%);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            0% {
                transform: translateX(0);
                opacity: 1;
            }
            100% {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        /* ============================================
           OVERLAY
           ============================================ */
        .popup-overlay {
            position: fixed;
            inset: 0;
            z-index: 50;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .popup-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        /* ============================================
           POPUP SLIDE (CART & WISHLIST)
           ============================================ */
        .popup-slide {
            position: fixed;
            top: 0;
            right: 0;
            z-index: 51;
            height: 100vh;
            width: 100vw;
            max-width: 440px;
            background: #ffffff;
            box-shadow: -8px 0 30px rgba(0, 0, 0, 0.15);
            transform: translateX(130%);
            transition:.3s all;
            display: flex;
            flex-direction: column;
        }

        .popup-slide.active {
            transform: translateX(0);
        }

        /* ============================================
           POPUP HEADER
           ============================================ */
        .popup-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        .popup-header h2 {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .popup-header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .popup-header-actions .btn-clear {
            font-size: 13px;
            color: #ef4444;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 8px;
            transition: background 0.2s;
            display: none;
            align-items: center;
            gap: 4px;
        }

        .popup-header-actions .btn-clear:hover {
            background: #fef2f2;
        }

        .popup-header-actions .btn-clear.visible {
            display: inline-flex;
        }

        .btn-close {
            background: none;
            border: none;
            padding: 8px;
            cursor: pointer;
            color: #94a3b8;
            transition: color 0.2s;
            font-size: 24px;
            line-height: 1;
        }

        .btn-close:hover {
            color: #475569;
        }

        /* ============================================
           POPUP BODY
           ============================================ */
        .popup-body {
            flex: 1;
            overflow-y: auto;
            padding: 16px 20px;
        }

        .popup-body-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #94a3b8;
            text-align: center;
        }

        .popup-body-empty iconify-icon {
            font-size: 64px;
            color: #cbd5e1;
            margin-bottom: 12px;
        }

        .popup-body-empty p:first-of-type {
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
        }

        .popup-body-empty p:last-of-type {
            font-size: 14px;
            color: #94a3b8;
        }

        /* ============================================
           CART ITEMS
           ============================================ */
        .cart-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-image {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
            background: #f1f5f9;
        }

        .cart-item-info {
            flex: 1;
            min-width: 0;
        }

        .cart-item-name {
            font-weight: 600;
            font-size: 14px;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cart-item-variant {
            font-size: 12px;
            color: #94a3b8;
        }

        .cart-item-price {
            font-weight: 700;
            font-size: 15px;
            color: #0f172a;
        }

        .cart-item-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 6px;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid #e2e8f0;
            background: #fff;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            color: #475569;
        }

        .qty-btn:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        .qty-input {
            width: 40px;
            text-align: center;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 4px 0;
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            background: #fff;
        }

        .qty-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .btn-remove {
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            padding: 4px;
            transition: color 0.2s;
            font-size: 18px;
        }

        .btn-remove:hover {
            color: #ef4444;
        }

        /* ============================================
           POPUP FOOTER
           ============================================ */
        .popup-footer {
            border-top: 1px solid #e2e8f0;
            padding: 16px 20px;
            background: #f8fafc;
            flex-shrink: 0;
            display: none;
        }

        .popup-footer.visible {
            display: block;
        }

        .popup-footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .popup-footer-total-label {
            font-size: 13px;
            color: #64748b;
        }

        .popup-footer-total {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .popup-footer-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-outline {
            padding: 10px 18px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background: #fff;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline:hover {
            background: #f1f5f9;
        }

        .btn-primary {
            padding: 10px 24px;
            border-radius: 12px;
            border: none;
            background: #2563eb;
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        /* ============================================
           WISHLIST ITEMS
           ============================================ */
        .wishlist-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .wishlist-item:last-child {
            border-bottom: none;
        }

        .wishlist-item-image {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
            background: #f1f5f9;
        }

        .wishlist-item-info {
            flex: 1;
            min-width: 0;
        }

        .wishlist-item-name {
            font-weight: 600;
            font-size: 14px;
            color: #0f172a;
        }

        .wishlist-item-price {
            font-weight: 700;
            font-size: 15px;
            color: #0f172a;
        }

        .wishlist-item-actions {
            display: flex;
            gap: 8px;
            margin-top: 6px;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-sm-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-sm-primary:hover {
            background: #1d4ed8;
        }

        .btn-sm-danger {
            background: #fef2f2;
            color: #ef4444;
        }

        .btn-sm-danger:hover {
            background: #fee2e2;
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 480px) {
            .popup-slide {
                max-width: 100vw;
            }

            .popup-header {
                padding: 14px 16px;
            }

            .popup-body {
                padding: 12px 16px;
            }

            .popup-footer {
                padding: 14px 16px;
            }

            .popup-footer-inner {
                flex-direction: column;
                gap: 12px;
                align-items: stretch;
            }

            .popup-footer-buttons {
                justify-content: center;
            }

            .cart-item-image,
            .wishlist-item-image {
                width: 52px;
                height: 52px;
            }
        }

        /* ============================================
           UTILITY
           ============================================ */
        .hidden {
            display: none !important;
        }

        .text-center {
            text-align: center;
        }

        .mt-2 {
            margin-top: 8px;
        }

        .text-red-500 {
            color: #ef4444;
        }

        .text-blue-600 {
            color: #2563eb;
        }

        .text-sm {
            font-size: 13px;
        }
    </style>
</head>

<body>

    <div id="toast-container" class="toast-container"></div>

    @include('customer.partials.navbar')
    @include('customer.partials.variant-modal')

    <main>
        @yield('content')
    </main>

    {{-- ============================================ --}}
    {{-- POPUP CART SLIDE --}}
    {{-- ============================================ --}}
    <div id="cart-overlay" class="popup-overlay"></div>

    <div id="cart-popup" class="popup-slide">
        {{-- Header --}}
        <div class="popup-header">
            <h2>🛒 Keranjang Belanja</h2>
            <div class="popup-header-actions">
                <button id="cart-clear" class="btn-clear">
                    <iconify-icon icon="mdi:delete-outline" width="18"></iconify-icon>
                    Kosongkan
                </button>
                <button id="cart-close" class="btn-close">✕</button>
            </div>
        </div>

        {{-- Body --}}
        <div id="cart-body" class="popup-body">
            <div id="cart-content">
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
                    <p class="popup-footer-total-label">Total</p>
                    <p id="cart-total" class="popup-footer-total">Rp 0</p>
                </div>
                <div class="popup-footer-buttons">
                    <a href="{{ route('customer.cart.index') }}" class="btn-outline">Lihat</a>
                    <a href="{{ route('customer.checkout.index') }}" class="btn-primary">Checkout →</a>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- POPUP WISHLIST SLIDE --}}
    {{-- ============================================ --}}
    <div id="wishlist-overlay" class="popup-overlay"></div>

    <div id="wishlist-popup" class="popup-slide">
        {{-- Header --}}
        <div class="popup-header">
            <h2>❤️ Wishlist</h2>
            <div class="popup-header-actions">
                <button id="wishlist-close" class="btn-close">✕</button>
            </div>
        </div>

        {{-- Body --}}
        <div id="wishlist-body" class="popup-body">
            <div id="wishlist-content">
                <div class="popup-body-empty">
                    <iconify-icon icon="mdi:heart-outline"></iconify-icon>
                    <p>Wishlist kosong</p>
                    <p>Simpan produk favoritmu di sini!</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SCRIPT --}}
    {{-- ============================================ --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        var swiper = new Swiper(".mySwiper", {
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
        document.addEventListener('DOMContentLoaded', function() {

            // ============================================
            // TOAST SYSTEM
            // ============================================
            window.showToast = function(message, type, duration) {
                type = type || 'info';
                duration = duration || 4000;

                var container = document.getElementById('toast-container');
                if (!container) return;

                var toast = document.createElement('div');
                toast.className = 'toast toast-' + type;

                var icons = {
                    success: '✅',
                    error: '❌',
                    warning: '⚠️',
                    info: 'ℹ️'
                };

                toast.innerHTML = `
                    <span>${icons[type] || 'ℹ️'}</span>
                    <span>${message}</span>
                    <span class="toast-close">&times;</span>
                `;

                container.appendChild(toast);

                toast.querySelector('.toast-close').addEventListener('click', function() {
                    closeToast(toast);
                });

                setTimeout(function() {
                    closeToast(toast);
                }, duration);
            };

            function closeToast(toast) {
                if (!toast || toast.classList.contains('hide')) return;
                toast.classList.add('hide');
                setTimeout(function() {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 300);
            }

            // ============================================
            // CART POPUP
            // ============================================
            var cartPopup = document.getElementById('cart-popup');
            var cartOverlay = document.getElementById('cart-overlay');
            var cartClose = document.getElementById('cart-close');
            var cartClear = document.getElementById('cart-clear');
            var cartContent = document.getElementById('cart-content');
            var cartFooter = document.getElementById('cart-footer');
            var cartTotal = document.getElementById('cart-total');

            var isCartOpen = false;

            function openCartPopup() {
                isCartOpen = true;
                cartPopup.classList.add('active');
                cartOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
                loadCartContent();
            }

            function closeCartPopup() {
                isCartOpen = false;
                cartPopup.classList.remove('active');
                cartOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            function loadCartContent() {
                fetch('{{ route("customer.cart.popup") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.html) {
                        cartContent.innerHTML = data.html;
                        cartFooter.classList.add('visible');
                        cartClear.classList.add('visible');

                        if (data.total) {
                            cartTotal.textContent = 'Rp ' + formatNumber(data.total);
                        }

                        var cartCount = document.getElementById('cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.count || 0;
                        }

                        attachCartEvents();
                    } else {
                        cartContent.innerHTML = `
                            <div class="popup-body-empty">
                                <iconify-icon icon="mdi:cart-outline"></iconify-icon>
                                <p>Keranjang kosong</p>
                                <p>Yuk, mulai belanja!</p>
                            </div>
                        `;
                        cartFooter.classList.remove('visible');
                        cartClear.classList.remove('visible');
                    }
                })
                .catch(function() {
                    cartContent.innerHTML = `
                        <div class="popup-body-empty">
                            <p class="text-red-500">Gagal memuat keranjang</p>
                            <button onclick="loadCartContent()" class="btn-sm btn-sm-primary" style="margin-top:8px;">Coba Lagi</button>
                        </div>
                    `;
                });
            }

            // ============================================
            // CLEAR CART
            // ============================================
            function clearCart() {
                if (!confirm('Kosongkan semua item di keranjang?')) return;

                var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                fetch('{{ route("customer.cart.clear") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success) {
                        var cartCount = document.getElementById('cart-count');
                        if (cartCount) {
                            cartCount.textContent = 0;
                        }
                        loadCartContent();
                        window.showToast('Keranjang berhasil dikosongkan', 'success');
                    } else {
                        window.showToast(data.message || 'Gagal mengosongkan keranjang', 'error');
                    }
                })
                .catch(function() {
                    window.showToast('Terjadi kesalahan saat mengosongkan keranjang', 'error');
                });
            }

            // ============================================
            // FORMAT NUMBER
            // ============================================
            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // ============================================
            // ATTACH CART EVENTS
            // ============================================
            function attachCartEvents() {
                // Quantity buttons
                document.querySelectorAll('#cart-content .qty-btn').forEach(function(button) {
                    button.addEventListener('click', function() {
                        var input = this.closest('.cart-item-actions').querySelector('.qty-input');
                        if (!input) return;
                        var value = parseInt(input.value) || 1;
                        var action = this.dataset.action;
                        if (action === 'increase') value += 1;
                        else if (action === 'decrease' && value > 1) value -= 1;
                        if (value < 1) return;
                        input.value = value;
                        updateCartItem(input.dataset.key, value);
                    });
                });

                // Quantity input change
                document.querySelectorAll('#cart-content .qty-input').forEach(function(input) {
                    input.addEventListener('change', function() {
                        var value = parseInt(this.value) || 1;
                        if (value < 1) value = 1;
                        this.value = value;
                        updateCartItem(this.dataset.key, value);
                    });
                });

                // Remove item
                document.querySelectorAll('#cart-content .btn-remove').forEach(function(button) {
                    button.addEventListener('click', function() {
                        if (!confirm('Hapus item ini?')) return;
                        var key = this.dataset.key;
                        removeCartItem(key);
                    });
                });
            }

            // ============================================
            // UPDATE CART ITEM
            // ============================================
            function updateCartItem(key, quantity) {
                var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                fetch('{{ route("customer.cart.update") }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ key: key, quantity: quantity })
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success) {
                        loadCartContent();
                        var cartCount = document.getElementById('cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.count || 0;
                        }
                    } else {
                        alert(data.message || 'Gagal update keranjang');
                        loadCartContent();
                    }
                })
                .catch(function() {
                    loadCartContent();
                });
            }

            // ============================================
            // REMOVE CART ITEM
            // ============================================
            function removeCartItem(key) {
                var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                fetch('{{ route("customer.cart.remove") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ key: key })
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success) {
                        loadCartContent();
                        var cartCount = document.getElementById('cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.count || 0;
                        }
                    }
                })
                .catch(function() {
                    loadCartContent();
                });
            }

            // ============================================
            // CART EVENT LISTENERS
            // ============================================
            document.getElementById('cart-toggle')?.addEventListener('click', function(e) {
                e.preventDefault();
                openCartPopup();
            });

            if (cartClose) {
                cartClose.addEventListener('click', closeCartPopup);
            }

            if (cartOverlay) {
                cartOverlay.addEventListener('click', closeCartPopup);
            }

            if (cartClear) {
                cartClear.addEventListener('click', clearCart);
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && isCartOpen) {
                    closeCartPopup();
                }
            });

            // ============================================
            // WISHLIST POPUP
            // ============================================
            var wishlistPopup = document.getElementById('wishlist-popup');
            var wishlistOverlay = document.getElementById('wishlist-overlay');
            var wishlistClose = document.getElementById('wishlist-close');
            var wishlistContent = document.getElementById('wishlist-content');

            var isWishlistOpen = false;

            function openWishlistPopup() {
                isWishlistOpen = true;
                wishlistPopup.classList.add('active');
                wishlistOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
                loadWishlistContent();
            }

            function closeWishlistPopup() {
                isWishlistOpen = false;
                wishlistPopup.classList.remove('active');
                wishlistOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            function loadWishlistContent() {
                fetch('{{ route("customer.wishlist.popup") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.html) {
                        wishlistContent.innerHTML = data.html;
                        var wishlistCount = document.getElementById('wishlist-count');
                        if (wishlistCount) {
                            wishlistCount.textContent = data.count || 0;
                        }
                        attachWishlistEvents();
                    } else {
                        wishlistContent.innerHTML = `
                            <div class="popup-body-empty">
                                <iconify-icon icon="mdi:heart-outline"></iconify-icon>
                                <p>Wishlist kosong</p>
                                <p>Simpan produk favoritmu di sini!</p>
                            </div>
                        `;
                    }
                })
                .catch(function() {
                    wishlistContent.innerHTML = `
                        <div class="popup-body-empty">
                            <p class="text-red-500">Gagal memuat wishlist</p>
                            <button onclick="loadWishlistContent()" class="btn-sm btn-sm-primary" style="margin-top:8px;">Coba Lagi</button>
                        </div>
                    `;
                });
            }

            // ============================================
            // ATTACH WISHLIST EVENTS
            // ============================================
            function attachWishlistEvents() {
                document.querySelectorAll('#wishlist-content .remove-wishlist').forEach(function(button) {
                    button.addEventListener('click', function() {
                        var productId = this.dataset.productId;
                        if (!confirm('Hapus dari wishlist?')) return;
                        removeFromWishlist(productId);
                    });
                });

                document.querySelectorAll('#wishlist-content .add-to-cart-wishlist').forEach(function(button) {
                    button.addEventListener('click', function() {
                        var productId = this.dataset.productId;
                        var variantId = this.dataset.variantId || null;
                        addToCartFromWishlist(productId, variantId);
                    });
                });
            }

            // ============================================
            // REMOVE FROM WISHLIST
            // ============================================
            function removeFromWishlist(productId) {
                var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                fetch('{{ route("customer.wishlist.remove") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success) {
                        loadWishlistContent();
                        var wishlistCount = document.getElementById('wishlist-count');
                        if (wishlistCount) {
                            wishlistCount.textContent = data.count || 0;
                        }
                        window.showToast(data.message || 'Produk dihapus dari wishlist', 'success');
                    } else {
                        window.showToast(data.message || 'Gagal menghapus dari wishlist', 'error');
                    }
                })
                .catch(function() {
                    window.showToast('Terjadi kesalahan', 'error');
                });
            }

            // ============================================
            // ADD TO CART FROM WISHLIST
            // ============================================
            function addToCartFromWishlist(productId, variantId) {
                var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

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
                        quantity: 1
                    })
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success) {
                        var cartCount = document.getElementById('cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.count || 0;
                        }
                        removeFromWishlist(productId);
                        window.showToast('Produk ditambahkan ke keranjang!', 'success');
                    } else {
                        window.showToast(data.message || 'Gagal menambahkan ke keranjang', 'error');
                    }
                })
                .catch(function() {
                    window.showToast('Terjadi kesalahan', 'error');
                });
            }

            // ============================================
            // WISHLIST EVENT LISTENERS
            // ============================================
            document.getElementById('wishlist-toggle')?.addEventListener('click', function(e) {
                e.preventDefault();
                openWishlistPopup();
            });

            if (wishlistClose) {
                wishlistClose.addEventListener('click', closeWishlistPopup);
            }

            if (wishlistOverlay) {
                wishlistOverlay.addEventListener('click', closeWishlistPopup);
            }

            // ============================================
            // SESSION FLASH MESSAGES
            // ============================================
            @if (session('success'))
                window.showToast('{{ session('success') }}', 'success');
            @endif

            @if (session('error'))
                window.showToast('{{ session('error') }}', 'error');
            @endif

            @if (session('warning'))
                window.showToast('{{ session('warning') }}', 'warning');
            @endif

            @if (session('info'))
                window.showToast('{{ session('info') }}', 'info');
            @endif

            // Expose functions
            window.openCartPopup = openCartPopup;
            window.closeCartPopup = closeCartPopup;
            window.loadCartContent = loadCartContent;
            window.clearCart = clearCart;
            window.openWishlistPopup = openWishlistPopup;
            window.closeWishlistPopup = closeWishlistPopup;
            window.loadWishlistContent = loadWishlistContent;

        });
    </script>

</body>
</html>