<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WishlistController extends Controller
{
    /**
     * Get wishlist items
     */
    public function index()
    {
        $wishlist = session()->get('wishlist', []);
        
        // Get product details from wishlist
        $products = [];
        if (!empty($wishlist)) {
            $productIds = array_keys($wishlist);
            $products = Product::with(['images', 'variants'])
                ->whereIn('id', $productIds)
                ->where('is_active', true)
                ->get();
        }
        
        return view('customer.wishlist.index', compact('products', 'wishlist'));
    }

    /**
     * Get wishlist popup (AJAX)
     */
    public function popup(Request $request)
    {
        $wishlist = session()->get('wishlist', []);
        $count = count($wishlist);
        
        // Get product details
        $products = [];
        if (!empty($wishlist)) {
            $productIds = array_keys($wishlist);
            $products = Product::with(['images', 'variants'])
                ->whereIn('id', $productIds)
                ->where('is_active', true)
                ->get();
        }
        
        if ($request->ajax() || $request->wantsJson()) {
            $html = view('customer.wishlist.popup', compact('products', 'wishlist', 'count'))->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $count,
                'wishlist_ids' => array_keys($wishlist), // 🔥 Kirim daftar ID wishlist
            ]);
        }
        
        return redirect()->route('customer.wishlist.index');
    }

    /**
     * Add to wishlist
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $wishlist = session()->get('wishlist', []);
        
        $inWishlist = false;
        $message = '';

        if (!isset($wishlist[$request->product_id])) {
            $wishlist[$request->product_id] = [
                'added_at' => now(),
            ];
            session()->put('wishlist', $wishlist);
            $inWishlist = true;
            $message = 'Produk ditambahkan ke wishlist!';
        } else {
            // Jika sudah ada, hapus dari wishlist (toggle)
            unset($wishlist[$request->product_id]);
            session()->put('wishlist', $wishlist);
            $inWishlist = false;
            $message = 'Produk dihapus dari wishlist.';
        }
        
        $count = count($wishlist);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count,
                'in_wishlist' => $inWishlist,
                'product_id' => (int) $request->product_id,
            ]);
        }
        
        return back()->with('success', $message);
    }

    /**
     * Remove from wishlist
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $wishlist = session()->get('wishlist', []);
        
        if (isset($wishlist[$request->product_id])) {
            unset($wishlist[$request->product_id]);
            session()->put('wishlist', $wishlist);
            $message = 'Produk dihapus dari wishlist.';
        } else {
            $message = 'Produk tidak ditemukan di wishlist.';
        }
        
        $count = count($wishlist);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count,
                'product_id' => (int) $request->product_id,
            ]);
        }
        
        return back()->with('success', $message);
    }

    /**
     * Clear all wishlist
     */
    public function clear(Request $request)
    {
        session()->forget('wishlist');
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Wishlist berhasil dikosongkan',
                'count' => 0,
            ]);
        }
        
        return redirect()
            ->route('customer.wishlist.index')
            ->with('success', 'Wishlist berhasil dikosongkan.');
    }

    /**
     * Get wishlist status for multiple products
     */
    public function status(Request $request)
    {
        $wishlist = session()->get('wishlist', []);
        $wishlistIds = array_keys($wishlist);
        
        return response()->json([
            'success' => true,
            'wishlist_ids' => $wishlistIds,
        ]);
    }
}