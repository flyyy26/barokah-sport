<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\MarketplaceController;
use App\Http\Controllers\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\Customer\CustomerHomeController;
use App\Http\Controllers\Customer\CustomerProductController;
use App\Http\Controllers\Customer\CustomerCategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\AgenWebsiteController;
use App\Http\Controllers\Api\VillageController;
use App\Http\Controllers\Customer\BiteshipController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\ArticleCategoryAjaxController;
use App\Http\Controllers\Customer\CustomerArticleController;
use App\Http\Controllers\Customer\ContactController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\TermController;
use App\Http\Controllers\Customer\CustomerTermController;
use App\Http\Controllers\Admin\PrivacyPolicyController;
use App\Http\Controllers\Customer\CustomerPrivacyController;

// ============================================
// CUSTOMER FRONTEND
// ============================================

Route::get('/', [CustomerHomeController::class, 'index'])->name('customer.home');
Route::get('/kontak', [ContactController::class, 'index'])->name('customer.contact');
Route::get('/syarat-dan-ketentuan', [CustomerTermController::class, 'index'])->name('customer.terms');
Route::get('/kebijakan-privasi', [CustomerPrivacyController::class, 'index'])->name('customer.privacy');

// ============================================
// API PRODUCT VARIANTS (untuk modal)
// ============================================
Route::get('/api/products/{product}/variants', function (App\Models\Product $product) {
    // 🔥 LOAD SEMUA RELASI YANG DIPERLUKAN
    $product->load([
        'options' => function ($query) {
            $query->orderBy('sort_order', 'asc');
        },
        'options.values' => function ($query) {
            $query->orderBy('sort_order', 'asc');
        },
        'variants' => function ($query) {
            $query->where('stock', '>', 0); // Hanya varian dengan stok
        },
        'variants.variantValues',
        'images' => function ($query) {
            $query->orderBy('sort_order', 'asc');
        },
    ]);

    // 🔥 CEK APAKAH PRODUK PUNYA OPTIONS
    $hasOptions = $product->options->isNotEmpty();

    $options = $product->options->map(function ($option) {
        return [
            'id' => (int) $option->id,
            'name' => $option->name,
            'values' => $option->values->map(function ($value) {
                return [
                    'id' => (int) $value->id,
                    'value' => $value->value,
                    'image' => $value->image ? Storage::url($value->image) : null,
                ];
            })->values()->toArray(),
        ];
    })->values()->toArray();

    // 🔥 BUILD VARIANT DATA DENGAN VALUE IDS
    $variants = $product->variants->map(function ($variant) {
        $valueIds = $variant->variantValues->pluck('product_option_value_id')->map(function($id) {
            return (int) $id;
        })->toArray();

        return [
            'id' => (int) $variant->id,
            'sku' => $variant->sku,
            'price' => (float) $variant->price,
            'discount_price' => $variant->discount_price ? (float) $variant->discount_price : null,
            'stock' => (int) $variant->stock,
            'weight' => (int) $variant->weight,
            'image' => $variant->image ? Storage::url($variant->image) : null,
            'values' => $valueIds,
        ];
    })->values()->toArray();

    // 🔥 AMBIL GAMBAR PRODUK
    $productImage = $product->images->first() 
        ? Storage::url($product->images->first()->image) 
        : null;

    return response()->json([
        'success' => true,
        'product' => [
            'id' => (int) $product->id,
            'name' => $product->name,
            'image' => $productImage,
            'has_options' => $hasOptions,
        ],
        'options' => $options,
        'variants' => $variants,
        'debug' => [
            'options_count' => count($options),
            'variants_count' => count($variants),
        ]
    ]);
})->name('api.products.variants');

Route::get('/katalog', [CustomerProductController::class, 'index'])->name('customer.products.index');
Route::get('/produk/{product:slug}', [CustomerProductController::class, 'show'])->name('customer.products.show');
Route::get('/katalog/terbaru', [CustomerProductController::class, 'latest'])->name('customer.products.latest');
Route::get('/katalog/promo', [CustomerProductController::class, 'promo'])->name('customer.products.promo'); 

Route::get('/artikel', [CustomerArticleController::class, 'index'])
    ->name('customer.articles.index');

Route::get('/artikel/{slug}', [CustomerArticleController::class, 'show'])
    ->name('customer.articles.show');

Route::post('/api/articles/record-view', [App\Http\Controllers\Customer\CustomerArticleController::class, 'recordView'])
    ->name('customer.articles.record-view');

