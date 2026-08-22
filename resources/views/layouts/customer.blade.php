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

    <script>
        if (typeof window.addToWishlist === 'function') {
            const originalAddToWishlist = window.addToWishlist;
            
            window.addToWishlist = function(productId) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                
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
                    body: JSON.stringify({ product_id: productId })
                })
                .then(response => {
                    if (response.status === 401) {
                        // 🔥 REDIRECT KE LOGIN
                        showToast('Silakan login terlebih dahulu', 'warning');
                        setTimeout(() => {
                            window.location.href = window.customerRoutes.login;
                        }, 1500);
                        throw new Error('Unauthorized');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const count = data.count || 0;
                        const inWishlist = data.in_wishlist || false;
                        
                        updateWishlistIcon(productId, inWishlist);
                        
                        if (typeof window.updateNavbarWishlistCount === 'function') {
                            window.updateNavbarWishlistCount(count);
                        }
                        
                        document.dispatchEvent(new CustomEvent('wishlist-updated', {
                            detail: { count, product_id: productId, in_wishlist: inWishlist }
                        }));
                        
                        showToast(data.message || (inWishlist ? 'Produk ditambahkan ke wishlist!' : 'Produk dihapus dari wishlist!'), 'success');
                        
                        if (typeof loadWishlistPopup === 'function') {
                            loadWishlistPopup();
                        }
                    } else {
                        showToast(data.message || 'Gagal menambahkan ke wishlist', 'error');
                    }
                })
                .catch(function(error) {
                    if (error.message !== 'Unauthorized') {
                        showToast('Terjadi kesalahan', 'error');
                    }
                })
                .finally(() => {
                    allButtons.forEach(function(btn) {
                        if (!btn.disabled) return;
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

        // Override addToCart untuk handle redirect login
        if (typeof window.addToCartDirect === 'function') {
            const originalAddToCartDirect = window.addToCartDirect;
            
            window.addToCartDirect = function(productId, variantId = null, quantity = 1) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                
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
                .then(response => {
                    if (response.status === 401) {
                        showToast('Silakan login terlebih dahulu', 'warning');
                        setTimeout(() => {
                            window.location.href = window.customerRoutes.login;
                        }, 1500);
                        throw new Error('Unauthorized');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const count = data.count || 0;
                        updateNavbarCartCount(count);
                        
                        document.dispatchEvent(new CustomEvent('cart-updated', {
                            detail: { count, message: data.message }
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
                .catch(function(error) {
                    if (error.message !== 'Unauthorized') {
                        showToast('Terjadi kesalahan', 'error');
                    }
                })
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<iconify-icon icon="solar:cart-linear"></iconify-icon>';
                    }
                });
            };
        }
    </script>
    

</body>
</html>