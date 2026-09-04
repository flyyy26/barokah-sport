<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $isLoggedIn = Auth::guard('customer')->check() || Auth::check();
        $userId = Auth::guard('customer')->id() ?? Auth::id();
        $userName = Auth::guard('customer')->user()->name ?? Auth::user()->name ?? '';
        $userRole = Auth::guard('customer')->user()->role ?? Auth::user()->role ?? 'customer';
    @endphp

    <!-- Customer Login Status -->
    @if($isLoggedIn)
        <meta name="customer-logged-in" content="true">
        <meta name="user-id" content="{{ $userId }}">
        <meta name="user-name" content="{{ $userName }}">
        <meta name="user-role" content="{{ $userRole }}">
    @else
        <meta name="customer-logged-in" content="false">
        <meta name="user-role" content="guest">
    @endif
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
    
    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/popup.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product_show.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cart-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link rel="stylesheet" href="{{ asset('css/variant-modal.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        /* ============================================
           VOUCHER POPUP (Right Side Slide)
           ============================================ */
        .voucher-popup-overlay {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background: rgba(0, 0, 0, 0.5) !important;
            z-index: 99998 !important;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .voucher-popup-overlay.active {
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto !important;
        }

        .voucher-popup {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            width: 420px !important;
            max-width: 90vw !important;
            height: 100vh !important;
            background: #ffffff !important;
            z-index: 99999 !important;
            transform: translateX(100%) !important;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: -4px 0 25px rgba(0, 0, 0, 0.2) !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .voucher-popup.active {
            transform: translateX(0) !important;
        }

        .voucher-popup-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5vw 1.5vw 1vw;
            border-bottom: 0.1vw solid #e2e8f0;
            flex-shrink: 0;
        }

        .voucher-popup-header h2 {
            font-size: 1.2vw;
            font-weight: 700;
            color: #0f172a;
        }

        .voucher-popup-header .close-popup {
            background: none;
            border: none;
            font-size: 1.5vw;
            color: #94a3b8;
            cursor: pointer;
            padding: 0.2vw 0.5vw;
            border-radius: 0.3vw;
            transition: all 0.2s;
            line-height: 1;
        }

        .voucher-popup-header .close-popup:hover {
            color: #0f172a;
            background: #f1f5f9;
        }

        .voucher-popup-body {
            padding: 1.5vw;
            overflow-y: auto;
            flex: 1;
        }

        /* Voucher Input in Popup */
        .popup-voucher-input-group {
            display: flex;
            gap: 0.5vw;
            margin-bottom: 1vw;
        }

        .popup-voucher-input {
            flex: 1;
            padding: 0.7vw 1vw;
            border: 0.1vw solid #e2e8f0;
            border-radius: 0.7vw;
            font-size: 0.8vw;
            color: #0f172a;
            background: #ffffff;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .popup-voucher-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2vw rgba(59, 130, 246, 0.1);
        }

        .popup-btn-apply {
            padding: 0.7vw 1.5vw;
            background: #0f172a;
            color: #ffffff;
            border: none;
            border-radius: 0.7vw;
            font-size: 0.8vw;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
            white-space: nowrap;
        }

        .popup-btn-apply:hover {
            background: #1e293b;
        }

        .popup-btn-apply:active {
            transform: scale(0.97);
        }

        .popup-btn-apply:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Voucher List in Popup */
        .popup-voucher-list {
            display: flex;
            flex-direction: column;
            gap: 0.6vw;
        }

        .popup-voucher-list .list-title {
            font-size: 0.8vw;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0.2vw;
        }

        .popup-voucher-item {
            padding: 0.8vw 1vw;
            border: 0.1vw solid #e2e8f0;
            border-radius: 0.7vw;
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .popup-voucher-item.applicable {
            border-color: #86efac;
            background: #f0fdf4;
        }

        .popup-voucher-item.applicable:hover {
            border-color: #22c55e;
            box-shadow: 0 0.1vw 0.4vw rgba(34, 197, 94, 0.15);
        }

        .popup-voucher-item.not-applicable {
            opacity: 0.5;
        }

        .popup-voucher-item .item-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5vw;
        }

        .popup-voucher-item .item-info {
            display: flex;
            align-items: center;
            gap: 0.6vw;
            flex-wrap: wrap;
            flex: 1;
        }

        .popup-voucher-item .item-code {
            font-family: monospace;
            font-weight: 700;
            font-size: 0.65vw;
            padding: 0.1vw 0.5vw;
            background: #e2e8f0;
            color: #0f172a;
            border-radius: 0.3vw;
            letter-spacing: 0.05em;
        }

        .popup-voucher-item .item-name {
            font-size: 0.75vw;
            font-weight: 500;
            color: #0f172a;
        }

        .popup-voucher-item .item-discount {
            font-size: 0.7vw;
            font-weight: 600;
            color: #16a34a;
        }

        .popup-voucher-item .item-min {
            font-size: 0.6vw;
            color: #94a3b8;
        }

        .popup-voucher-item .btn-use {
            padding: 0.25vw 0.8vw;
            background: #22c55e;
            color: white;
            border: none;
            border-radius: 0.4vw;
            font-size: 0.6vw;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
            white-space: nowrap;
        }

        .popup-voucher-item .btn-use:hover {
            background: #16a34a;
            transform: scale(1.05);
        }

        .popup-voucher-item .btn-use:active {
            transform: scale(0.95);
        }

        .popup-voucher-item .status-unavailable {
            font-size: 0.6vw;
            color: #94a3b8;
            white-space: nowrap;
        }

        .popup-empty {
            text-align: center;
            padding: 2vw 0;
            color: #94a3b8;
            font-size: 0.8vw;
        }
    </style>
    
    {{-- Meta Description --}}
    @yield('meta_description')
</head>

<body>

    @include('customer.partials.navbar')
    @include('customer.partials.variant-modal')
    @include('customer.partials.search-popup')

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
                        <iconify-icon icon="mdi:delete-outline"></iconify-icon>
                        Kosongkan
                    </button>
                    <button id="wishlist-toggle" class="icon-btn icon-btn-wishlist" style="position:relative;" aria-label="Wishlist">
                        <iconify-icon icon="mynaui:heart"></iconify-icon> Wishlist
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
                        <button onclick="goToCheckout()" class="btn-primary" style="border:none;cursor:pointer;">
                            Checkout
                            <iconify-icon icon="mdi:arrow-right" width="16"></iconify-icon>
                        </button>
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
                        <iconify-icon icon="mdi:delete-outline"></iconify-icon>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================
    VOUCHER POPUP (Right Side)
    ============================================ --}}
    <div class="voucher-popup-overlay" id="voucher-popup-overlay"></div>
    <div class="voucher-popup" id="voucher-popup">
        <div class="voucher-popup-header">
            <h2>Pilih Voucher</h2>
            <button type="button" class="close-popup" id="close-voucher-popup">✕</button>
        </div>
        <div class="voucher-popup-body">
            {{-- Input Kode Voucher --}}
            <div class="popup-voucher-input-group">
                <input type="text" 
                    id="popup-voucher-input" 
                    placeholder="Masukkan kode promo..." 
                    class="popup-voucher-input"
                    maxlength="50">
                <button type="button" class="popup-btn-apply" id="btn-apply-manual-voucher">
                    Pakai
                </button>
            </div>

            {{-- Daftar Voucher Tersedia --}}
            <div class="popup-voucher-list" id="popup-voucher-list">
                <p class="list-title">Voucher tersedia untuk kamu:</p>
                {{-- Will be populated by JavaScript --}}
                <div id="popup-voucher-items">
                    {{-- Voucher items loaded via AJAX --}}
                </div>
            </div>
        </div>
    </div>

    @include('customer.partials.login-popup')

    @include('customer.partials.footer')

    {{-- ============================================ --}}
    {{-- SCRIPTS --}}
    {{-- ============================================ --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        function goToCheckout() {
            // 🔥 CEK APAKAH ADA ITEM DI CART
            const cartCount = parseInt(document.getElementById('cart-count')?.textContent || 0);
            
            console.log('🛒 goToCheckout called, cart count:', cartCount);
            
            if (cartCount === 0) {
                showToast('Keranjang kosong. Tambahkan produk terlebih dahulu.', 'warning');
                return;
            }
            
            // 🔥 TUTUP POPUP
            const popup = document.getElementById('cart-popup');
            if (popup) {
                popup.classList.remove('active');
                document.body.classList.remove('popup-open');
            }
            
            // 🔥 REDIRECT KE CHECKOUT
            const checkoutUrl = window.customerRoutes.checkout || '{{ route("customer.checkout.index") }}';
            console.log('🛒 Redirecting to checkout:', checkoutUrl);
            window.location.href = checkoutUrl;
        }

        // Expose ke global
        window.goToCheckout = goToCheckout;
    </script>

    <script>
        // ============================================
        // LOGIN POPUP FUNCTIONS
        // ============================================

        let loginPopupCallback = null;
        let loginPopupAction = null;

        function openLoginPopup(action, callback) {
            const overlay = document.getElementById('login-popup-overlay');
            if (!overlay) return;

            // Reset semua state
            document.getElementById('login-popup-login-form').style.display = 'block';
            document.getElementById('login-popup-register-form').style.display = 'none';
            document.getElementById('login-popup-success').style.display = 'none';
            document.getElementById('login-popup-error').style.display = 'none';
            document.getElementById('register-popup-error').style.display = 'none';

            // Reset form login
            document.getElementById('login-popup-email').value = '';
            document.getElementById('login-popup-password').value = '';
            document.getElementById('login-popup-remember').checked = false;
            
            // Reset form register
            document.getElementById('register-popup-name').value = '';
            document.getElementById('register-popup-email').value = '';
            document.getElementById('register-popup-password').value = '';
            document.getElementById('register-popup-password-confirm').value = '';
            document.getElementById('register-popup-terms').checked = false;
            
            const loginBtn = document.getElementById('login-popup-btn');
            loginBtn.disabled = false;
            loginBtn.querySelector('.spinner').style.display = 'none';
            loginBtn.querySelector('.btn-text').textContent = 'Login';

            const registerBtn = document.getElementById('register-popup-btn');
            registerBtn.disabled = false;
            registerBtn.querySelector('.spinner').style.display = 'none';
            registerBtn.querySelector('.btn-text').textContent = 'Daftar Sekarang';

            // Simpan callback dan action
            loginPopupCallback = callback;
            loginPopupAction = action;

            // Tampilkan popup
            overlay.style.display = 'flex';
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Focus ke input email
            setTimeout(function() {
                document.getElementById('login-popup-email').focus();
            }, 300);
        }

        function closeLoginPopup() {
            const overlay = document.getElementById('login-popup-overlay');
            if (overlay) {
                overlay.classList.remove('active');
                setTimeout(function() {
                    overlay.style.display = 'none';
                }, 300);
                document.body.style.overflow = '';
            }
        }

        function showLoginSuccess(message, username) {
            // Sembunyikan form login dan register
            document.getElementById('login-popup-login-form').style.display = 'none';
            document.getElementById('login-popup-register-form').style.display = 'none';
            document.getElementById('login-popup-error').style.display = 'none';
            document.getElementById('register-popup-error').style.display = 'none';
            
            // Tampilkan state sukses
            const successDiv = document.getElementById('login-popup-success');
            successDiv.style.display = 'block';
            
            // Set title dan message
            const titleEl = document.getElementById('login-popup-success-title');
            const msgEl = document.getElementById('login-popup-success-message');
            
            if (message === 'login') {
                titleEl.textContent = 'Login Berhasil!';
                msgEl.textContent = 'Selamat datang, ' + (username || '') + '!';
            } else if (message === 'register') {
                titleEl.textContent = 'Registrasi Berhasil!';
                msgEl.textContent = 'Selamat datang, ' + (username || '') + '!';
            }
            
            // 🔥 LOAD ULANG WISHLIST STATUS UNTUK SEMUA PRODUK
            loadWishlistStatus();
            
            // 🔥 LOAD ULANG CART COUNT
            loadCartCount();
            
            // Auto close setelah 1.5 detik
            setTimeout(function() {
                closeLoginPopup();
                // Reset success state setelah popup tertutup
                setTimeout(function() {
                    successDiv.style.display = 'none';
                    document.getElementById('login-popup-login-form').style.display = 'block';
                }, 300);
            }, 1500);
        }

        function submitLoginPopup(event) {
            event.preventDefault();

            const email = document.getElementById('login-popup-email').value;
            const password = document.getElementById('login-popup-password').value;
            const remember = document.getElementById('login-popup-remember').checked;
            const btn = document.getElementById('login-popup-btn');
            const errorDiv = document.getElementById('login-popup-error');
            const errorMessage = document.getElementById('login-popup-error-message');

            errorDiv.style.display = 'none';

            btn.disabled = true;
            btn.querySelector('.spinner').style.display = 'block';
            btn.querySelector('.btn-text').textContent = 'Memproses...';

            fetch('{{ route("customer.login.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    email: email,
                    password: password,
                    remember: remember
                })
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.querySelector('.spinner').style.display = 'none';
                btn.querySelector('.btn-text').textContent = 'Login';

                if (data.success) {
                    // 🔥 TAMPILKAN STATE SUKSES (DI DALAMNYA ADA loadWishlistStatus)
                    showLoginSuccess('login', data.user?.name || '');

                    // UPDATE CSRF TOKEN
                    const metaToken = document.querySelector('meta[name="csrf-token"]');
                    if (metaToken) {
                        metaToken.content = data.csrf_token || metaToken.content;
                    }
                    
                    if (typeof $.ajaxSetup === 'function') {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': data.csrf_token || metaToken?.content || ''
                            }
                        });
                    }

                    // 🔥 LOAD ULANG CART & WISHLIST COUNT (SUDAH DI showLoginSuccess)
                    // loadCartCount(); // SUDAH DI showLoginSuccess
                    // loadWishlistStatus(); // SUDAH DI showLoginSuccess

                    document.dispatchEvent(new CustomEvent('login-success'));

                    const action = loginPopupAction;
                    const callback = loginPopupCallback;

                    loginPopupCallback = null;
                    loginPopupAction = null;

                    // 🔥 EKSEKUSI ACTION SETELAH POPUP TERTUTUP
                    setTimeout(function() {
                        if (typeof callback === 'function') {
                            callback();
                        } else if (action === 'add_to_cart') {
                            if (window._pendingProductId) {
                                fetch('/api/products/' + window._pendingProductId + '/variants', {
                                    headers: { 'Accept': 'application/json' }
                                })
                                .then(function(response) { return response.json(); })
                                .then(function(data) {
                                    if (data.success && data.variants && data.variants.length > 0) {
                                        if (typeof openVariantModal === 'function') {
                                            openVariantModal(window._pendingProductId, 'add_to_cart');
                                            window._pendingProductId = null;
                                        }
                                    } else {
                                        addToCartDirect(window._pendingProductId);
                                        window._pendingProductId = null;
                                    }
                                })
                                .catch(function() {
                                    addToCartDirect(window._pendingProductId);
                                    window._pendingProductId = null;
                                });
                            }
                        } else if (action === 'add_to_wishlist') {
                            if (window._pendingProductId) {
                                addToWishlistDirect(window._pendingProductId);
                                window._pendingProductId = null;
                            }
                        }
                    }, 2000);
                } else {
                    errorMessage.textContent = data.message || 'Email atau password salah';
                    errorDiv.style.display = 'flex';
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.querySelector('.spinner').style.display = 'none';
                btn.querySelector('.btn-text').textContent = 'Login';
                errorMessage.textContent = 'Terjadi kesalahan, silakan coba lagi';
                errorDiv.style.display = 'flex';
                console.error('Login error:', error);
            });
        }

        function switchToRegister() {
            document.getElementById('login-popup-login-form').style.display = 'none';
            document.getElementById('login-popup-register-form').style.display = 'block';
            document.getElementById('register-popup-error').style.display = 'none';
            
            // Reset form register
            document.getElementById('register-popup-name').value = '';
            document.getElementById('register-popup-email').value = '';
            document.getElementById('register-popup-password').value = '';
            document.getElementById('register-popup-password-confirm').value = '';
            document.getElementById('register-popup-terms').checked = false;
            
            const btn = document.getElementById('register-popup-btn');
            btn.disabled = false;
            btn.querySelector('.spinner').style.display = 'none';
            btn.querySelector('.btn-text').textContent = 'Daftar Sekarang';

            // Focus ke name
            setTimeout(function() {
                document.getElementById('register-popup-name').focus();
            }, 300);
        }

        function switchToLogin() {
            document.getElementById('login-popup-register-form').style.display = 'none';
            document.getElementById('login-popup-login-form').style.display = 'block';
            document.getElementById('login-popup-error').style.display = 'none';
            
            // Reset form login
            document.getElementById('login-popup-email').value = '';
            document.getElementById('login-popup-password').value = '';
            document.getElementById('login-popup-remember').checked = false;
            
            const btn = document.getElementById('login-popup-btn');
            btn.disabled = false;
            btn.querySelector('.spinner').style.display = 'none';
            btn.querySelector('.btn-text').textContent = 'Login';

            // Focus ke email
            setTimeout(function() {
                document.getElementById('login-popup-email').focus();
            }, 300);
        }

        // ============================================
        // TOGGLE PASSWORD VISIBILITY
        // ============================================

        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            if (!input) return;
            
            const icon = button.querySelector('iconify-icon');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) icon.setAttribute('icon', 'mdi:eye-off-outline');
            } else {
                input.type = 'password';
                if (icon) icon.setAttribute('icon', 'mdi:eye-outline');
            }
        }

        // ============================================
        // PASSWORD STRENGTH CHECKER
        // ============================================

        function checkPasswordStrength(password) {
            let strength = 0;
            let label = '';
            let className = '';
            
            // Length check
            if (password.length >= 8) strength += 1;
            if (password.length >= 12) strength += 1;
            
            // Contains lowercase
            if (/[a-z]/.test(password)) strength += 1;
            
            // Contains uppercase
            if (/[A-Z]/.test(password)) strength += 1;
            
            // Contains number
            if (/\d/.test(password)) strength += 1;
            
            // Contains special character
            if (/[^a-zA-Z0-9]/.test(password)) strength += 1;
            
            // Determine strength
            if (strength <= 2) {
                label = 'Lemah';
                className = 'weak';
            } else if (strength <= 4) {
                label = 'Sedang';
                className = 'medium';
            } else {
                label = 'Kuat';
                className = 'strong';
            }
            
            return { strength, label, className };
        }

        function updatePasswordStrength() {
            const password = document.getElementById('register-popup-password');
            const strengthDiv = document.getElementById('register-password-strength');
            
            if (!password || !strengthDiv) return;
            
            const value = password.value;
            
            if (value.length === 0) {
                strengthDiv.innerHTML = '';
                return;
            }
            
            const result = checkPasswordStrength(value);
            
            // Create strength bars
            const maxBars = 5;
            const activeBars = Math.min(result.strength, maxBars);
            
            let barsHtml = '';
            for (let i = 0; i < maxBars; i++) {
                const isActive = i < activeBars;
                barsHtml += `<span class="${isActive ? 'active ' + result.className : ''}"></span>`;
            }
            
            strengthDiv.innerHTML = `
                <div class="strength-bar">${barsHtml}</div>
                <div class="strength-text">Kekuatan: <strong>${result.label}</strong></div>
            `;
        }

        function validatePasswordMatch() {
            const password = document.getElementById('register-popup-password');
            const confirm = document.getElementById('register-popup-password-confirm');
            const matchDiv = document.getElementById('register-password-match');
            
            if (!password || !confirm || !matchDiv) return;
            
            const passVal = password.value;
            const confirmVal = confirm.value;
            
            if (confirmVal.length === 0) {
                matchDiv.innerHTML = '';
                return;
            }
            
            if (passVal === confirmVal) {
                matchDiv.innerHTML = '✓ Password cocok';
                matchDiv.className = 'password-match match-success';
            } else {
                matchDiv.innerHTML = '✗ Password tidak cocok';
                matchDiv.className = 'password-match match-error';
            }
        }

        // ============================================
        // EVENT LISTENERS UNTUK VALIDASI PASSWORD
        // ============================================

        document.addEventListener('DOMContentLoaded', function() {
            // Password strength
            const passwordInput = document.getElementById('register-popup-password');
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    updatePasswordStrength();
                    validatePasswordMatch();
                });
            }
            
            // Password match
            const confirmInput = document.getElementById('register-popup-password-confirm');
            if (confirmInput) {
                confirmInput.addEventListener('input', function() {
                    validatePasswordMatch();
                });
            }
        });

        // 🔥 JUGA TAMBAHKAN UNTUK HALAMAN REGISTER (jika ada)
        // Sama seperti di atas, tapi untuk elemen dengan ID yang berbeda
        document.addEventListener('DOMContentLoaded', function() {
            // Untuk halaman register (customer.auth.register)
            const regPassword = document.getElementById('password');
            const regConfirm = document.getElementById('password_confirmation');
            
            if (regPassword && regConfirm) {
                // Toggle password untuk halaman register
                const toggleBtn = document.querySelector('.toggle-password-btn');
                if (toggleBtn) {
                    // sudah ada di HTML
                }
            }
        });

        // ============================================
        // SUBMIT REGISTER POPUP
        // ============================================

        function submitRegisterPopup(event) {
            event.preventDefault();

            const name = document.getElementById('register-popup-name').value;
            const email = document.getElementById('register-popup-email').value;
            const password = document.getElementById('register-popup-password').value;
            const passwordConfirm = document.getElementById('register-popup-password-confirm').value;
            const terms = document.getElementById('register-popup-terms').checked;
            const btn = document.getElementById('register-popup-btn');
            const errorDiv = document.getElementById('register-popup-error');
            const errorMessage = document.getElementById('register-popup-error-message');

            errorDiv.style.display = 'none';

            // Validasi
            if (!name || name.length < 2) {
                errorMessage.textContent = 'Nama lengkap minimal 2 karakter';
                errorDiv.style.display = 'flex';
                return;
            }

            if (!email || !email.includes('@')) {
                errorMessage.textContent = 'Masukkan email yang valid';
                errorDiv.style.display = 'flex';
                return;
            }

            if (!password || password.length < 8) {
                errorMessage.textContent = 'Password minimal 8 karakter';
                errorDiv.style.display = 'flex';
                return;
            }

            if (password !== passwordConfirm) {
                errorMessage.textContent = 'Konfirmasi password tidak cocok';
                errorDiv.style.display = 'flex';
                return;
            }

            if (!terms) {
                errorMessage.textContent = 'Anda harus menyetujui Syarat & Ketentuan';
                errorDiv.style.display = 'flex';
                return;
            }

            btn.disabled = true;
            btn.querySelector('.spinner').style.display = 'block';
            btn.querySelector('.btn-text').textContent = 'Memproses...';

            fetch('{{ route("customer.register.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: name,
                    email: email,
                    password: password,
                    password_confirmation: passwordConfirm,
                    terms: terms ? 1 : 0
                })
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.querySelector('.spinner').style.display = 'none';
                btn.querySelector('.btn-text').textContent = 'Daftar Sekarang';

                if (data.success) {
                    // 🔥 TAMPILKAN STATE SUKSES (DI DALAMNYA ADA loadWishlistStatus)
                    showLoginSuccess('register', data.user?.name || '');

                    // UPDATE CSRF TOKEN
                    const metaToken = document.querySelector('meta[name="csrf-token"]');
                    if (metaToken) {
                        metaToken.content = data.csrf_token || metaToken.content;
                    }

                    // 🔥 LOAD ULANG CART & WISHLIST COUNT (SUDAH DI showLoginSuccess)
                    // loadCartCount(); // SUDAH DI showLoginSuccess
                    // loadWishlistStatus(); // SUDAH DI showLoginSuccess

                    document.dispatchEvent(new CustomEvent('login-success'));

                    // Reset state
                    const action = loginPopupAction;
                    loginPopupCallback = null;
                    loginPopupAction = null;

                    // 🔥 EKSEKUSI ACTION SETELAH POPUP TERTUTUP
                    setTimeout(function() {
                        if (action === 'add_to_cart' && window._pendingProductId) {
                            fetch('/api/products/' + window._pendingProductId + '/variants', {
                                headers: { 'Accept': 'application/json' }
                            })
                            .then(function(response) { return response.json(); })
                            .then(function(data) {
                                if (data.success && data.variants && data.variants.length > 0) {
                                    if (typeof openVariantModal === 'function') {
                                        openVariantModal(window._pendingProductId, 'add_to_cart');
                                        window._pendingProductId = null;
                                    }
                                } else {
                                    addToCartDirect(window._pendingProductId);
                                    window._pendingProductId = null;
                                }
                            })
                            .catch(function() {
                                addToCartDirect(window._pendingProductId);
                                window._pendingProductId = null;
                            });
                        } else if (action === 'add_to_wishlist' && window._pendingProductId) {
                            addToWishlistDirect(window._pendingProductId);
                            window._pendingProductId = null;
                        }
                    }, 2000);

                } else {
                    errorMessage.textContent = data.message || 'Registrasi gagal. Silakan coba lagi.';
                    errorDiv.style.display = 'flex';
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.querySelector('.spinner').style.display = 'none';
                btn.querySelector('.btn-text').textContent = 'Daftar Sekarang';
                errorMessage.textContent = 'Terjadi kesalahan, silakan coba lagi';
                errorDiv.style.display = 'flex';
                console.error('Register error:', error);
            });
        }

        // Close popup with ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLoginPopup();
            }
        });

        // Close on overlay click
        document.addEventListener('click', function(e) {
            const overlay = document.getElementById('login-popup-overlay');
            if (e.target === overlay) {
                closeLoginPopup();
            }
        });

        // Close popup when clicking register link
        document.addEventListener('click', function(e) {
            const registerLink = document.getElementById('login-popup-register-link');
            if (e.target === registerLink) {
                closeLoginPopup();
            }
        });

        console.log('✅ Login popup initialized');
    </script>

    <script>
        document.addEventListener('login-success', function(e) {
            console.log('🔔 Login success event received, reloading counts...');
            loadCartCount();
            loadWishlistCount();
        });
    </script>

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
            slidesPerView: 3,
            spaceBetween: 10,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            autoplay: {
                delay: 2500,
                disableOnInteraction: true,
                pauseOnMouseEnter: true,
            },
            cssMode: true,
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 20 },
                768: { slidesPerView: 4, spaceBetween: 30 },
                1024: { slidesPerView: 5, spaceBetween: 30 },
            },
        });
    </script>

    <script>
        window.customerRoutes = {
            cartAdd: '{{ route("customer.cart.add") }}',
            cartPopup: '{{ route("customer.cart.popup") }}',
            cartCount: '{{ route("customer.cart.count") }}',
            cartUpdate: '{{ route("customer.cart.update") }}',
            cartRemove: '{{ route("customer.cart.remove") }}',
            cartClear: '{{ route("customer.cart.clear") }}',
            buyNow: '{{ route("customer.cart.buy-now") }}',
            wishlistAdd: '{{ route("customer.wishlist.add") }}',
            wishlistPopup: '{{ route("customer.wishlist.popup") }}',
            wishlistStatus: '{{ route("customer.wishlist.status") }}',
            wishlistRemove: '{{ route("customer.wishlist.remove") }}',
            wishlistClear: '{{ route("customer.wishlist.clear") }}',
            checkout: '{{ route("customer.checkout.index") }}',
            login: '{{ route("customer.login") }}',
        };
    </script>

    <script>
        // ============================================
        // SEARCH POPUP FUNCTIONS
        // ============================================
        function openSearchPopup() {
            const overlay = document.getElementById('search-popup-overlay');
            if (overlay) {
                overlay.classList.remove('fade-out');
                overlay.style.display = 'flex';
                // Force reflow for animation
                void overlay.offsetWidth;
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
                
                // 🔥 FOCUS INPUT
                setTimeout(function() {
                    const input = document.getElementById('search-popup-input');
                    if (input) {
                        input.focus();
                        input.select();
                    }
                }, 200);
            }
        }

        function closeSearchPopup() {
            const overlay = document.getElementById('search-popup-overlay');
            if (overlay) {
                overlay.classList.add('fade-out');
                overlay.classList.remove('active');
                setTimeout(function() {
                    overlay.style.display = 'none';
                    overlay.classList.remove('fade-out');
                }, 300);
                document.body.style.overflow = '';
            }
        }

        // 🔥 SEARCH FORM SUBMIT HANDLER
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.getElementById('search-popup-form');
            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    const input = document.getElementById('search-popup-input');
                    const searchValue = input ? input.value.trim() : '';
                    
                    if (!searchValue || searchValue === '') {
                        e.preventDefault();
                        // Tampilkan notifikasi jika search kosong
                        showToast('Silakan masukkan kata kunci pencarian', 'warning');
                        return;
                    }
                    
                    // 🔥 TUTUP POPUP SEBELUM SUBMIT
                    closeSearchPopup();
                    
                    // 🔥 TAMBAHKAN DELAY AGAR POPUP TUTUP DULU
                    setTimeout(function() {
                        searchForm.submit();
                    }, 300);
                });
            }

            // 🔥 TRENDING SEARCH CLICK
            document.querySelectorAll('.search-popup-trending-item').forEach(function(item) {
                item.addEventListener('click', function(e) {
                    // Tutup popup sebelum navigasi
                    closeSearchPopup();
                });
            });

            // 🔥 NAV LINKS CLICK
            document.querySelectorAll('.search-popup-nav-link').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    // Tutup popup sebelum navigasi
                    closeSearchPopup();
                });
            });
        });

        // 🔥 TUTUP POPUP DENGAN TOMBOL ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSearchPopup();
            }
            // 🔥 Ctrl+K atau Cmd+K untuk membuka popup
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                openSearchPopup();
            }
        });

        // 🔥 CLOSE ON OVERLAY CLICK
        document.addEventListener('click', function(e) {
            const overlay = document.getElementById('search-popup-overlay');
            if (e.target === overlay) {
                closeSearchPopup();
            }
        });

        // 🔥 TOAST NOTIFICATION
        function showToast(message, type) {
            // Cek apakah toast sudah ada
            let toast = document.getElementById('custom-toast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'custom-toast';
                toast.style.cssText = `
                    position: fixed;
                    bottom: 30px;
                    left: 50%;
                    transform: translateX(-50%);
                    padding: 12px 24px;
                    border-radius: 8px;
                    font-size: 14px;
                    z-index: 99999;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    animation: slideUp 0.3s ease;
                    max-width: 90%;
                    text-align: center;
                    font-weight: 500;
                `;
                document.body.appendChild(toast);
                
                // Tambahkan style animasi jika belum ada
                if (!document.getElementById('toast-animations')) {
                    const style = document.createElement('style');
                    style.id = 'toast-animations';
                    style.textContent = `
                        @keyframes slideUp {
                            from { transform: translateX(-50%) translateY(20px); opacity: 0; }
                            to { transform: translateX(-50%) translateY(0); opacity: 1; }
                        }
                        @keyframes slideDown {
                            from { transform: translateX(-50%) translateY(0); opacity: 1; }
                            to { transform: translateX(-50%) translateY(20px); opacity: 0; }
                        }
                    `;
                    document.head.appendChild(style);
                }
            }
            
            // Set warna berdasarkan type
            const colors = {
                success: '#10b981',
                error: '#ef4444',
                warning: '#f59e0b',
                info: '#3b82f6'
            };
            toast.style.background = colors[type] || colors.info;
            toast.style.color = '#ffffff';
            toast.textContent = message;
            toast.style.display = 'block';
            toast.style.animation = 'slideUp 0.3s ease forwards';
            
            // Hapus setelah 3 detik
            clearTimeout(toast._timeout);
            toast._timeout = setTimeout(function() {
                toast.style.animation = 'slideDown 0.3s ease forwards';
                setTimeout(function() {
                    toast.style.display = 'none';
                }, 300);
            }, 3000);
        }

        // 🔥 MAKE showToast GLOBAL
        window.showToast = showToast;

        console.log('🔍 Search popup siap digunakan!');
    </script>

    <style>
        /* ============================================
        SEARCH POPUP NAVBAR CUSTOM
        ============================================ */
        .header_section .btn_header_bottom .search-btn {
            cursor: pointer;
            background: none;
            border: none;
            padding: 0.3vw;
            font-size: 1.2vw;
            color: #0f172a;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header_section .btn_header_bottom .search-btn:hover {
            color: #076694;
        }

        @media (max-width: 768px) {
            .header_section .btn_header_bottom .search-btn {
                font-size: 3vw;
            }
        }
    </style>

    <script>