Route::get('/kategori/{category:slug}', [CustomerCategoryController::class, 'show'])->name('customer.categories.show');

// ============================================
// CUSTOMER AUTH
// ============================================

Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
Route::post('/register', [CustomerAuthController::class, 'register'])->name('customer.register.process');

Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
Route::post('/login', [CustomerAuthController::class, 'login'])->name('customer.login.process');
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

// ============================================
// WISHLIST
// ============================================

Route::get('/wishlist', [WishlistController::class, 'index'])->name('customer.wishlist.index');
Route::get('/wishlist/popup', [WishlistController::class, 'popup'])->name('customer.wishlist.popup');
Route::get('/wishlist/status', [WishlistController::class, 'status'])->name('customer.wishlist.status');
Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('customer.wishlist.add');
Route::delete('/wishlist/remove', [WishlistController::class, 'remove'])->name('customer.wishlist.remove');
Route::delete('/wishlist/clear', [WishlistController::class, 'clear'])->name('customer.wishlist.clear');

// ============================================
// CART
// ============================================

Route::get('/cart', [CartController::class, 'index'])->name('customer.cart.index');
Route::get('/cart/popup', [CartController::class, 'popup'])->name('customer.cart.popup');
Route::post('/cart/add', [CartController::class, 'add'])->name('customer.cart.add');
Route::put('/cart/update', [CartController::class, 'update'])->name('customer.cart.update');
Route::delete('/cart/remove', [CartController::class, 'remove'])->name('customer.cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('customer.cart.clear');
Route::get('/cart/count', [CartController::class, 'count'])->name('customer.cart.count');

// ============================================
// BUY NOW
// ============================================
Route::post('/buy-now', [CheckoutController::class, 'buyNow'])->name('customer.buy-now');

// ============================================
// CHECKOUT
// ============================================

Route::get('/checkout', [CheckoutController::class, 'index'])->name('customer.checkout.index');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('customer.checkout.process');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('customer.checkout.success');

// ============================================
// API AGENWEBSITE (untuk ongkir)
// ============================================

Route::get('/test-biteship', function () {
    $apiKey = config('services.biteship.api_key');
    
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiKey,
        'Content-Type' => 'application/json',
    ])->post('https://api.biteship.com/v1/rates/couriers', [
        'origin_postal_code' => '10110',
        'destination_postal_code' => '40111',
        'couriers' => ['jne'],
        'items' => [
            [
                'name' => 'Test Product',
                'value' => 100000,
                'weight' => 1000,
                'quantity' => 1,
            ]
        ]
    ]);
    
    return response()->json([
        'status' => $response->status(),
        'body' => $response->json(),
        'headers' => $response->headers(),
    ]);
});

Route::get('/api/villages', [VillageController::class, 'getVillages'])->name('api.villages');
Route::prefix('api/biteship')->group(function () {
    Route::get('/search-location', [BiteshipController::class, 'searchLocation'])->name('api.biteship.search');
    Route::post('/rates', [BiteshipController::class, 'getRates'])->name('api.biteship.rates');
    Route::post('/order', [BiteshipController::class, 'createOrder'])->name('api.biteship.order');
    Route::get('/track/{orderId}', [BiteshipController::class, 'trackOrder'])->name('api.biteship.track');
    Route::get('/waybill/{orderId}', [BiteshipController::class, 'getWaybill'])->name('api.biteship.waybill');
});

// ============================================
// CUSTOMER ACCOUNT (with auth middleware)
// ============================================

Route::middleware(['customer'])->group(function () {

    Route::get('/akun', [AccountController::class, 'index'])->name('customer.account');

    Route::get('/akun/pesanan', [AccountController::class, 'orders'])->name('customer.orders');
    Route::get('/akun/pesanan/{order}', [AccountController::class, 'showOrder'])->name('customer.orders.show');

    Route::get('/akun/alamat/tambah', [AccountController::class, 'createAddress'])->name('customer.addresses.create');
    Route::post('/akun/alamat', [AccountController::class, 'storeAddress'])->name('customer.addresses.store');
    Route::get('/akun/alamat/{address}/edit', [AccountController::class, 'editAddress'])->name('customer.addresses.edit');
    Route::put('/akun/alamat/{address}', [AccountController::class, 'updateAddress'])->name('customer.addresses.update');
    Route::delete('/akun/alamat/{address}', [AccountController::class, 'destroyAddress'])->name('customer.addresses.destroy');

});

