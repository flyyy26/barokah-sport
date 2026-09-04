<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Customer;
use App\Models\Cart;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Traits\ProductDiscountTrait;
use App\Services\BiteshipService;

class CheckoutController extends Controller
{
    use ProductDiscountTrait;
    
    protected $biteship;

    public function __construct(BiteshipService $biteship)
    {
        $this->biteship = $biteship;
    }

    // ============================================
    // HELPER - GET EFFECTIVE PRICE
    // ============================================

    private function getEffectivePrice($variant, $product)
    {
        if ($variant) {
            return $variant->effective_price;
        }
        return $product->price;
    }

    // ============================================
    // HELPER - GET SUBTOTAL
    // ============================================

    private function getSubtotalFromCart($cart): float
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        return $subtotal;
    }

    // ============================================
    // INDEX - Tampilkan Halaman Checkout
    // ============================================

    public function index()
    {
        $cart = session()->get('cart', []);
        $isBuyNow = session()->get('is_buy_now', false);
        
        \Log::info('Checkout - Cart from session:', ['cart' => $cart]);
        
        if (empty($cart)) {
            $user = Auth::guard('customer')->user();
            if ($user) {
                $cartItems = Cart::with(['product.images', 'variant'])
                    ->where('user_id', $user->id)
                    ->get();
                
                if ($cartItems->isNotEmpty()) {
                    $cart = [];
                    foreach ($cartItems as $item) {
                        $variant = $item->variant;
                        $product = $item->product;
                        
                        $price = $this->getEffectivePrice($variant, $product);
                        
                        $variantImage = null;
                        if ($variant) {
                            $variantImage = $this->getVariantImage($variant, $product);
                        }
                        if (!$variantImage) {
                            $variantImage = $product->images->first()?->image;
                        }
                        
                        $cart[] = [
                            'id' => $item->id,
                            'product_id' => $product->id,
                            'variant_id' => $variant?->id,
                            'product_name' => $product->name,
                            'variant_name' => $variant ? $variant->option_combination : null,
                            'price' => $price,
                            'original_price' => $variant?->price ?? $product->price,
                            'quantity' => $item->quantity,
                            'image' => $variantImage,
                            'slug' => $product->slug,
                            'weight' => $variant?->weight ?? $product->weight ?? 1000,
                        ];
                    }
                    
                    session()->put('cart', $cart);
                }
            }
        }

        if (empty($cart)) {
            if (session()->has('old_cart_backup') && !empty(session()->get('old_cart_backup'))) {
                session()->put('cart', session()->get('old_cart_backup'));
                session()->forget('old_cart_backup');
                session()->forget('is_buy_now');
                return redirect()->route('customer.cart.index');
            }
            
            return redirect()
                ->route('customer.cart.index')
                ->with('error', 'Keranjang belanja kosong.');
        }

        $subtotal = $this->getSubtotalFromCart($cart);

        \Log::info('Checkout - Cart items:', ['count' => count($cart), 'subtotal' => $subtotal]);

        // Cek stok
        foreach ($cart as $key => $item) {
            if ($item['variant_id']) {
                $variant = ProductVariant::find($item['variant_id']);
                if (!$variant || $variant->stock < $item['quantity']) {
                    if (session()->has('old_cart_backup') && !empty(session()->get('old_cart_backup'))) {
                        session()->put('cart', session()->get('old_cart_backup'));
                        session()->forget('old_cart_backup');
                        session()->forget('is_buy_now');
                    }
                    return redirect()
                        ->route('customer.cart.index')
                        ->with('error', "Stok {$item['product_name']} tidak mencukupi.");
                }
            }
        }

        // ============================================
        // 🔥 VOUCHER SECTION
        // ============================================
        
        // Get available public vouchers
        $availableVouchers = Voucher::publicActive()->get();

        // Get applied voucher from session
        $appliedVoucher = null;
        $voucherDiscount = 0;
        
        if (session()->has('voucher_code')) {
            $voucher = Voucher::where('code', session('voucher_code'))->first();
            if ($voucher) {
                $userId = Auth::guard('customer')->id();
                $eligibility = $voucher->checkEligibility($subtotal, $userId);
                if ($eligibility['eligible']) {
                    $appliedVoucher = $voucher;
                    $voucherDiscount = $voucher->calculateDiscount($subtotal);
                } else {
                    // Voucher tidak valid lagi, hapus dari session
                    session()->forget('voucher_code');
                    session()->forget('voucher_discount');
                }
            } else {
                session()->forget('voucher_code');
                session()->forget('voucher_discount');
            }
        }

        $addresses = [];
        $customer = null;
        $defaultAddress = null;
        $shippingCost = session()->get('shipping_cost', 0);
        
        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
            $addresses = $customer->addresses()->orderBy('is_default', 'desc')->get();
            $defaultAddress = $customer->addresses()->where('is_default', true)->first();
        }

        return view('customer.checkout.index', compact(
            'cart', 
            'subtotal', 
            'customer', 
            'addresses',
            'defaultAddress',
            'isBuyNow',
            'availableVouchers',
            'appliedVoucher',
            'voucherDiscount',
            'shippingCost'  // Add this
        ));
    }

    // ============================================
    // APPLY VOUCHER - Apply Voucher to Checkout
    // ============================================

    public function applyVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string|exists:vouchers,code'
        ]);

        $voucher = Voucher::where('code', strtoupper(trim($request->voucher_code)))->first();
        
        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Kode voucher tidak ditemukan.'
            ], 404);
        }

        // Get current cart and subtotal
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang belanja kosong.'
            ], 400);
        }

        $subtotal = $this->getSubtotalFromCart($cart);
        $userId = Auth::guard('customer')->id();

        // Check eligibility
        $eligibility = $voucher->checkEligibility($subtotal, $userId);

        if (!$eligibility['eligible']) {
            return response()->json([
                'success' => false,
                'message' => $eligibility['message']
            ], 400);
        }

        // Calculate discount
        $discount = $voucher->calculateDiscount($subtotal);
        
        if ($discount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher tidak memberikan potongan untuk transaksi ini.'
            ], 400);
        }

        // Apply voucher to session
        session()->put('voucher_code', $voucher->code);
        session()->put('voucher_discount', $discount);

        // Get shipping cost from session if exists
        $shippingCost = session()->get('shipping_cost', 0);
        $total = $subtotal + $shippingCost - $discount;

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diterapkan!',
            'discount' => $discount,
            'discount_formatted' => 'Rp ' . number_format($discount, 0, ',', '.'),
            'new_subtotal' => $subtotal - $discount,
            'new_total' => $total,
            'voucher' => [
                'code' => $voucher->code,
                'name' => $voucher->name,
                'discount_type' => $voucher->discount_type,
                'discount_value' => (float) $voucher->discount_value,
                'max_discount_amount' => $voucher->max_discount_amount ? (float) $voucher->max_discount_amount : null,
            ]
        ]);
    }

    // ============================================
    // REMOVE VOUCHER - Remove Applied Voucher
    // ============================================

    public function removeVoucher(Request $request)
    {
        session()->forget('voucher_code');
        session()->forget('voucher_discount');

        $cart = session()->get('cart', []);
        $subtotal = $this->getSubtotalFromCart($cart);
        $shippingCost = session()->get('shipping_cost', 0);
        $total = $subtotal + $shippingCost;

        return response()->json([
            'success' => true,
            'message' => 'Voucher dibatalkan.',
            'new_subtotal' => $subtotal,
            'new_total' => $total
        ]);
    }

    // ============================================
    // PROCESS - Proses Checkout (Support Guest)
    // ============================================

    public function process(Request $request)
    {
        $cart = session()->get('cart', []);
        $isBuyNow = session()->get('is_buy_now', false);

        if (empty($cart)) {
            if (session()->has('old_cart_backup') && !empty(session()->get('old_cart_backup'))) {
                session()->put('cart', session()->get('old_cart_backup'));
                session()->forget('old_cart_backup');
                session()->forget('is_buy_now');
                return redirect()->route('customer.cart.index');
            }
            return redirect()->route('customer.cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        // Cek stok
        foreach ($cart as $item) {
            if ($item['variant_id']) {
                $variant = ProductVariant::find($item['variant_id']);
                if (!$variant || $variant->stock < $item['quantity']) {
                    return back()->with('error', "Stok {$item['product_name']} tidak mencukupi.");
                }
            }
        }

        $rules = [
            'shipping_name' => 'required|string|max:100',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_province' => 'required|string|max:100',
            'shipping_city' => 'required|string|max:100',
            'shipping_district' => 'required|string|max:100',
            'shipping_subdistrict' => 'required|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:10',
            'courier' => 'nullable|string|max:50',
            'shipping_service' => 'nullable|string|max:50',
            'shipping_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];

        if (!Auth::guard('customer')->check()) {
            $rules['email'] = 'nullable|email|max:255|unique:customers,email';
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            // ============================================
            // CUSTOMER HANDLING (Logged in or Guest)
            // ============================================
            
            if (Auth::guard('customer')->check()) {
                $customer = Auth::guard('customer')->user();
            } else {
                $existingCustomer = Customer::where('phone', $validated['shipping_phone'])->first();
                
                if ($existingCustomer) {
                    $customer = $existingCustomer;
                    Auth::guard('customer')->login($customer);
                    $request->session()->regenerate();
                } else {
                    $defaultPassword = substr(preg_replace('/[^0-9]/', '', $validated['shipping_phone']), -6);

                    $customer = Customer::create([
                        'name' => $validated['shipping_name'],
                        'email' => $validated['email'] ?? null,
                        'phone' => $validated['shipping_phone'],
                        'password' => $defaultPassword,
                        'is_active' => true,
                    ]);

                    Auth::guard('customer')->login($customer);
                    $request->session()->regenerate();

                    CustomerAddress::create([
                        'customer_id' => $customer->id,
                        'label' => 'Alamat Utama',
                        'recipient_name' => $validated['shipping_name'],
                        'recipient_phone' => $validated['shipping_phone'],
                        'address' => $validated['shipping_address'],
                        'city' => $validated['shipping_city'],
                        'province' => $validated['shipping_province'],
                        'postal_code' => $validated['shipping_postal_code'] ?? '0',
                        'is_default' => true,
                    ]);
                }
            }

            // ============================================
            // CALCULATE TOTALS
            // ============================================
            
            $subtotal = $this->getSubtotalFromCart($cart);
            $shippingCost = $validated['shipping_cost'] ?? 0;
            
            // 🔥 VOUCHER DISCOUNT
            $voucherDiscount = 0;
            $voucherCode = session('voucher_code');
            $appliedVoucher = null;
            
            if ($voucherCode) {
                $voucher = Voucher::where('code', $voucherCode)->first();
                if ($voucher) {
                    $eligibility = $voucher->checkEligibility($subtotal, $customer->id);
                    if ($eligibility['eligible']) {
                        $voucherDiscount = $voucher->calculateDiscount($subtotal);
                        $appliedVoucher = $voucher;
                    } else {
                        // Voucher tidak valid, hapus dari session
                        session()->forget('voucher_code');
                        session()->forget('voucher_discount');
                    }
                }
            }
            
            $total = $subtotal + $shippingCost - $voucherDiscount;

            // ============================================
            // CREATE ORDER
            // ============================================
            
            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'shipping_status' => 'pending',
                'shipping_name' => $validated['shipping_name'],
                'shipping_phone' => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_province' => $validated['shipping_province'],
                'shipping_district' => $validated['shipping_district'] ?? null,
                'shipping_subdistrict' => $validated['shipping_subdistrict'] ?? null,
                'shipping_postal_code' => $validated['shipping_postal_code'] ?? '0',
                'courier' => $validated['courier'] ?? null,
                'service' => $validated['shipping_service'] ?? null,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => $voucherDiscount,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
            ]);

            // ============================================
            // CREATE ORDER ITEMS
            // ============================================
            
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?: null,
                    'product_name' => $item['product_name'],
                    'variant_name' => $item['variant_name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);

                // Decrease stock
                if ($item['variant_id']) {
                    $variant = ProductVariant::find($item['variant_id']);
                    if ($variant) {
                        $variant->decrement('stock', $item['quantity']);
                    }
                }
            }

            // ============================================
            // 🔥 SAVE VOUCHER USAGE
            // ============================================
            
            if ($appliedVoucher && $voucherDiscount > 0) {
                VoucherUsage::create([
                    'voucher_id' => $appliedVoucher->id,
                    'user_id' => $customer->id,
                    'order_id' => $order->id,
                    'discount_applied' => $voucherDiscount,
                ]);

                // Increment used count
                $appliedVoucher->increment('used_count');
            }

            DB::commit();

            // ============================================
            // CLEANUP SESSION
            // ============================================
            
            session()->forget('voucher_code');
            session()->forget('voucher_discount');

            if ($isBuyNow && session()->has('old_cart_backup')) {
                $oldCart = session()->get('old_cart_backup');
                session()->put('cart', $oldCart);
                session()->forget('old_cart_backup');
                session()->forget('is_buy_now');
            } else {
                session()->forget('cart');
            }

            return redirect()
                ->route('customer.checkout.success', $order)
                ->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            if ($isBuyNow && session()->has('old_cart_backup')) {
                session()->put('cart', session()->get('old_cart_backup'));
                session()->forget('old_cart_backup');
                session()->forget('is_buy_now');
            }

            return back()
                ->with('error', 'Gagal memproses pesanan: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ============================================
    // SUCCESS - Halaman Sukses
    // ============================================

    public function success(Order $order)
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $order->load(['items.product', 'items.variant']);

        return view('customer.checkout.success', compact('order'));
    }

    // ============================================
    // BUY NOW - Direct Checkout
    // ============================================

    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = \App\Models\Product::with(['images', 'variants'])->findOrFail($request->product_id);
        $variant = \App\Models\ProductVariant::with(['variantValues.optionValue'])->findOrFail($request->variant_id);

        $effectivePrice = $variant->effective_price;

        $oldCart = session()->get('cart', []);
        session()->put('old_cart_backup', $oldCart);
        session()->forget('cart');

        $variantName = $variant->variantValues->map(function($vv) {
            return $vv->optionValue->value ?? '';
        })->filter()->implode(' / ');

        $imageUrl = null;
        if ($product->images->first()) {
            $imageUrl = $product->images->first()->image;
        }

        $buyNowItems = [
            [
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'product_name' => $product->name,
                'variant_name' => $variantName,
                'price' => $effectivePrice,
                'original_price' => $variant->price,
                'quantity' => $request->quantity,
                'weight' => $variant->weight,
                'image' => $imageUrl,
                'slug' => $product->slug,
            ]
        ];

        session()->put('cart', $buyNowItems);
        session()->put('is_buy_now', true);

        return response()->json([
            'success' => true,
            'redirect' => route('customer.checkout.index'),
        ]);
    }

    // ============================================
    // HELPER - GET VARIANT IMAGE
    // ============================================

    private function getVariantImage($variant, $product)
    {
        if (!$variant) return null;

        if ($variant->image) {
            return $variant->image;
        }

        $variantValueIds = $variant->variantValues->pluck('product_option_value_id')->toArray();
        foreach ($product->options as $option) {
            if (strtolower($option->name) === 'warna' || strtolower($option->name) === 'color') {
                foreach ($option->values as $value) {
                    if (in_array($value->id, $variantValueIds) && $value->image) {
                        return $value->image;
                    }
                }
            }
        }

        return $product->images->first()?->image;
    }

    // ============================================
    // TRACKING - Order Tracking
    // ============================================

    public function trackOrder(Order $order)
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        if (!$order->biteship_order_id) {
            return response()->json([
                'success' => false,
                'message' => 'Order belum memiliki tracking'
            ]);
        }

        $tracking = $this->biteship->trackOrder($order->biteship_order_id);

        return response()->json($tracking);
    }

    public function getWaybill(Order $order)
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        if (!$order->biteship_order_id) {
            return response()->json([
                'success' => false,
                'message' => 'Order belum memiliki resi'
            ]);
        }

        $waybill = $this->biteship->getWaybill($order->biteship_order_id);

        if (isset($waybill['waybill_url'])) {
            return redirect($waybill['waybill_url']);
        }

        return response()->json($waybill);
    }

    public function getAvailableVouchers(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json([
                'success' => true,
                'vouchers' => []
            ]);
        }

        $subtotal = $this->getSubtotalFromCart($cart);
        $userId = Auth::guard('customer')->id();
        
        // Get applied voucher code from session
        $appliedCode = session('voucher_code');
        
        // Get all active public vouchers
        $vouchers = Voucher::publicActive()->get();
        
        $result = [];
        foreach ($vouchers as $voucher) {
            // Skip if already applied
            if ($voucher->code === $appliedCode) {
                continue;
            }
            
            $eligibility = $voucher->checkEligibility($subtotal, $userId);
            
            $result[] = [
                'code' => $voucher->code,
                'name' => $voucher->name,
                'discount_type' => $voucher->discount_type,
                'discount_value' => (float) $voucher->discount_value,
                'max_discount_amount' => $voucher->max_discount_amount ? (float) $voucher->max_discount_amount : null,
                'min_transaction_amount' => (float) $voucher->min_transaction_amount,
                'is_applicable' => $eligibility['eligible'],
                'message' => $eligibility['eligible'] ? 'Voucher dapat digunakan' : $eligibility['message']
            ];
        }
        
        return response()->json([
            'success' => true,
            'vouchers' => $result
        ]);
    }
}