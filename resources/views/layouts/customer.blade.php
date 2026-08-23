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
                            Mulai Belanja
                        </a>
                    </div>
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

    <script src="{{ asset('js/cart.js') }}"></script>
    <script src="{{ asset('js/popup.js') }}"></script>
    <script src="{{ asset('js/variant-modal.js') }}"></script>
    

</body>
</html>