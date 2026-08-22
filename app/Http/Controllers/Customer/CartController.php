<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CartController extends Controller
{
    // ============================================
    // INDEX - Tampilkan Keranjang
    // ============================================

    public function index()
    {
        // 🔥 PERBAIKI: Gunakan guard('customer')
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return redirect()->route('customer.login');
        }

        $cartItems = Cart::with(['product.images', 'variant'])
            ->where('user_id', $user->id)
            ->get();

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $price = $item->variant ? 
                ($item->variant->discount_price ?? $item->variant->price) : 
                $item->product->price;
            $subtotal += $price * $item->quantity;
        }

        return view('customer.cart.index', compact('cartItems', 'subtotal'));
    }

    // ============================================
    // POPUP - Tampilkan Popup Keranjang (AJAX)
    // ============================================

    public function popup(Request $request)
    {
        // 🔥 PERBAIKI: Gunakan guard('customer')
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => true,
                'html' => view('customer.cart.popup', ['cart' => []])->render(),
                'total' => 0,
                'count' => 0,
            ]);
        }

        $cartItems = Cart::with(['product.images', 'variant'])
            ->where('user_id', $user->id)
            ->get();

        $cart = [];
        $subtotal = 0;

        foreach ($cartItems as $item) {
            $variant = $item->variant;
            $product = $item->product;

            $price = $variant ? 
                ($variant->discount_price ?? $variant->price) : 
                $product->price;

            $subtotal += $price * $item->quantity;

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

        $count = $cartItems->count();

        if ($request->ajax() || $request->wantsJson()) {
            $html = view('customer.cart.popup', compact('cart', 'subtotal'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'total' => $subtotal,
                'count' => $count,
            ]);
        }

        return redirect()->route('customer.cart.index');
    }

    // ============================================
    // ADD - Tambah ke Keranjang (WAJIB LOGIN)
    // ============================================

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // 🔥 PERBAIKI: Gunakan guard('customer')
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
                'redirect' => route('customer.login')
            ], 401);
        }

        $userExists = \App\Models\User::where('id', $user->id)->exists();
        if (!$userExists) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan. Silakan login ulang.',
                'redirect' => route('customer.login')
            ], 401);
        }

        $product = Product::with(['variants', 'images', 'options.values'])->findOrFail($validated['product_id']);
        $variantId = $validated['variant_id'];
        $quantity = $validated['quantity'];

        // Cek stok
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if ($variant && $variant->stock < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Tersedia: ' . $variant->stock,
                ], 400);
            }
        } else {
            $firstVariant = $product->variants->first();
            if ($firstVariant && $firstVariant->stock < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Tersedia: ' . $firstVariant->stock,
                ], 400);
            }
        }

        // Cek apakah item sudah ada di cart
        $cartItem = Cart::where('user_id', $user->id)
            ->where('product_id', $validated['product_id'])
            ->where('variant_id', $variantId)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => $user->id,
                'product_id' => $validated['product_id'],
                'variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }

        $count = Cart::where('user_id', $user->id)->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang.',
                'count' => $count,
            ]);
        }

        return redirect()
            ->route('customer.cart.index')
            ->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    // ============================================
    // BUY NOW - Langsung ke Checkout (TIDAK PERLU LOGIN)
    // ============================================

    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::with(['images', 'variants', 'options.values'])->findOrFail($request->product_id);
        $variantId = $request->variant_id;
        $quantity = $request->quantity;

        // Cek stok
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if ($variant && $variant->stock < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Tersedia: ' . $variant->stock,
                ], 400);
            }
        } else {
            $firstVariant = $product->variants->first();
            if ($firstVariant && $firstVariant->stock < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Tersedia: ' . $firstVariant->stock,
                ], 400);
            }
        }

        // SIMPAN CART LAMA (jika ada)
        $oldCart = session()->get('cart', []);
        if (!empty($oldCart)) {
            session()->put('old_cart_backup', $oldCart);
        }

        // KOSONGKAN CART SESSION
        session()->forget('cart');

        // BUAT DATA BUY NOW
        $variant = null;
        $price = 0;
        $variantName = null;
        $weight = 1000;
        $variantImage = null;

        if ($variantId) {
            $variant = ProductVariant::with('values')->find($variantId);
            if ($variant) {
                $price = $variant->discount_price ?? $variant->price;
                $variantName = $variant->values->pluck('value')->implode(' / ');
                $weight = $variant->weight ?? 1000;
                $variantImage = $this->getVariantImage($variant, $product);
            }
        } else {
            $firstVariant = $product->variants->first();
            if ($firstVariant) {
                $price = $firstVariant->discount_price ?? $firstVariant->price;
                $weight = $firstVariant->weight ?? 1000;
                $variantImage = $this->getVariantImage($firstVariant, $product);
            }
        }

        if (!$variantImage) {
            $variantImage = $product->images->first()?->image;
        }

        $buyNowItems = [
            [
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'product_name' => $product->name,
                'variant_name' => $variantName,
                'price' => $price,
                'original_price' => $variant?->price ?? $product->price,
                'quantity' => $quantity,
                'weight' => $weight,
                'image' => $variantImage,
                'slug' => $product->slug,
            ]
        ];

        // SIMPAN BUY NOW KE SESSION
        session()->put('cart', $buyNowItems);
        session()->put('is_buy_now', true);

        return response()->json([
            'success' => true,
            'redirect' => route('customer.checkout.index'),
        ]);
    }

    // ============================================
    // UPDATE - Update Quantity
    // ============================================

    public function update(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|integer|exists:carts,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // 🔥 PERBAIKI: Gunakan guard('customer')
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
            ], 401);
        }

        $cartItem = Cart::where('id', $validated['key'])
            ->where('user_id', $user->id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan.',
            ], 404);
        }

        // Cek stok
        if ($cartItem->variant_id) {
            $variant = ProductVariant::find($cartItem->variant_id);
            if ($variant && $variant->stock < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Tersedia: ' . $variant->stock,
                ], 400);
            }
        }

        $cartItem->quantity = $validated['quantity'];
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil diperbarui.',
        ]);
    }

    // ============================================
    // REMOVE - Hapus Item
    // ============================================

    public function remove(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|integer|exists:carts,id',
        ]);

        // 🔥 PERBAIKI: Gunakan guard('customer')
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
            ], 401);
        }

        $cartItem = Cart::where('id', $validated['key'])
            ->where('user_id', $user->id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan.',
            ], 404);
        }

        $cartItem->delete();

        $count = Cart::where('user_id', $user->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus.',
            'count' => $count,
        ]);
    }

    // ============================================
    // CLEAR - Kosongkan Keranjang
    // ============================================

    public function clear(Request $request)
    {
        // 🔥 PERBAIKI: Gunakan guard('customer')
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
            ], 401);
        }

        Cart::where('user_id', $user->id)->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil dikosongkan.',
                'count' => 0,
            ]);
        }

        return redirect()
            ->route('customer.cart.index')
            ->with('success', 'Keranjang berhasil dikosongkan.');
    }

    // ============================================
    // COUNT - Jumlah Item di Keranjang
    // ============================================

    public function count()
    {
        // 🔥 PERBAIKI: Gunakan guard('customer')
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json(['count' => 0]);
        }

        $count = Cart::where('user_id', $user->id)->count();

        return response()->json(['count' => $count]);
    }

    // ============================================
    // HELPER - Get Variant Image
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
}