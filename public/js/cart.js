// ============================================
// CART FUNCTIONS - LENGKAP & TERSTRUKTUR
// ============================================

/**
 * Tambah ke Keranjang - Cek varian dulu
 */
function addToCart(productId) {
    fetch(`/api/products/${productId}/variants`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.variants && data.variants.length > 0) {
            if (typeof openVariantModal === 'function') {
                openVariantModal(productId, 'add_to_cart');
            } else {
                console.error('openVariantModal tidak tersedia');
                showToast('Terjadi kesalahan', 'error');
            }
        } else {
            addToCartDirect(productId);
        }
    })
    .catch(error => {
        console.error('Error checking variants:', error);
        addToCartDirect(productId);
    });
}

function updateNavbarCartCount(count) {
    console.log('🛒 Updating navbar cart count to:', count);
    
    const finalCount = parseInt(count) || 0;
    
    // Update semua elemen dengan id cart-count
    const cartCountElements = document.querySelectorAll('#cart-count');
    console.log('📦 Found cart-count elements:', cartCountElements.length);
    
    cartCountElements.forEach(function(element, index) {
        console.log(`📦 Updating element ${index}:`, element);
        element.textContent = finalCount;
        
        if (finalCount > 0) {
            element.style.display = 'inline-flex';
            element.style.visibility = 'visible';
            element.classList.remove('hidden');
        } else {
            element.style.display = 'none';
            element.style.visibility = 'hidden';
            element.classList.add('hidden');
        }
    });
    
    // Update elemen dengan class .cart-count atau .cart-badge
    document.querySelectorAll('.cart-count, .cart-badge, .cart-counter').forEach(function(el) {
        el.textContent = finalCount;
        if (finalCount > 0) {
            el.style.display = 'inline-flex';
            el.classList.remove('hidden');
        } else {
            el.style.display = 'none';
            el.classList.add('hidden');
        }
    });
}

document.addEventListener('cart-updated', function(e) {
    console.log('🛒 Cart updated event received:', e.detail);
    if (e.detail && e.detail.count !== undefined) {
        updateNavbarCartCount(e.detail.count);
    } else {
        loadCartCount(); // Reload from server
    }
});

document.addEventListener('wishlist-updated', function(e) {
    console.log('❤️ Wishlist updated event received:', e.detail);
    if (e.detail && e.detail.count !== undefined) {
        updateNavbarWishlistCount(e.detail.count);
    } else {
        loadWishlistCount(); // Reload from server
    }
});

function updateNavbarWishlistCount(count) {
    console.log('❤️ Updating navbar wishlist count to:', count);
    
    const finalCount = parseInt(count) || 0;
    
    const wishlistCountElements = document.querySelectorAll('#wishlist-count');
    wishlistCountElements.forEach(function(element) {
        element.textContent = finalCount;
        
        if (finalCount > 0) {
            element.style.display = 'inline-flex';
            element.style.visibility = 'visible';
            element.classList.remove('hidden');
        } else {
            element.style.display = 'none';
            element.style.visibility = 'hidden';
            element.classList.add('hidden');
        }
    });
    
    document.querySelectorAll('.wishlist-count, .wishlist-badge, .wishlist-counter').forEach(function(el) {
        el.textContent = finalCount;
        if (finalCount > 0) {
            el.style.display = 'inline-flex';
            el.classList.remove('hidden');
        } else {
            el.style.display = 'none';
            el.classList.add('hidden');
        }
    });
}

/**
 * Tambah ke Keranjang - Langsung (tanpa varian)
 */
function addToCartDirect(productId, variantId = null, quantity = 1) {
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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const count = data.count || data.cart_count || 0;
            console.log('✅ Cart add success, count:', count);
            
            // 🔥 UPDATE CART COUNT
            updateNavbarCartCount(count);
            
            // 🔥 TRIGGER EVENT
            document.dispatchEvent(new CustomEvent('cart-updated', {
                detail: { count: count, message: data.message }
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
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan', 'error');
    })
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<iconify-icon icon="solar:cart-linear"></iconify-icon>';
        }
    });
}


/**
 * Beli Sekarang
 */
