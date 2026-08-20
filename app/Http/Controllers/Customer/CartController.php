<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class CartController extends Controller
{
    // ============================================
    // INDEX - Tampilkan Keranjang
    // ============================================

    public function index()
    {
        $cart = session()->get('cart', []);
        
        // Hitung total
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        return view('customer.cart.index', compact('cart', 'subtotal'));
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

        $product = Product::with(['variants', 'images', 'options.values'])->findOrFail($validated['product_id']);
        
        $weight = 0;
        $variantName = null;
        $price = 0;
        $originalPrice = 0;
        $variantImage = null; // 🔥 Tambahkan ini

        if (!empty($validated['variant_id'])) {
            $variant = ProductVariant::with('values')->find($validated['variant_id']);
            if ($variant) {
                $weight = $variant->weight ?? 1000;
                $price = $variant->discount_price ?? $variant->price;
                $originalPrice = $variant->price;
                $variantName = $variant->option_combination ?? $variant->values->pluck('value')->implode(' / ');
                
                // 🔥 CARI GAMBAR VARIAN DARI OPTION VALUES (WARNA)
                $variantImage = $this->getVariantImage($variant, $product);
            }
        } else {
            $firstVariant = $product->variants->first();
            if ($firstVariant) {
                $weight = $firstVariant->weight ?? 1000;
                $price = $firstVariant->discount_price ?? $firstVariant->price ?? 0;
                $originalPrice = $firstVariant->price ?? 0;
                $variantImage = $this->getVariantImage($firstVariant, $product);
            }
        }

        // Jika tidak ada gambar varian, gunakan gambar produk pertama
        if (!$variantImage) {
            $variantImage = $product->images->first()?->image;
        }

        $cart = session()->get('cart', []);
        $key = $validated['product_id'] . '-' . ($validated['variant_id'] ?? '0');

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $validated['quantity'];
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'variant_id' => $validated['variant_id'] ?? null,
                'variant_name' => $variantName,
                'price' => $price,
                'original_price' => $originalPrice,
                'quantity' => $validated['quantity'],
                'image' => $variantImage, // 🔥 Gunakan gambar varian
                'slug' => $product->slug,
                'weight' => $weight,
            ];
        }

        session()->put('cart', $cart);
        session()->save();
        
        $cart = session()->get('cart', []);
        $cartCount = array_sum(array_column($cart, 'quantity'));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang.',
                'count' => $cartCount,
            ]);
        }

        return redirect()
            ->route('customer.cart.index')
            ->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * 🔥 GET VARIANT IMAGE FROM OPTION VALUES (WARNA)
     */
    private function getVariantImage($variant, $product)
    {
        // Ambil semua option value IDs dari varian
        $variantValueIds = $variant->variantValues->pluck('product_option_value_id')->toArray();
        
        // Cari opsi yang bernama "Warna" atau "Color"
        foreach ($product->options as $option) {
            if (strtolower($option->name) === 'warna' || strtolower($option->name) === 'color') {
                foreach ($option->values as $value) {
                    if (in_array($value->id, $variantValueIds) && $value->image) {
                        return $value->image;
                    }
                }
            }
        }
        
        // Jika ada gambar di variant langsung
        if ($variant->image) {
            return $variant->image;
        }
        
        return null;
    }

    // ============================================
    // UPDATE - Update Quantity
    // ============================================

    public function update(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$validated['key']])) {
            // Cek stok
            $variantId = $cart[$validated['key']]['variant_id'];
            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                if ($variant && $variant->stock < $validated['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak mencukupi.',
                    ], 400);
                }
            }

            $cart[$validated['key']]['quantity'] = $validated['quantity'];
            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil diperbarui.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item tidak ditemukan.',
        ], 404);
    }

    // ============================================
    // REMOVE - Hapus Item
    // ============================================

    public function remove(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$validated['key']])) {
            unset($cart[$validated['key']]);
            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item tidak ditemukan.',
        ], 404);
    }

    // ============================================
    // CLEAR - Kosongkan Keranjang
    // ============================================

    public function clear(Request $request)
    {
        session()->forget('cart');
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil dikosongkan',
                'count' => 0,
            ]);
        }
        
        return redirect()
            ->route('customer.cart.index')
            ->with('success', 'Keranjang berhasil dikosongkan.');
    }

    // ============================================
    // COUNT - Jumlah Item di Keranjang (untuk navbar)
    // ============================================

    public function count()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['quantity'];
        }
        return response()->json(['count' => $total]);
    }

    public function popup(Request $request)
    {
        $cart = session()->get('cart', []);
        $subtotal = 0;
        $count = 0;

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
            $count += $item['quantity'];
        }

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
}