// ============================================
// VOUCHER POPUP FUNCTIONS - GLOBAL SCOPE
// ============================================

// Open voucher popup
function openVoucherPopup() {
    var overlay = document.getElementById('voucher-popup-overlay');
    var popup = document.getElementById('voucher-popup');
    
    if (overlay && popup) {
        overlay.classList.add('active');
        popup.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Refresh voucher list
        refreshVoucherList();
        
        // Focus input after animation
        setTimeout(function() {
            var input = document.getElementById('popup-voucher-input');
            if (input) input.focus();
        }, 350);
    }
}

// Close voucher popup
function closeVoucherPopup() {
    var overlay = document.getElementById('voucher-popup-overlay');
    var popup = document.getElementById('voucher-popup');
    
    if (overlay && popup) {
        overlay.classList.remove('active');
        popup.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Refresh voucher list via AJAX
function refreshVoucherList() {
    var container = document.getElementById('popup-voucher-items');
    if (!container) return;
    
    container.innerHTML = '<p style="text-align:center;padding:1vw 0;color:#94a3b8;">Memuat voucher...</p>';
    
    fetch('{{ route("customer.checkout.vouchers-ajax") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(response) { 
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json(); 
    })
    .then(function(data) {
        if (data.success && data.vouchers && data.vouchers.length > 0) {
            var html = '';
            data.vouchers.forEach(function(voucher) {
                var isApplicable = voucher.is_applicable;
                var discountText = voucher.discount_type === 'fixed' 
                    ? 'Rp ' + formatNumber(voucher.discount_value)
                    : voucher.discount_value + '%' + (voucher.max_discount_amount ? ' (Maks. Rp ' + formatNumber(voucher.max_discount_amount) + ')' : '');
                
                html += `
                    <div class="popup-voucher-item ${isApplicable ? 'applicable' : 'not-applicable'}">
                        <div class="item-content">
                            <div class="item-info">
                                <span class="item-code">${voucher.code}</span>
                                <span class="item-name">${voucher.name}</span>
                                <span class="item-discount">${discountText}</span>
                                <span class="item-min">Min. Rp ${formatNumber(voucher.min_transaction_amount)}</span>
                            </div>
                            ${isApplicable ? 
                                `<button type="button" data-code="${voucher.code}" class="btn-use btn-apply-item">Pakai</button>` :
                                `<span class="status-unavailable">${voucher.message || 'Tidak tersedia'}</span>`
                            }
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        } else {
            container.innerHTML = `
                <div class="popup-empty">
                    <p>Belum ada voucher tersedia saat ini.</p>
                    <p style="font-size:0.7vw;margin-top:0.3vw;">Cek kembali nanti untuk promo menarik!</p>
                </div>
            `;
        }
    })
    .catch(function(error) {
        console.error('Error loading vouchers:', error);
        container.innerHTML = '<p style="text-align:center;padding:1vw 0;color:#ef4444;">Gagal memuat voucher. Silakan refresh halaman.</p>';
    });
}

// Apply voucher from popup
function applyVoucherFromPopup(code) {
    var voucherCode = code || document.getElementById('popup-voucher-input').value;
    
    if (!voucherCode) {
        showToast('Masukkan kode voucher terlebih dahulu.', 'warning');
        return;
    }

    voucherCode = voucherCode.trim().toUpperCase();

    // Show loading state
    var btn = document.getElementById('btn-apply-manual-voucher');
    var originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Memproses...';

    fetch('{{ route("customer.checkout.apply-voucher") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ voucher_code: voucherCode })
    })
    .then(function(response) { 
        if (!response.ok) {
            return response.json().then(function(err) {
                throw new Error(err.message || 'Gagal menerapkan voucher');
            });
        }
        return response.json(); 
    })
    .then(function(data) {
        if (data.success) {
            // Update summary UI
            updateVoucherUI(data);
            
            // Close popup
            closeVoucherPopup();
            
            showToast(data.message || 'Voucher berhasil diterapkan!', 'success');
            
            // Refresh page to update all states
            setTimeout(function() {
                location.reload();
            }, 1000);
        } else {
            showToast(data.message || 'Gagal menerapkan voucher.', 'error');
        }
    })
    .catch(function(error) {
        showToast(error.message || 'Terjadi kesalahan. Silakan coba lagi.', 'error');
    })
    .finally(function() {
        btn.disabled = false;
        btn.textContent = originalText;
    });
}

// Remove voucher
function removeVoucher() {
    fetch('{{ route("customer.checkout.remove-voucher") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            // Update summary UI
            var summary = document.getElementById('applied-voucher-summary');
            if (summary) summary.classList.add('hidden');
            
            var discountText = document.getElementById('voucher-discount-text');
            if (discountText) discountText.textContent = 'Rp 0';
            
            var actionText = document.getElementById('voucher-action-text');
            if (actionText) actionText.textContent = 'Pilih Voucher';
            
            var input = document.getElementById('popup-voucher-input');
            if (input) input.value = '';
            
            updateTotalWithVoucher(0);

            showToast('Voucher dibatalkan.', 'info');
            
            // Refresh page
            setTimeout(function() {
                location.reload();
            }, 500);
        }
    })
    .catch(function() {
        showToast('Gagal membatalkan voucher.', 'error');
    });
}

// Update voucher UI in summary
function updateVoucherUI(data) {
    var summary = document.getElementById('applied-voucher-summary');
    if (summary) {
        summary.classList.remove('hidden');
        var codeEl = summary.querySelector('.code');
        var nameEl = summary.querySelector('.name');
        var discountEl = summary.querySelector('.discount');
        if (codeEl) codeEl.textContent = data.voucher.code;
        if (nameEl) nameEl.textContent = data.voucher.name;
        if (discountEl) discountEl.textContent = '-Rp ' + formatNumber(data.discount);
    }
    
    var discountText = document.getElementById('voucher-discount-text');
    if (discountText) discountText.textContent = '-Rp ' + formatNumber(data.discount);
    
    var actionText = document.getElementById('voucher-action-text');
    if (actionText) actionText.textContent = 'Ganti Voucher';
    
    updateTotalWithVoucher(data.discount);
}

// Update total with voucher discount
function updateTotalWithVoucher(discount) {
    var subtotalEl = document.getElementById('subtotal-display');
    var shippingEl = document.getElementById('shipping_cost');
    var totalEl = document.getElementById('total-display');
    
    if (!subtotalEl || !totalEl) return;
    
    var subtotal = parseFloat(subtotalEl.textContent.replace(/[^0-9]/g, '')) || 0;
    var shippingCost = parseInt(shippingEl ? shippingEl.value : 0) || 0;
    var total = subtotal + shippingCost - discount;
    totalEl.textContent = 'Rp ' + formatNumber(total);
}

// Format number with dots
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Show toast notification
function showToast(message, type) {
    type = type || 'info';
    var colors = {
        success: '#22c55e',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
    };

    var existing = document.querySelector('.voucher-toast');
    if (existing) {
        existing.remove();
    }

    var toast = document.createElement('div');
    toast.className = 'voucher-toast';
    toast.style.cssText = `
        position: fixed;
        bottom: 2vw;
        right: 2vw;
        padding: 1vw 1.5vw;
        background: ${colors[type] || colors.info};
        color: white;
        border-radius: 0.7vw;
        font-size: 0.85vw;
        box-shadow: 0 0.2vw 1vw rgba(0,0,0,0.15);
        z-index: 99999;
        max-width: 25vw;
        transform: translateY(120%);
        transition: transform 0.3s ease;
        font-family: inherit;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(function() {
        toast.style.transform = 'translateY(0)';
    }, 100);

    setTimeout(function() {
        toast.style.transform = 'translateY(120%)';
        setTimeout(function() {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// ============================================
// EVENT BINDINGS - Run when DOM ready
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Open popup - using both ID and class for compatibility
    var openBtn = document.getElementById('btn-open-voucher');
    if (openBtn) {
        openBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openVoucherPopup();
        });
    }
    
    // Also handle if button uses class .btn-open-voucher
    var openBtns = document.querySelectorAll('.btn-open-voucher');
    openBtns.forEach(function(btn) {
        if (btn.id !== 'btn-open-voucher') {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                openVoucherPopup();
            });
        }
    });

    // Close popup - overlay
    var overlay = document.getElementById('voucher-popup-overlay');
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                closeVoucherPopup();
            }
        });
    }

    // Close popup - close button
    var closeBtn = document.getElementById('close-voucher-popup');
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeVoucherPopup();
        });
    }

    // Apply manual voucher
    var applyBtn = document.getElementById('btn-apply-manual-voucher');
    if (applyBtn) {
        applyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            applyVoucherFromPopup();
        });
    }

    // Enter key on input
    var input = document.getElementById('popup-voucher-input');
    if (input) {
        input.addEventListener('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                applyVoucherFromPopup();
            }
        });
    }

    // Apply from list - delegated
    document.addEventListener('click', function(e) {
        if (e.target && e.target.matches('.btn-apply-item')) {
            var code = e.target.getAttribute('data-code');
            if (code) {
                applyVoucherFromPopup(code);
            }
        }
    });

    // Remove voucher
    var removeBtn = document.getElementById('btn-remove-voucher');
    if (removeBtn) {
        removeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            removeVoucher();
        });
    }

    // ESC key to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeVoucherPopup();
        }
    });
});

// Make functions globally accessible
window.openVoucherPopup = openVoucherPopup;
window.closeVoucherPopup = closeVoucherPopup;
window.applyVoucherFromPopup = applyVoucherFromPopup;
window.removeVoucher = removeVoucher;
window.showToast = showToast;
window.formatNumber = formatNumber;
window.refreshVoucherList = refreshVoucherList;

console.log('🎟️ Voucher popup initialized!');
</script>

    <script src="{{ asset('js/cart.js') }}"></script>
    <script src="{{ asset('js/popup.js') }}"></script>
    <script src="{{ asset('js/variant-modal.js') }}"></script>
    

</body>
</html>