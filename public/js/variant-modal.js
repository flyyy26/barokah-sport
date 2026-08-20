// ============================================
// VARIANT MODAL - SEDERHANA & LENGKAP
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
    const modalQtyInput = document.getElementById('modal-qty-input');
    const modalAddToCartBtn = document.getElementById('modal-add-to-cart-btn');
    const modalError = document.getElementById('modal-error');
    const modalActionMode = document.getElementById('modal-action-mode');

    let selectedValues = {};
    let productVariants = [];
    let currentVariant = null;
    let currentProductId = null;

    // ============================================
    // OPEN MODAL
    // ============================================
    window.openVariantModal = function(productId, mode = 'add_to_cart') {
        currentProductId = productId;
        
        // Reset
        selectedValues = {};
        modalSelectedVariant.value = '';
        modalQtyInput.value = 1;
        modalError.classList.add('hidden');
        modalAddToCartBtn.disabled = true;
        modalActionMode.value = mode;

        // Ubah teks tombol
        modalAddToCartBtn.textContent = mode === 'buy_now' ? 'Beli Sekarang' : 'Tambah ke Keranjang';

        // Loading
        modalVariantOptions.innerHTML = `
            <div class="variant-loading">
                <div class="variant-spinner"></div>
                <p>Memuat varian...</p>
            </div>
        `;

        // Tampilkan modal
        modal.classList.remove('hidden');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        // 🔥 RESET FLAG _fromWishlist jika modal dibuka dari wishlist
        // Flag ini akan digunakan di add to cart untuk reload wishlist popup

        // Fetch data
        fetch(`/api/products/${productId}/variants`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Gagal memuat data');
            }

            productVariants = data.variants;

            // Set product info
            modalProductId.value = productId;
            modalProductName.textContent = data.product.name;
            
            const minPrice = data.variants.reduce((min, v) => {
                const price = v.discount_price ?? v.price;
                return price < min ? price : min;
            }, Infinity);
            modalProductPrice.textContent = 'Rp ' + formatRupiah(minPrice);

            const totalStock = data.variants.reduce((sum, v) => sum + v.stock, 0);
            modalProductStock.textContent = 'Stok: ' + totalStock;

            if (data.product.image) {
                modalProductImage.src = data.product.image;
            }

            if (!data.options || data.options.length === 0) {
                modalVariantOptions.innerHTML = `
                    <div class="variant-empty-state">Produk ini tidak memiliki varian.</div>
                `;
                return;
            }

            renderOptions(data.options, data.variants);
        })
        .catch(error => {
            modalVariantOptions.innerHTML = `
                <div class="variant-empty-state error">
                    ❌ ${error.message}
                    <br>
                    <button onclick="openVariantModal(${productId}, '${mode}')" class="variant-retry-btn">Coba lagi</button>
                </div>
            `;
        });
    };

    // ============================================
    // CLOSE MODAL
    // ============================================
    window.closeVariantModal = function(event) {
        if (event && event.target !== event.currentTarget) return;
        modal.classList.remove('active');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };

    // ============================================
    // RENDER OPTIONS
    // ============================================
    function renderOptions(options, variants) {
        modalVariantOptions.innerHTML = '';

        options.forEach(option => {
            const group = document.createElement('div');
            group.className = 'variant-option-group';

            let valuesHtml = '';
            
            option.values.forEach(value => {
                valuesHtml += `
                    <button type="button"
                        class="variant-option-btn"
                        data-option-id="${option.id}"
                        data-value-id="${value.id}"
                        data-image="${value.image || ''}">
                        ${value.value}
                    </button>
                `;
            });

            group.innerHTML = `
                <label class="variant-option-label">${option.name}</label>
                <div class="variant-option-values" data-option-id="${option.id}">
                    ${valuesHtml}
                </div>
            `;

            modalVariantOptions.appendChild(group);
        });

        // Event Delegation Klik Opsi
        modalVariantOptions.addEventListener('click', function(e) {
            const btn = e.target.closest('.variant-option-btn');
            if (!btn || btn.disabled) return;

            const optionId = btn.dataset.optionId;
            const valueId = parseInt(btn.dataset.valueId);

            // 1. Simpan nilai yang dipilih
            selectedValues[optionId] = valueId;

            // 2. Update status disabled & sesuaikan opsi grup lain jika opsi aktifnya jadi disabled
            updateOptionAvailability(variants);

            // 3. Refresh visual class active ke semua tombol
            refreshActiveButtons();

            // 4. Update preview gambar jika ada
            const image = btn.dataset.image;
            if (image) {
                modalProductImage.src = image;
            }

            // 5. Cek kecocokan varian & update harga/stok
            checkSelection(variants);
        });

        // Auto select opsi pertama yang memiliki stok di tiap grup
        autoSelectInitialOptions(variants);
    }

    function refreshActiveButtons() {
        document.querySelectorAll('.variant-option-btn').forEach(btn => {
            const optionId = btn.dataset.optionId;
            const valueId = parseInt(btn.dataset.valueId);

            if (selectedValues[optionId] === valueId && !btn.disabled) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    function autoSelectInitialOptions(variants) {
        // Cari varian pertama yang masih memiliki stok
        const availableVariant = variants.find(v => v.stock > 0);

        if (availableVariant && availableVariant.values) {
            document.querySelectorAll('.variant-option-btn').forEach(btn => {
                const optionId = btn.dataset.optionId;
                const valueId = parseInt(btn.dataset.valueId);

                if (availableVariant.values.includes(valueId)) {
                    selectedValues[optionId] = valueId;
                }
            });
        }

        updateOptionAvailability(variants);
        refreshActiveButtons();
        checkSelection(variants);
    }

    function updateOptionAvailability(variants) {
        const groups = document.querySelectorAll('.variant-option-values');

        groups.forEach(group => {
            const optionId = group.dataset.optionId;
            const buttons = group.querySelectorAll('.variant-option-btn');

            buttons.forEach(btn => {
                const valueId = parseInt(btn.dataset.valueId);

                // Cek apakah ada varian dengan kombinasi ini yang memiliki stok > 0
                const isAvailable = variants.some(variant => {
                    if (!variant.values || variant.stock <= 0) return false;
                    if (!variant.values.includes(valueId)) return false;

                    // Cocokkan dengan opsi grup LAIN yang sedang aktif
                    for (const [otherOptId, otherValId] of Object.entries(selectedValues)) {
                        if (otherOptId !== optionId && !variant.values.includes(otherValId)) {
                            return false;
                        }
                    }
                    return true;
                });

                btn.disabled = !isAvailable;
                btn.classList.toggle('disabled', !isAvailable);

                // Jika tombol yang tadinya aktif menjadi disabled, hapus dari selection
                if (!isAvailable && selectedValues[optionId] === valueId) {
                    delete selectedValues[optionId];
                }
            });

            // Jika tidak ada tombol yang aktif di grup ini setelah update, auto-select tombol pertama yang valid
            if (!selectedValues[optionId]) {
                const firstValid = group.querySelector('.variant-option-btn:not([disabled])');
                if (firstValid) {
                    selectedValues[optionId] = parseInt(firstValid.dataset.valueId);
                }
            }
        });
    }

    // ============================================
    // CHECK SELECTION
    // ============================================
    function checkSelection(variants) {
        const totalOptions = document.querySelectorAll('.variant-option-values').length;
        const selectedCount = Object.keys(selectedValues).length;

        if (selectedCount < totalOptions) {
            modalAddToCartBtn.disabled = true;
            return;
        }

        const selectedIds = Object.values(selectedValues).map(Number).sort();

        const matched = variants.find(v => {
            if (!v.values) return false;
            const ids = v.values.map(Number).sort();
            return JSON.stringify(ids) === JSON.stringify(selectedIds) && v.stock > 0;
        });

        if (matched) {
            currentVariant = matched;
            modalSelectedVariant.value = matched.id;
            
            const price = matched.discount_price ?? matched.price;
            modalProductPrice.textContent = 'Rp ' + formatRupiah(price);
            modalProductStock.textContent = 'Stok: ' + matched.stock;
            modalQtyInput.max = matched.stock;
            modalQtyInput.value = 1;
            modalAddToCartBtn.disabled = false;
            modalError.classList.add('hidden');
        } else {
            currentVariant = null;
            modalSelectedVariant.value = '';
            modalAddToCartBtn.disabled = true;
            
            // 🔥 CEK APAKAH KOMBINASI ADA TAPI STOK HABIS
            const hasVariant = variants.some(v => {
                if (!v.values) return false;
                const ids = v.values.map(Number).sort();
                return JSON.stringify(ids) === JSON.stringify(selectedIds);
            });

            if (hasVariant) {
                modalError.textContent = 'Stok habis untuk kombinasi ini.';
            } else {
                modalError.textContent = 'Kombinasi tidak tersedia.';
            }
            modalError.classList.remove('hidden');
        }
    }

    // ============================================
    // QUANTITY BUTTONS
    // ============================================
    document.querySelectorAll('.variant-qty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            let value = parseInt(modalQtyInput.value) || 1;
            const max = parseInt(modalQtyInput.max) || 999;

            if (this.dataset.action === 'increase' && value < max) {
                value++;
            } else if (this.dataset.action === 'decrease' && value > 1) {
                value--;
            }
            modalQtyInput.value = value;
        });
    });

    // ============================================
    // 🔥 ADD TO CART FROM MODAL
    // ============================================
    modalAddToCartBtn.addEventListener('click', function() {
        const variantId = modalSelectedVariant.value;
        const productId = modalProductId.value;
        const quantity = parseInt(modalQtyInput.value) || 1;
        const mode = modalActionMode.value;

        if (!variantId) {
            modalError.textContent = 'Pilih varian terlebih dahulu!';
            modalError.classList.remove('hidden');
            return;
        }

        const variant = productVariants.find(v => v.id == variantId);
        if (variant && quantity > variant.stock) {
            modalError.textContent = 'Stok tidak mencukupi! Tersedia: ' + variant.stock;
            modalError.classList.remove('hidden');
            return;
        }

        this.disabled = true;
        this.textContent = 'Memproses...';

        if (mode === 'buy_now') {
            window._buyNowMode = true;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        let variantImage = null;
        if (variant) {
            // Cari thumbnail dengan option-value-id yang sesuai
            const thumb = document.querySelector(`.image-thumb[data-option-value-id="${variant.values[0]}"]`);
            if (thumb) {
                variantImage = thumb.dataset.image;
            }
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
                quantity: quantity,
                variant_image: variantImage
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('🛒 Variant modal: cart add success, count:', data.count);
                
                if (typeof window.updateNavbarCartCount === 'function') {
                    window.updateNavbarCartCount(data.count);
                } else if (typeof window.updateCartCount === 'function') {
                    window.updateCartCount(data.count);
                } else {
                    const cartCountEl = document.getElementById('cart-count');
                    if (cartCountEl) {
                        cartCountEl.textContent = data.count || 0;
                        cartCountEl.style.display = (data.count > 0) ? 'inline-flex' : 'none';
                    }
                }
                
                document.dispatchEvent(new CustomEvent('cart-updated', {
                    detail: { count: data.count, message: data.message }
                }));
                
                showToast(data.message || 'Produk ditambahkan ke keranjang!', 'success');
                
                if (typeof window.loadCartPopup === 'function') {
                    window.loadCartPopup();
                }
                
                // 🔥 RELOAD WISHLIST POPUP JIKA DARI WISHLIST
                if (window._fromWishlist) {
                    window._fromWishlist = false;
                    if (typeof loadWishlistPopup === 'function') {
                        setTimeout(function() {
                            loadWishlistPopup();
                        }, 500);
                    }
                }
                
                closeVariantModal();
                
                if (mode === 'buy_now') {
                    setTimeout(() => {
                        window.location.href = window.customerRoutes.checkout;
                    }, 500);
                }
            } else {
                modalError.textContent = data.message || 'Gagal menambahkan ke keranjang.';
                modalError.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalError.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
            modalError.classList.remove('hidden');
        })
        .finally(() => {
            this.disabled = false;
            this.textContent = mode === 'buy_now' ? 'Beli Sekarang' : 'Tambah ke Keranjang';
        });
    });

    // ============================================
    // ESC CLOSE
    // ============================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeVariantModal();
        }
    });

});

// ============================================
// HELPER: FORMAT RUPIAH
// ============================================
function formatRupiah(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}