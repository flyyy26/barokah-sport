<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\BiteshipService;

class CheckoutController extends Controller
{
    // ============================================
    // INDEX - Tampilkan Halaman Checkout
    // ============================================

    protected $biteship;

    public function __construct(BiteshipService $biteship)
    {
        $this->biteship = $biteship;
    }


    public function index()
    {
        $cart = session()->get('cart', []);
        $isBuyNow = session()->get('is_buy_now', false);

        if (empty($cart)) {
            // 🔥 KEMBALIKAN CART LAMA JIKA ADA
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

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Cek stok
        foreach ($cart as $key => $item) {
            if ($item['variant_id']) {
                $variant = ProductVariant::find($item['variant_id']);
                if (!$variant || $variant->stock < $item['quantity']) {
                    // 🔥 KEMBALIKAN CART LAMA JIKA ADA
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

        $addresses = [];
        $customer = null;
        $defaultAddress = null;
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
            'isBuyNow'
        ));
    }

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

        // Jika waybill berupa PDF, return sebagai download
        if (isset($waybill['waybill_url'])) {
            return redirect($waybill['waybill_url']);
        }

        return response()->json($waybill);
    }

    // ============================================
    // PROCESS - Proses Checkout (Support Guest)
    // ============================================

    public function process(Request $request)
    {
        $cart = session()->get('cart', []);
        $isBuyNow = session()->get('is_buy_now', false);

        if (empty($cart)) {
            return redirect()
                ->route('customer.cart.index')
                ->with('error', 'Keranjang belanja kosong.');
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

        // ============================================
        // VALIDASI - HAPUS shipping_postal_code
        // ============================================

        $rules = [
            'shipping_name' => 'required|string|max:100',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_province' => 'required|string|max:100',
            'shipping_city' => 'required|string|max:100',
            'shipping_district' => 'required|string|max:100',
            'shipping_subdistrict' => 'required|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:10', // 🔥 BISA NULLABLE
            'courier' => 'nullable|string|max:50',
            'shipping_service' => 'nullable|string|max:50',
            'shipping_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];

        // Jika user tidak login, tambahkan validasi untuk auto register
        if (!Auth::guard('customer')->check()) {
            $rules['email'] = 'nullable|email|max:255|unique:customers,email';
            // 🔥 PERBAIKAN: Gunakan shipping_phone sebagai phone, bukan field phone terpisah
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            // ============================================
            // DAPATKAN / BUAT CUSTOMER
            // ============================================

            if (Auth::guard('customer')->check()) {
                $customer = Auth::guard('customer')->user();
            } else {
                // 🔥 PERBAIKAN: Cek apakah customer sudah ada berdasarkan phone
                $existingCustomer = Customer::where('phone', $validated['shipping_phone'])->first();
                
                if ($existingCustomer) {
                    // Jika sudah ada, login customer tersebut
                    $customer = $existingCustomer;
                    Auth::guard('customer')->login($customer);
                    $request->session()->regenerate();
                } else {
                    // Auto register guest
                    $defaultPassword = substr(preg_replace('/[^0-9]/', '', $validated['shipping_phone']), -6);

                    $customer = Customer::create([
                        'name' => $validated['shipping_name'],
                        'email' => $validated['email'] ?? null,
                        'phone' => $validated['shipping_phone'],
                        'password' => $defaultPassword,
                        'is_active' => true,
                    ]);

                    // Auto login
                    Auth::guard('customer')->login($customer);
                    $request->session()->regenerate();

                    // Simpan alamat customer
                    CustomerAddress::create([
                        'customer_id' => $customer->id,
                        'label' => 'Alamat Utama',
                        'recipient_name' => $validated['shipping_name'],
                        'recipient_phone' => $validated['shipping_phone'],
                        'address' => $validated['shipping_address'],
                        'city' => $validated['shipping_city'],
                        'province' => $validated['shipping_province'],
                        'postal_code' => $validated['shipping_postal_code'] ?? '0', // 🔥 PASTIKAN TERISI
                        'is_default' => true,
                    ]);
                }
            }

            // ============================================
            // HITUNG TOTAL
            // ============================================

            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            // Shipping cost dari form
            $shippingCost = $validated['shipping_cost'] ?? 0;
            $total = $subtotal + $shippingCost;

            // ============================================
            // BUAT ORDER
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
                'discount' => 0,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
            ]);

            // ============================================
            // ORDER ITEMS & KURANGI STOK
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

                // Kurangi stok
                if ($item['variant_id']) {
                    $variant = ProductVariant::find($item['variant_id']);
                    if ($variant) {
                        $variant->decrement('stock', $item['quantity']);
                    }
                }
            }

            DB::commit();

            if ($isBuyNow && session()->has('old_cart_backup')) {
                $oldCart = session()->get('old_cart_backup');
                session()->put('cart', $oldCart);
                session()->forget('old_cart_backup');
                session()->forget('is_buy_now');
            } else {
                // Kosongkan cart
                session()->forget('cart');
            }

            // Kosongkan cart
            session()->forget('cart');

            return redirect()
                ->route('customer.checkout.success', $order)
                ->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            // 🔥 KEMBALIKAN CART LAMA JIKA ERROR
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
        // Pastikan order milik customer yang login
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $order->load(['items.product', 'items.variant']);

        return view('customer.checkout.success', compact('order'));
    }

    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = \App\Models\Product::with(['images', 'variants'])->findOrFail($request->product_id);
        $variant = \App\Models\ProductVariant::with(['variantValues.optionValue'])->findOrFail($request->variant_id);

        // 🔥 SIMPAN CART LAMA KE SESSION SEMENTARA
        $oldCart = session()->get('cart', []);
        session()->put('old_cart_backup', $oldCart);

        // 🔥 KOSONGKAN CART
        session()->forget('cart');

        // 🔥 BUAT DATA BUY NOW
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
                'price' => $variant->discount_price ?? $variant->price,
                'quantity' => $request->quantity,
                'weight' => $variant->weight,
                'image' => $imageUrl,
                'slug' => $product->slug,
            ]
        ];

        // 🔥 SIMPAN BUY NOW KE SESSION
        session()->put('cart', $buyNowItems);
        session()->put('is_buy_now', true);

        return response()->json([
            'success' => true,
            'redirect' => route('customer.checkout.index'),
        ]);
    }
}