function buyNow(productId) {
    // Cek varian produk
    fetch(`/api/products/${productId}/variants`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.variants && data.variants.length > 0) {
            // Ada varian → buka modal mode buy_now
            if (typeof openVariantModal === 'function') {
                window._buyNowMode = true;
                openVariantModal(productId, 'buy_now');
            }
        } else {
            // Tidak ada varian → langsung checkout
            window._buyNowMode = true;
            addToCartDirect(productId);
        }
    })
    .catch(() => {
        // Jika error, langsung checkout
        window._buyNowMode = true;
        addToCartDirect(productId);
    });
}

/**
 * Tambah ke Wishlist
 */
function addToWishlist(productId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    const btn = document.querySelector(`.product_layout_box[data-product-id="${productId}"] .add_to_wishlist_btn`);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '⏳';
    }
    
    fetch(window.customerRoutes.wishlistAdd, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 🔥 UPDATE WISHLIST COUNT
            if (typeof window.updateNavbarWishlistCount === 'function') {
                window.updateNavbarWishlistCount(data.count || 0);
            }
            
            // 🔥 UPDATE ICON HEART
            updateWishlistIcon(productId, data.in_wishlist);
            
            // 🔥 TRIGGER EVENT - TANPA TOAST DI SINI
            document.dispatchEvent(new CustomEvent('wishlist-updated', {
                detail: { 
                    count: data.count, 
                    product_id: productId,
                    in_wishlist: data.in_wishlist
                }
            }));
            
            // 🔥 TAMPILKAN TOAST HANYA DI SINI
            showToast(data.message || 'Produk ditambahkan ke wishlist!', 'success');
            
            if (typeof loadWishlistPopup === 'function') {
                loadWishlistPopup();
            }
        } else {
            showToast(data.message || 'Gagal menambahkan ke wishlist', 'error');
        }
    })
    .catch(() => {
        showToast('Terjadi kesalahan', 'error');
    })
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            // Icon akan diupdate oleh updateWishlistIcon
            updateWishlistIcon(productId);
        }
    });
}


/**
 * 🔥 UPDATE CART COUNT - PASTIKAN BEKERJA
 */
function updateCartCount(count) {
    console.log('Updating cart count to:', count); // Debug
    
    // Cari semua elemen dengan id cart-count
    const cartCountElements = document.querySelectorAll('#cart-count');
    
    cartCountElements.forEach(function(element) {
        element.textContent = count || 0;
        
        if (count > 0) {
            element.style.display = 'inline-flex';
            element.style.visibility = 'visible';
        } else {
            element.style.display = 'none';
            element.style.visibility = 'hidden';
        }
    });
    
    // Juga update elemen dengan class .cart-count jika ada
    document.querySelectorAll('.cart-count, .cart-badge').forEach(function(el) {
        el.textContent = count || 0;
        if (count > 0) {
            el.style.display = 'inline-flex';
        } else {
            el.style.display = 'none';
        }
    });
}

function updateWishlistIcon(productId, inWishlist = null) {
    const btn = document.querySelector(`.product_layout_box[data-product-id="${productId}"] .add_to_wishlist_btn`);
    if (!btn) return;
    
    // Jika inWishlist tidak diberikan, cek dari data attribute
    if (inWishlist === null) {
        inWishlist = btn.dataset.inWishlist === 'true';
    }
    
    if (inWishlist) {
        btn.innerHTML = '<iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>';
        btn.classList.add('active');
        btn.dataset.inWishlist = 'true';
    } else {
        btn.innerHTML = '<iconify-icon icon="solar:heart-linear"></iconify-icon>';
        btn.classList.remove('active');
        btn.dataset.inWishlist = 'false';
    }
}

function loadWishlistStatus() {
    if (!window.customerRoutes.wishlistStatus) {
        console.warn('Wishlist status route not configured');
        return;
    }
    
    fetch(window.customerRoutes.wishlistStatus, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.wishlist_ids) {
            const wishlistIds = data.wishlist_ids.map(id => parseInt(id));
            
            // Update semua tombol wishlist
            document.querySelectorAll('.add_to_wishlist_btn').forEach(function(btn) {
                const productId = parseInt(btn.closest('.product_layout_box')?.dataset?.productId);
                if (productId) {
                    const inWishlist = wishlistIds.includes(productId);
                    updateWishlistIcon(productId, inWishlist);
                }
            });
            
            console.log('❤️ Wishlist status loaded:', wishlistIds);
        }
    })
    .catch(error => {
        console.error('Error loading wishlist status:', error);
    });
}

