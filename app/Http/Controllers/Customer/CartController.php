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
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return redirect()->route('customer.login');
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

            // 🔥 GUNAKAN FORMAT YANG KONSISTEN
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

        // 🔥 SIMPAN KE SESSION AGAR KONSISTEN
        session()->put('cart', $cart);

        return view('customer.cart.index', compact('cart', 'subtotal'));
    }

    // ============================================
    // POPUP - Tampilkan Popup Keranjang (AJAX)
    // ============================================

    public function popup(Request $request)
    {
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

            // 🔥 GUNAKAN FORMAT YANG KONSISTEN
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
    // ADD - Tambah ke Keranjang
    // ============================================

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
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

        // 🔥 UPDATE SESSION CART
        $this->syncCartSession($user->id);

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
    // UPDATE - Update Quantity
    // ============================================

    public function update(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|integer|exists:carts,id',
            'quantity' => 'required|integer|min:1',
        ]);

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

        // 🔥 UPDATE SESSION CART
        $this->syncCartSession($user->id);

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
            'key' => 'required|string',
        ]);

        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
            ], 401);
        }

        $key = $validated['key'];
        
        if (is_numeric($key)) {
            $cartItem = Cart::where('id', $key)
                ->where('user_id', $user->id)
                ->first();
        } else {
            $cartItem = null;
        }

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan.',
            ], 404);
        }

        $cartItem->delete();

        // 🔥 UPDATE SESSION CART
        $this->syncCartSession($user->id);

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
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
            ], 401);
        }

        Cart::where('user_id', $user->id)->delete();

        // 🔥 KOSONGKAN SESSION CART
        session()->forget('cart');

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
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json(['count' => 0, 'is_logged_in' => false]);
        }

        $count = Cart::where('user_id', $user->id)->count();

        return response()->json(['count' => $count, 'is_logged_in' => true]);
    }

    // ============================================
    // SYNC CART SESSION
    // ============================================

    private function syncCartSession($userId)
    {
        $cartItems = Cart::with(['product.images', 'variant'])
            ->where('user_id', $userId)
            ->get();

        $cart = [];
        foreach ($cartItems as $item) {
            $variant = $item->variant;
            $product = $item->product;

            $price = $variant ? 
                ($variant->discount_price ?? $variant->price) : 
                $product->price;

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