<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {{-- Meta Description --}}
    @yield('meta_description')

    @stack('head')
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
    <div id="cart-popup-overlay" class="fixed inset-0 z-50 bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300"></div>

    <div id="cart-popup" class="fixed top-0 right-0 z-50 h-full w-full max-w-md bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out">
        
        {{-- Header Popup --}}
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-4">
            <h2 class="text-lg font-bold text-slate-900">🛒 Keranjang Belanja</h2>
            <div class="flex items-center gap-2">
                {{-- Tombol Clear All --}}
                <button id="cart-popup-clear" class="text-sm text-red-500 hover:text-red-700 transition px-2 py-1 rounded-lg hover:bg-red-50 hidden">
                    <svg class="h-5 w-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Kosongkan
                </button>
                <button id="cart-popup-close" class="p-2 text-slate-400 hover:text-slate-600 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body Popup --}}
        <div id="cart-popup-body" class="flex h-[calc(100%-180px)] flex-col overflow-y-auto p-4">
            {{-- Content akan diisi oleh JavaScript --}}
            <div id="cart-popup-content" class="flex-1">
                <div class="flex h-full flex-col items-center justify-center text-slate-400">
                    <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <p class="mt-4 font-medium text-slate-600">Keranjang kosong</p>
                    <p class="text-sm text-slate-400">Yuk, mulai belanja!</p>
                </div>
            </div>
        </div>

        {{-- Footer Popup --}}
        <div id="cart-popup-footer" class="border-t border-slate-200 bg-slate-50 px-4 py-4 hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total</p>
                    <p id="cart-popup-total" class="text-lg font-bold text-slate-900">Rp 0</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('customer.cart.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Lihat
                    </a>
                    <a href="{{ route('customer.checkout.index') }}" class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                        Checkout →
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- POPUP WISHLIST SLIDE --}}
    {{-- ============================================ --}}
    <div id="wishlist-popup-overlay" class="fixed inset-0 z-50 bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300"></div>

    <div id="wishlist-popup" class="fixed top-0 right-0 z-50 h-full w-full max-w-md bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out">
        
        {{-- Header Popup --}}
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-4">
            <h2 class="text-lg font-bold text-slate-900">❤️ Wishlist</h2>
            <div class="flex items-center gap-2">
                <button id="wishlist-popup-close" class="p-2 text-slate-400 hover:text-slate-600 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body Popup --}}
        <div id="wishlist-popup-body" class="flex h-[calc(100%-80px)] flex-col overflow-y-auto p-4">
            <div id="wishlist-popup-content" class="flex-1">
                <div class="flex h-full flex-col items-center justify-center text-slate-400">
                    <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <p class="mt-4 font-medium text-slate-600">Wishlist kosong</p>
                    <p class="text-sm text-slate-400">Simpan produk favoritmu di sini!</p>
                </div>
            </div>
        </div>
    </div>

    @include('customer.partials.footer')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ============================================
            // TOAST / ALERT SYSTEM
            // ============================================
            window.showToast = function(message, type = 'info', duration = 4000) {
                const container = document.getElementById('toast-container');
                if (!container) return;

                const toast = document.createElement('div');
                toast.className = `toast toast-${type}`;

                const icons = {
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

                // Close button
                toast.querySelector('.toast-close').addEventListener('click', function() {
                    closeToast(toast);
                });

                // Auto close
                setTimeout(function() {
                    closeToast(toast);
                }, duration);

                return toast;
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
            // AUTO SHOW SESSION FLASH MESSAGES
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

            // ============================================
            // MOBILE MENU TOGGLE
            // ============================================
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuToggle && mobileMenu) {
                mobileMenuToggle.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }

        });
    </script>

     <script>
        // ============================================
        // VARIANT MODAL
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById('variant-modal');
            const modalProductId = document.getElementById('modal-product-id');
            const modalProductName = document.getElementById('modal-product-name');
            const modalProductPrice = document.getElementById('modal-product-price');
            const modalProductImage = document.getElementById('modal-product-image');
            const modalProductStock = document.getElementById('modal-product-stock');
            const modalVariantOptions = document.getElementById('modal-variant-options');
            const modalSelectedVariant = document.getElementById('modal-selected-variant');
            const modalVariantValues = document.getElementById('modal-variant-values');
            const modalQtyInput = document.getElementById('modal-qty-input');
            const modalAddToCartBtn = document.getElementById('modal-add-to-cart-btn');
            const modalError = document.getElementById('modal-error');

            let modalSelectedValues = {};
            let modalProductVariants = [];
            let modalProductOptions = [];
            let modalProductData = null;
            let currentSelectedVariant = null;

            // ============================================
            // OPEN MODAL
            // ============================================
            window.openVariantModal = function(productId) {
                // Reset state
                modalSelectedValues = {};
                modalSelectedVariant.value = '';
                modalVariantValues.value = '';
                modalQtyInput.value = 1;
                modalError.classList.add('hidden');
                modalError.textContent = '';
                modalAddToCartBtn.disabled = true;
                currentSelectedVariant = null;

                // Show loading state
                modalVariantOptions.innerHTML = `
                    <div class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
                        <p class="mt-2 text-sm text-slate-500">Memuat varian...</p>
                    </div>
                `;

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                // Fetch product data
                fetch(`/api/products/${productId}/variants`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Gagal memuat data');
                        }

                        modalProductData = data.product;
                        modalProductVariants = data.variants;
                        modalProductOptions = data.options;

                        // Set product info
                        modalProductId.value = productId;
                        modalProductName.textContent = data.product.name;
                        
                        // Hitung harga termurah
                        const minPrice = data.variants.reduce((min, v) => {
                            const price = v.discount_price ?? v.price;
                            return price < min ? price : min;
                        }, Infinity);
                        modalProductPrice.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(minPrice);

                        // Total stok
                        const totalStock = data.variants.reduce((sum, v) => sum + v.stock, 0);
                        if (modalProductStock) {
                            modalProductStock.textContent = 'Stok: ' + totalStock;
                        }

                        if (data.product.image) {
                            modalProductImage.src = data.product.image;
                        }

                        // Check if product has options
                        if (!data.options || data.options.length === 0) {
                            if (data.variants && data.variants.length > 0) {
                                modalVariantOptions.innerHTML = `
                                    <div class="rounded-lg bg-red-50 p-4 text-center text-sm text-red-700">
                                        ⚠️ Produk memiliki varian tetapi opsi tidak ditemukan.
                                    </div>
                                `;
                                return;
                            }

                            modalVariantOptions.innerHTML = `
                                <div class="rounded-lg bg-yellow-50 p-4 text-center text-sm text-yellow-700">
                                    Produk ini tidak memiliki varian khusus.
                                </div>
                            `;
                            return;
                        }

                        // Render variant options
                        renderVariantOptions(data.options, data.variants);
                    })
                    .catch(error => {
                        console.error('Error fetching product variants:', error);
                        modalVariantOptions.innerHTML = `
                            <div class="rounded-lg bg-red-50 p-4 text-center text-sm text-red-700">
                                ❌ Gagal memuat data varian.
                                <br><button onclick="openVariantModal(${productId})" class="mt-2 text-blue-600 hover:underline">Coba lagi</button>
                            </div>
                        `;
                    });
            };

            // ============================================
            // CLOSE MODAL
            // ============================================
            window.closeVariantModal = function(event) {
                if (event && event.target !== event.currentTarget) return;
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            };

            // ============================================
            // RENDER VARIANT OPTIONS
            // ============================================
            function renderVariantOptions(options, variants) {
                modalVariantOptions.innerHTML = '';

                if (!options || options.length === 0) return;

                options.forEach((option) => {
                    const optionDiv = document.createElement('div');
                    optionDiv.className = 'mb-4';

                    // 🔥 Cek apakah opsi ini adalah Ukuran / Size
                    const isSizeOption = option.name.toLowerCase().includes('ukuran') || option.name.toLowerCase().includes('size');

                    let valuesHtml = '';
                    option.values.forEach((value) => {
                        const variantsWithValue = variants.filter(v => 
                            v.values && v.values.includes(value.id) && v.stock > 0
                        );
                        const totalStock = variantsWithValue.reduce((sum, v) => sum + v.stock, 0);
                        const hasStock = totalStock > 0;

                        // 🔥 TAMPILKAN STOK HANYA DI UKURAN
                        let stockBadge = '';
                        if (isSizeOption) {
                            stockBadge = hasStock 
                                ? `<span class="stock-badge ml-1 text-xs text-slate-400">(${totalStock})</span>` 
                                : `<span class="stock-badge ml-1 text-xs text-red-400">(habis)</span>`;
                        }

                        valuesHtml += `
                            <button type="button"
                                class="modal-variant-option rounded-lg border-2 px-4 py-2 text-sm font-medium transition
                                    border-slate-200 bg-white text-slate-900 hover:border-blue-300
                                    ${!hasStock ? 'opacity-50 cursor-not-allowed bg-slate-100' : ''}"
                                data-option-id="${option.id}"
                                data-value-id="${value.id}"
                                data-value-name="${value.value}"
                                data-is-size="${isSizeOption ? '1' : '0'}"
                                data-image="${value.image || ''}" 
                                ${!hasStock ? 'disabled' : ''}>
                                ${value.value}
                                ${stockBadge}
                            </button>
                        `;
                    });

                    optionDiv.innerHTML = `
                        <label class="block text-sm font-medium text-slate-700 mb-2">${option.name}</label>
                        <div class="flex flex-wrap gap-2 option-group" data-option-id="${option.id}">
                            ${valuesHtml}
                        </div>
                    `;

                    modalVariantOptions.appendChild(optionDiv);
                });

                // Pasang event listener untuk klik tombol
                attachOptionEvents(options, variants);

                // 🔥 OTOMATIS PILIH PILIHAN PERTAMA YANG TERSEDIA
                autoSelectFirstAvailable(options, variants);
            }

            // ============================================
            // ATTACH CLICK EVENTS
            // ============================================
            function attachOptionEvents(options, variants) {
                document.querySelectorAll('.modal-variant-option').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        if (this.disabled) return;
                        selectOptionButton(this, options, variants);
                    });
                });
            }

            // ============================================
            // SELECT OPTION BUTTON
            // ============================================
            function selectOptionButton(btn, options, variants) {
                const optionId = btn.getAttribute('data-option-id');
                const valueId = parseInt(btn.getAttribute('data-value-id'));
                const optionImage = btn.getAttribute('data-image'); // 🔥 Ambil URL gambar dari tombol
                const group = btn.closest('.option-group');
                if (!group) return;

                // Hapus style active dari tombol lain dalam grup yang sama
                group.querySelectorAll('.modal-variant-option').forEach(function(b) {
                    b.classList.remove('active', 'border-blue-500', 'bg-blue-50', 'text-blue-700');
                    b.classList.add('border-slate-200', 'bg-white', 'text-slate-900');
                });

                // Tambahkan style active ke tombol yang diklik
                btn.classList.add('active', 'border-blue-500', 'bg-blue-50', 'text-blue-700');
                btn.classList.remove('border-slate-200', 'bg-white', 'text-slate-900');

                // 🔥 GANTI GAMBAR MODAL DENGAN GAMBAR WARNA (JIKA ADA)
                if (optionImage && optionImage.trim() !== '') {
                    modalProductImage.src = optionImage;
                }

                // Simpan pilihan
                modalSelectedValues[optionId] = valueId;

                // Update state varian terpilih
                updateVariantSelection(options, variants);
            }

            // ============================================
            // AUTO SELECT FIRST AVAILABLE
            // ============================================
            function autoSelectFirstAvailable(options, variants) {
                const groups = modalVariantOptions.querySelectorAll('.option-group');
                groups.forEach(function(group) {
                    const firstAvailable = group.querySelector('.modal-variant-option:not(:disabled)');
                    if (firstAvailable) {
                        selectOptionButton(firstAvailable, options, variants);
                    }
                });
            }

            // ============================================
            // UPDATE VARIANT SELECTION & STOK
            // ============================================
            function updateVariantSelection(options, variants) {
                const selectedIds = Object.values(modalSelectedValues).map(Number).sort();

                // Update ketersediaan tombol opsi lainnya berdasarkan pilihan saat ini
                updateStockDisplay(options, variants);

                // Cek apakah semua opsi sudah dipilih
                const totalOptionsCount = options ? options.length : 0;
                const selectedCount = Object.keys(modalSelectedValues).length;

                if (selectedCount < totalOptionsCount) {
                    modalAddToCartBtn.disabled = true;
                    return;
                }

                // Cari varian yang cocok dengan kombinasi terpilih
                const matchedVariant = variants.find(function(v) {
                    if (!v.values) return false;
                    const variantIds = v.values.map(Number).sort();
                    return JSON.stringify(variantIds) === JSON.stringify(selectedIds) && v.stock > 0;
                });

                if (matchedVariant) {
                    currentSelectedVariant = matchedVariant;
                    modalSelectedVariant.value = matchedVariant.id;
                    modalVariantValues.value = JSON.stringify(Object.values(modalSelectedValues));

                    // 🔥 Ganti ke gambar varian jika varian punya gambar tersendiri
                    if (matchedVariant.image) {
                        modalProductImage.src = matchedVariant.image;
                    }

                    // Update Harga
                    const price = matchedVariant.discount_price ?? matchedVariant.price;
                    modalProductPrice.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(price);

                    // Update Stok
                    if (modalProductStock) {
                        modalProductStock.textContent = 'Stok: ' + matchedVariant.stock;
                    }

                    modalQtyInput.max = matchedVariant.stock;
                    modalQtyInput.value = 1;
                    modalAddToCartBtn.disabled = false;
                    modalError.classList.add('hidden');
                    modalError.textContent = '';
                } else {
                    currentSelectedVariant = null;
                    modalSelectedVariant.value = '';
                    modalAddToCartBtn.disabled = true;
                    modalError.textContent = 'Kombinasi varian tidak tersedia atau stok habis.';
                    modalError.classList.remove('hidden');
                }
            }

            // ============================================
            // UPDATE STOK DISPLAY (KHUSUS UKURAN)
            // ============================================
            function updateStockDisplay(options, variants) {
                document.querySelectorAll('.modal-variant-option').forEach(function(btn) {
                    const optionId = btn.getAttribute('data-option-id');
                    const valueId = parseInt(btn.getAttribute('data-value-id'));
                    const isSize = btn.getAttribute('data-is-size') === '1';

                    const tempValues = Object.assign({}, modalSelectedValues);
                    tempValues[optionId] = valueId;
                    const selectedIds = Object.values(tempValues).map(Number).sort();

                    const matchedVariant = variants.find(function(v) {
                        if (!v.values) return false;
                        const variantIds = v.values.map(Number).sort();
                        return selectedIds.every(id => variantIds.includes(id)) && v.stock > 0;
                    });

                    const stockBadge = btn.querySelector('.stock-badge');

                    if (matchedVariant) {
                        btn.disabled = false;
                        btn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-slate-100');

                        // 🔥 HANYA UPDATE BADGE TEKS JIKA OPSI UKURAN
                        if (isSize && stockBadge) {
                            stockBadge.textContent = `(${matchedVariant.stock})`;
                            stockBadge.className = 'stock-badge ml-1 text-xs text-slate-400';
                        }
                    } else {
                        btn.disabled = true;
                        btn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-slate-100');
                        btn.classList.remove('active', 'border-blue-500', 'bg-blue-50', 'text-blue-700');
                        btn.classList.add('border-slate-200', 'bg-white', 'text-slate-900');

                        if (isSize && stockBadge) {
                            stockBadge.textContent = '(habis)';
                            stockBadge.className = 'stock-badge ml-1 text-xs text-red-400';
                        }
                    }
                });
            }

            // ============================================
            // MODAL QUANTITY BUTTONS
            // ============================================
            document.querySelectorAll('.modal-qty-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    let value = parseInt(modalQtyInput.value) || 1;
                    const max = parseInt(modalQtyInput.max) || 999;

                    if (this.getAttribute('data-action') === 'increase' && value < max) {
                        value += 1;
                    } else if (this.getAttribute('data-action') === 'decrease' && value > 1) {
                        value -= 1;
                    }
                    modalQtyInput.value = value;
                });
            });

            // ============================================
            // MODAL ADD TO CART
            // ============================================
            modalAddToCartBtn.addEventListener('click', function() {
                const variantId = modalSelectedVariant.value;
                const productId = modalProductId.value;
                const quantity = parseInt(modalQtyInput.value) || 1;

                if (!variantId) {
                    modalError.textContent = 'Pilih varian terlebih dahulu!';
                    modalError.classList.remove('hidden');
                    return;
                }

                const variant = modalProductVariants.find(function(v) { return v.id == variantId; });
                if (variant && quantity > variant.stock) {
                    modalError.textContent = 'Stok tidak mencukupi! Tersedia: ' + variant.stock;
                    modalError.classList.remove('hidden');
                    return;
                }

                modalAddToCartBtn.disabled = true;
                modalAddToCartBtn.textContent = 'Memproses...';

                fetch('{{ route("customer.cart.add") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        variant_id: variantId,
                        quantity: quantity
                    })
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success) {
                        closeVariantModal();
                        updateCartCount(data.cart_count);
                        showNotification('Produk berhasil ditambahkan ke keranjang!', 'success');
                    } else {
                        modalError.textContent = data.message || 'Gagal menambahkan ke keranjang.';
                        modalError.classList.remove('hidden');
                    }
                })
                .catch(function(error) {
                    console.error('Error adding to cart:', error);
                    modalError.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
                    modalError.classList.remove('hidden');
                })
                .finally(function() {
                    modalAddToCartBtn.disabled = false;
                    modalAddToCartBtn.textContent = 'Tambah ke Keranjang';
                });
            });

            // ============================================
            // UPDATE CART COUNT
            // ============================================
            function updateCartCount(count) {
                const cartCountElements = document.querySelectorAll('#cart-count');
                cartCountElements.forEach(function(el) {
                    el.textContent = count;
                    if (count > 0) {
                        el.classList.remove('hidden');
                    } else {
                        el.classList.add('hidden');
                    }
                });
            }

            // ============================================
            // SHOW NOTIFICATION
            // ============================================
            function showNotification(message, type) {
                type = type || 'success';
                const colors = {
                    success: 'bg-green-500',
                    error: 'bg-red-500',
                    warning: 'bg-yellow-500',
                    info: 'bg-blue-500'
                };

                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 z-50 rounded-lg ' + (colors[type] || 'bg-slate-800') + ' px-6 py-3 text-white shadow-lg transition-all duration-300 transform translate-x-full';
                notification.textContent = message;
                document.body.appendChild(notification);

                setTimeout(function() {
                    notification.classList.remove('translate-x-full');
                    notification.classList.add('translate-x-0');
                }, 100);

                setTimeout(function() {
                    notification.classList.remove('translate-x-0');
                    notification.classList.add('translate-x-full');
                    setTimeout(function() { notification.remove(); }, 300);
                }, 3000);
            }

            // ============================================
            // CLOSE MODAL ON ESC
            // ============================================
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeVariantModal();
                }
            });

        });
    </script>

    <script>
        // ============================================
        // CART POPUP SLIDE
        // ============================================

        (function() {
            const cartPopup = document.getElementById('cart-popup');
            const cartOverlay = document.getElementById('cart-popup-overlay');
            const cartClose = document.getElementById('cart-popup-close');
            const cartClear = document.getElementById('cart-popup-clear');
            const cartContent = document.getElementById('cart-popup-content');
            const cartFooter = document.getElementById('cart-popup-footer');
            const cartTotal = document.getElementById('cart-popup-total');
            const cartCount = document.getElementById('cart-count');

            let isOpen = false;

            // ============================================
            // OPEN POPUP
            // ============================================
            function openCartPopup() {
                isOpen = true;
                cartPopup.classList.remove('translate-x-full');
                cartOverlay.classList.remove('opacity-0', 'pointer-events-none');
                cartOverlay.classList.add('opacity-100', 'pointer-events-auto');
                document.body.style.overflow = 'hidden';
                loadCartContent();
            }

            // ============================================
            // CLOSE POPUP
            // ============================================
            function closeCartPopup() {
                isOpen = false;
                cartPopup.classList.add('translate-x-full');
                cartOverlay.classList.add('opacity-0', 'pointer-events-none');
                cartOverlay.classList.remove('opacity-100', 'pointer-events-auto');
                document.body.style.overflow = '';
            }

            // ============================================
            // LOAD CART CONTENT
            // ============================================
            function loadCartContent() {
                fetch('{{ route("customer.cart.popup") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.html) {
                        cartContent.innerHTML = data.html;
                        cartFooter.classList.remove('hidden');
                        cartClear.classList.remove('hidden');
                        
                        // Update total
                        if (data.total) {
                            cartTotal.textContent = 'Rp ' + formatNumber(data.total);
                        }
                        
                        // Update cart count
                        if (cartCount) {
                            cartCount.textContent = data.count || 0;
                        }

                        // Attach events untuk item di popup
                        attachCartEvents();
                    } else {
                        cartContent.innerHTML = `
                            <div class="flex h-full flex-col items-center justify-center text-slate-400">
                                <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                <p class="mt-4 font-medium text-slate-600">Keranjang kosong</p>
                                <p class="text-sm text-slate-400">Yuk, mulai belanja!</p>
                            </div>
                        `;
                        cartFooter.classList.add('hidden');
                        cartClear.classList.add('hidden');
                    }
                })
                .catch(() => {
                    cartContent.innerHTML = `
                        <div class="flex h-full flex-col items-center justify-center text-slate-400">
                            <p class="text-red-500">Gagal memuat keranjang</p>
                            <button onclick="loadCartContent()" class="mt-2 text-blue-600">Coba Lagi</button>
                        </div>
                    `;
                });
            }

            document.getElementById('cart-toggle')?.addEventListener('click', function(e) {
                e.preventDefault();
                openCartPopup();
            });

            // ============================================
            // CLEAR ALL CART
            // ============================================
            function clearCart() {
                if (!confirm('Kosongkan semua item di keranjang?')) return;
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                
                fetch('{{ route("customer.cart.clear") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (cartCount) {
                            cartCount.textContent = 0;
                        }
                        loadCartContent();
                        showToast('Keranjang berhasil dikosongkan', 'success');
                    } else {
                        showToast(data.message || 'Gagal mengosongkan keranjang', 'error');
                    }
                })
                .catch(error => {
                    console.error('Clear cart error:', error);
                    showToast('Terjadi kesalahan saat mengosongkan keranjang', 'error');
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
                // Quantity buttons di popup
                document.querySelectorAll('#cart-popup-content .qty-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const input = this.closest('.flex')?.querySelector('.qty-input');
                        if (!input) return;
                        let value = parseInt(input.value) || 1;
                        const action = this.dataset.action;
                        if (action === 'increase') value += 1;
                        else if (action === 'decrease' && value > 1) value -= 1;
                        if (value < 1) return;
                        input.value = value;
                        updateCartItem(input.dataset.key, value);
                    });
                });

                // Quantity input change
                document.querySelectorAll('#cart-popup-content .qty-input').forEach(input => {
                    input.addEventListener('change', function() {
                        let value = parseInt(this.value) || 1;
                        if (value < 1) value = 1;
                        this.value = value;
                        updateCartItem(this.dataset.key, value);
                    });
                });

                // Remove item
                document.querySelectorAll('#cart-popup-content .remove-item').forEach(button => {
                    button.addEventListener('click', function() {
                        if (!confirm('Hapus item ini?')) return;
                        const key = this.dataset.key;
                        removeCartItem(key);
                    });
                });
            }

            // ============================================
            // UPDATE CART ITEM
            // ============================================
            function updateCartItem(key, quantity) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                
                fetch('{{ route("customer.cart.update") }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ key, quantity })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadCartContent();
                        if (cartCount) {
                            cartCount.textContent = data.count || 0;
                        }
                    } else {
                        alert(data.message || 'Gagal update keranjang');
                        loadCartContent();
                    }
                })
                .catch(() => {
                    loadCartContent();
                });
            }

            // ============================================
            // REMOVE CART ITEM
            // ============================================
            function removeCartItem(key) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                
                fetch('{{ route("customer.cart.remove") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ key })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadCartContent();
                        if (cartCount) {
                            cartCount.textContent = data.count || 0;
                        }
                    }
                })
                .catch(() => {
                    loadCartContent();
                });
            }

            // ============================================
            // EVENT LISTENERS
            // ============================================

            // Klik ikon cart di header
            document.addEventListener('click', function(e) {
                const cartLink = e.target.closest('a[href*="cart"]');
                if (cartLink && !e.target.closest('#cart-popup')) {
                    e.preventDefault();
                    openCartPopup();
                }
            });

            // Close popup
            if (cartClose) {
                cartClose.addEventListener('click', closeCartPopup);
            }

            if (cartOverlay) {
                cartOverlay.addEventListener('click', closeCartPopup);
            }

            // 🔥 Clear All
            if (cartClear) {
                cartClear.addEventListener('click', clearCart);
            }

            // ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && isOpen) {
                    closeCartPopup();
                }
            });

            // Expose functions ke global
            window.openCartPopup = openCartPopup;
            window.closeCartPopup = closeCartPopup;
            window.loadCartContent = loadCartContent;
            window.clearCart = clearCart;

        })();
    </script>

    <script>
        // ============================================
        // WISHLIST POPUP SLIDE
        // ============================================

        (function() {
            const wishlistPopup = document.getElementById('wishlist-popup');
            const wishlistOverlay = document.getElementById('wishlist-popup-overlay');
            const wishlistClose = document.getElementById('wishlist-popup-close');
            const wishlistContent = document.getElementById('wishlist-popup-content');
            const wishlistCount = document.getElementById('wishlist-count');

            let isWishlistOpen = false;

            // ============================================
            // OPEN WISHLIST POPUP
            // ============================================
            function openWishlistPopup() {
                isWishlistOpen = true;
                wishlistPopup.classList.remove('translate-x-full');
                wishlistOverlay.classList.remove('opacity-0', 'pointer-events-none');
                wishlistOverlay.classList.add('opacity-100', 'pointer-events-auto');
                document.body.style.overflow = 'hidden';
                loadWishlistContent();
            }

            // ============================================
            // CLOSE WISHLIST POPUP
            // ============================================
            function closeWishlistPopup() {
                isWishlistOpen = false;
                wishlistPopup.classList.add('translate-x-full');
                wishlistOverlay.classList.add('opacity-0', 'pointer-events-none');
                wishlistOverlay.classList.remove('opacity-100', 'pointer-events-auto');
                document.body.style.overflow = '';
            }

            // ============================================
            // LOAD WISHLIST CONTENT
            // ============================================
            function loadWishlistContent() {
                fetch('{{ route("customer.wishlist.popup") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.html) {
                        wishlistContent.innerHTML = data.html;
                        if (wishlistCount) {
                            wishlistCount.textContent = data.count || 0;
                        }
                        attachWishlistEvents();
                    } else {
                        wishlistContent.innerHTML = `
                            <div class="flex h-full flex-col items-center justify-center text-slate-400">
                                <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <p class="mt-4 font-medium text-slate-600">Wishlist kosong</p>
                                <p class="text-sm text-slate-400">Simpan produk favoritmu di sini!</p>
                            </div>
                        `;
                    }
                })
                .catch(() => {
                    wishlistContent.innerHTML = `
                        <div class="flex h-full flex-col items-center justify-center text-slate-400">
                            <p class="text-red-500">Gagal memuat wishlist</p>
                            <button onclick="loadWishlistContent()" class="mt-2 text-blue-600">Coba Lagi</button>
                        </div>
                    `;
                });
            }

            // ============================================
            // ATTACH WISHLIST EVENTS
            // ============================================
            function attachWishlistEvents() {
                // Remove from wishlist
                document.querySelectorAll('#wishlist-popup-content .remove-wishlist').forEach(button => {
                    button.addEventListener('click', function() {
                        const productId = this.dataset.productId;
                        if (!confirm('Hapus dari wishlist?')) return;
                        removeFromWishlist(productId);
                    });
                });

                // Add to cart dari wishlist
                document.querySelectorAll('#wishlist-popup-content .add-to-cart-wishlist').forEach(button => {
                    button.addEventListener('click', function() {
                        const productId = this.dataset.productId;
                        const variantId = this.dataset.variantId || null;
                        addToCartFromWishlist(productId, variantId);
                    });
                });
            }

            // ============================================
            // REMOVE FROM WISHLIST
            // ============================================
            function removeFromWishlist(productId) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                
                fetch('{{ route("customer.wishlist.remove") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadWishlistContent();
                        if (wishlistCount) {
                            wishlistCount.textContent = data.count || 0;
                        }
                        showToast(data.message || 'Produk dihapus dari wishlist', 'success');
                    } else {
                        showToast(data.message || 'Gagal menghapus dari wishlist', 'error');
                    }
                })
                .catch(() => {
                    showToast('Terjadi kesalahan', 'error');
                });
            }

            // ============================================
            // ADD TO CART FROM WISHLIST
            // ============================================
            function addToCartFromWishlist(productId, variantId) {
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
                        quantity: 1
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update cart count
                        const cartCount = document.getElementById('cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.count || 0;
                        }
                        
                        // Remove from wishlist after adding to cart
                        removeFromWishlist(productId);
                        showToast('Produk ditambahkan ke keranjang!', 'success');
                    } else {
                        showToast(data.message || 'Gagal menambahkan ke keranjang', 'error');
                    }
                })
                .catch(() => {
                    showToast('Terjadi kesalahan', 'error');
                });
            }

            // ============================================
            // EVENT LISTENERS
            // ============================================

            // Klik ikon wishlist di header
            document.getElementById('wishlist-toggle')?.addEventListener('click', function(e) {
                e.preventDefault();
                openWishlistPopup();
            });

            // Close popup
            if (wishlistClose) {
                wishlistClose.addEventListener('click', closeWishlistPopup);
            }

            if (wishlistOverlay) {
                wishlistOverlay.addEventListener('click', closeWishlistPopup);
            }

            // Expose functions ke global
            window.openWishlistPopup = openWishlistPopup;
            window.closeWishlistPopup = closeWishlistPopup;
            window.loadWishlistContent = loadWishlistContent;

        })();
    </script>

</body>
</html>