/**
 * 🔥 UPDATE WISHLIST COUNT - PASTIKAN BEKERJA
 */
function updateWishlistCount(count) {
    console.log('Updating wishlist count to:', count); // Debug
    
    // Cari semua elemen dengan id wishlist-count
    const wishlistCountElements = document.querySelectorAll('#wishlist-count');
    
    wishlistCountElements.forEach(function(element) {
        element.textContent = count || 0;
        
        if (count > 0) {
            element.style.display = 'inline-flex';
            element.style.visibility = 'visible';
        } else {
            element.style.display = 'none';
            element.style.visibility = 'hidden';
        }
    });
    
    // Juga update elemen dengan class .wishlist-count jika ada
    document.querySelectorAll('.wishlist-count, .wishlist-badge').forEach(function(el) {
        el.textContent = count || 0;
        if (count > 0) {
            el.style.display = 'inline-flex';
        } else {
            el.style.display = 'none';
        }
    });
}

/**
 * Load Cart Count dari Server
 */
function loadCartCount() {
    fetch(window.customerRoutes.cartCount, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.count !== undefined) {
            updateNavbarCartCount(data.count);
        }
    })
    .catch(error => {
        console.error('Error loading cart count:', error);
    });
}

/**
 * Load Wishlist Count dari Server
 */
function loadWishlistCount() {
    if (window.customerRoutes.wishlistPopup) {
        fetch(window.customerRoutes.wishlistPopup, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.count !== undefined) {
                updateNavbarWishlistCount(data.count);
            }
        })
        .catch(error => {
            console.error('Error loading wishlist count:', error);
        });
    }
}

/**
 * Show Toast Notification
 */
function showToast(message, type = 'info') {
    // Hapus toast lama
    const oldToast = document.querySelector('.custom-toast');
    if (oldToast) {
        oldToast.remove();
    }
    
    const toast = document.createElement('div');
    toast.className = `custom-toast custom-toast-${type}`;
    
    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    };
    
    toast.innerHTML = `
        <span>${icons[type] || 'ℹ️'}</span>
        <span>${message}</span>
        <span class="custom-toast-close">×</span>
    `;
    
    document.body.appendChild(toast);
    
    // Auto close
    setTimeout(() => {
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
    
    // Close button
    toast.querySelector('.custom-toast-close').addEventListener('click', function() {
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 300);
    });
}

function removeFromWishlist(productId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    fetch(window.customerRoutes.wishlistRemove, {
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
            updateNavbarWishlistCount(data.count || 0);
            showToast(data.message || 'Produk dihapus dari wishlist', 'success');
            
            if (typeof loadWishlistPopup === 'function') {
                loadWishlistPopup();
            }
        } else {
            showToast(data.message || 'Gagal menghapus dari wishlist', 'error');
        }
    })
    .catch(() => {
        showToast('Terjadi kesalahan', 'error');
    });
}

// Load count saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, loading counts...');
    loadCartCount();
    loadWishlistCount();
});

// Expose ke global
window.addToCart = addToCart;
window.addToCartDirect = addToCartDirect;
// 🔥 HANYA definisikan buyNow jika belum ada. Halaman detail produk (products/show)
// punya buyNow() sendiri yang menggunakan varian terpilih di form - JANGAN ditimpa.
if (typeof window.buyNow !== 'function') {
    window.buyNow = buyNow;
}
window.addToWishlist = addToWishlist;
window.removeFromWishlist = removeFromWishlist;
window.updateNavbarCartCount = updateNavbarCartCount;
window.updateNavbarWishlistCount = updateNavbarWishlistCount;
window.loadCartCount = loadCartCount;
window.loadWishlistCount = loadWishlistCount;
window.showToast = showToast;
window.updateCartCount = updateNavbarCartCount;

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, loading counts...');
    loadCartCount();
    loadWishlistCount();
});