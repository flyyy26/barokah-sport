<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WishlistController extends Controller
{
    // ============================================
    // INDEX - Tampilkan Wishlist
    // ============================================

    public function index()
    {
        // 🔥 PERBAIKI: Gunakan guard('customer')
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return redirect()->route('customer.login');
        }

        $wishlist = Wishlist::with(['product.images', 'product.variants', 'product.category'])
            ->where('user_id', $user->id)
            ->get();

        $products = $wishlist->pluck('product');

        return view('customer.wishlist.index', compact('products'));
    }

    // ============================================
    // POPUP - Tampilkan Popup Wishlist (AJAX)
    // ============================================

    public function popup(Request $request)
    {
        // 🔥 PERBAIKI: Gunakan guard('customer')
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => true,
                'html' => view('customer.wishlist.popup', ['products' => collect()])->render(),
                'count' => 0,
                'wishlist_ids' => [],
            ]);
        }

        $wishlist = Wishlist::with(['product.images', 'product.variants', 'product.category'])
            ->where('user_id', $user->id)
            ->get();

        $products = $wishlist->pluck('product');
        $count = $products->count();
        $wishlistIds = $wishlist->pluck('product_id')->toArray();

        if ($request->ajax() || $request->wantsJson()) {
            $html = view('customer.wishlist.popup', compact('products'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $count,
                'wishlist_ids' => $wishlistIds,
            ]);
        }

        return redirect()->route('customer.wishlist.index');
    }

    // ============================================
    // ADD - Tambah ke Wishlist (WAJIB LOGIN)
    // ============================================

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
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

        $productId = $request->product_id;

        // Cek apakah sudah ada
        $exists = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            Wishlist::where('user_id', $user->id)
                ->where('product_id', $productId)
                ->delete();

            $inWishlist = false;
            $message = 'Produk dihapus dari wishlist.';
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);

            $inWishlist = true;
            $message = 'Produk ditambahkan ke wishlist!';
        }

        $count = Wishlist::where('user_id', $user->id)->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count,
                'in_wishlist' => $inWishlist,
                'product_id' => (int) $productId,
            ]);
        }

        return back()->with('success', $message);
    }

    // ============================================
    // REMOVE - Hapus dari Wishlist
    // ============================================

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // 🔥 PERBAIKI: Gunakan guard('customer')
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
            ], 401);
        }

        $deleted = Wishlist::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->delete();

        $count = Wishlist::where('user_id', $user->id)->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $deleted ? 'Produk dihapus dari wishlist.' : 'Produk tidak ditemukan.',
                'count' => $count,
                'product_id' => (int) $request->product_id,
            ]);
        }

        return back()->with('success', 'Produk dihapus dari wishlist.');
    }

    // ============================================
    // CLEAR - Kosongkan Wishlist
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

        Wishlist::where('user_id', $user->id)->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Wishlist berhasil dikosongkan.',
                'count' => 0,
            ]);
        }

        return redirect()
            ->route('customer.wishlist.index')
            ->with('success', 'Wishlist berhasil dikosongkan.');
    }

    // ============================================
    // STATUS - Cek Status Wishlist
    // ============================================

    public function status(Request $request)
    {
        // 🔥 PERBAIKI: Gunakan guard('customer')
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => true,
                'wishlist_ids' => [],
            ]);
        }

        $wishlistIds = Wishlist::where('user_id', $user->id)
            ->pluck('product_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'wishlist_ids' => $wishlistIds,
        ]);
    }
}