// ============================================
// ADMIN
// ============================================

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // CATEGORY
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

    // PRODUCT
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/bulk-delete', [ProductController::class, 'bulkDestroy'])->name('admin.products.bulk-destroy');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::delete('/products/{product}/images/{image}', [ProductController::class, 'destroyImage'])->name('admin.products.images.destroy');
    Route::post('/products/check-sku', [ProductController::class, 'checkSku'])->name('admin.products.check-sku');

    // BANNER
    Route::get('/banners', [BannerController::class, 'index'])->name('admin.banners.index');
    Route::get('/banners/create', [BannerController::class, 'create'])->name('admin.banners.create');
    Route::post('/banners', [BannerController::class, 'store'])->name('admin.banners.store');
    Route::get('/banners/{banner}/edit', [BannerController::class, 'edit'])->name('admin.banners.edit');
    Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('admin.banners.update');
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('admin.banners.destroy');

    // MARKETPLACE
    Route::get('/marketplaces', [MarketplaceController::class, 'index'])->name('admin.marketplaces.index');
    Route::get('/marketplaces/create', [MarketplaceController::class, 'create'])->name('admin.marketplaces.create');
    Route::post('/marketplaces', [MarketplaceController::class, 'store'])->name('admin.marketplaces.store');
    Route::get('/marketplaces/{marketplace}/edit', [MarketplaceController::class, 'edit'])->name('admin.marketplaces.edit');
    Route::put('/marketplaces/{marketplace}', [MarketplaceController::class, 'update'])->name('admin.marketplaces.update');
    Route::delete('/marketplaces/{marketplace}', [MarketplaceController::class, 'destroy'])->name('admin.marketplaces.destroy');

    // SETTINGS
    Route::get('/settings', [SettingController::class, 'edit'])->name('admin.settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('admin.settings.update');

    // ORDERS
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');
    Route::put('/orders/{order}/payment', [OrderController::class, 'updatePayment'])->name('admin.orders.payment');
    Route::put('/orders/{order}/shipping', [OrderController::class, 'updateShipping'])->name('admin.orders.shipping');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('admin.orders.invoice');

    Route::post('/features', [FeatureController::class, 'store'])->name('admin.features.store');

    Route::get('/articles', [ArticleController::class, 'index'])->name('admin.articles.index');
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('admin.articles.create');
    Route::post('/articles', [ArticleController::class, 'store'])->name('admin.articles.store');
    Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->name('admin.articles.edit');
    Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('admin.articles.update');
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('admin.articles.destroy');

    // 🔥 ARTICLE CATEGORIES AJAX
    Route::get('/article-categories/ajax', [ArticleCategoryAjaxController::class, 'index'])->name('admin.article-categories.ajax');
    Route::post('/article-categories/ajax', [ArticleCategoryAjaxController::class, 'store'])->name('admin.article-categories.ajax.store');
    Route::delete('/article-categories/ajax/{id}', [ArticleCategoryAjaxController::class, 'destroy'])->name('admin.article-categories.ajax.destroy');

    Route::patch('faqs/{faq}/toggle', [FaqController::class, 'toggle'])->name('admin.faqs.toggle');
    Route::get('/faqs', [FaqController::class, 'index'])->name('admin.faqs.index');
    Route::get('/faqs/create', [FaqController::class, 'create'])->name('admin.faqs.create');
    Route::post('/faqs', [FaqController::class, 'store'])->name('admin.faqs.store');
    Route::get('/faqs/{faq}/edit', [FaqController::class, 'edit'])->name('admin.faqs.edit');
    Route::put('/faqs/{faq}', [FaqController::class, 'update'])->name('admin.faqs.update');
    Route::delete('/faqs/{faq}', [FaqController::class, 'destroy'])->name('admin.faqs.destroy');

    Route::get('/terms', [TermController::class, 'index'])->name('admin.terms.index');
    Route::put('/terms', [TermController::class, 'update'])->name('admin.terms.update');
    Route::patch('/terms/toggle', [TermController::class, 'toggle'])->name('admin.terms.toggle');

    Route::get('/privacy', [PrivacyPolicyController::class, 'index'])->name('admin.privacy.index');
    Route::put('/privacy', [PrivacyPolicyController::class, 'update'])->name('admin.privacy.update');
    Route::patch('/privacy/toggle', [PrivacyPolicyController::class, 'toggle'])->name('admin.privacy.toggle');
});