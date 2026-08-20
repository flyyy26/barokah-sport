<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Article;
use App\Models\Marketplace; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerHomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', true)
            ->where(function($query) {
                $query->whereNull('starts_at')
                      ->orWhere('starts_at', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>=', now());
            })
            ->orderBy('sort_order')
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('created_at', 'asc')
            ->get();

        $latestProducts = Product::with(['category', 'images', 'variants'])
            ->where('is_active', true)
            ->latest()
            ->limit(5)
            ->get();

        $featuredProducts = Product::with(['category', 'images', 'variants'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->limit(5)
            ->get();

        $bestSellers = Product::with(['category', 'images', 'variants'])
            ->where('is_active', true)
            ->where('is_best_seller', true)
            ->limit(5)
            ->get();

        // 🔥 AMBIL ARTIKEL
        $articles = Article::with('articleCategory')
            ->active()
            ->published()
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        $featuredArticle = Article::active()
            ->published()
            ->featured()
            ->latest()  // ✅ Gunakan scope latest yang sudah didefinisikan
            ->first();

        $marketplaces = Marketplace::active()->get();

        return view('customer.home', compact(
            'banners',
            'categories',
            'latestProducts',
            'featuredProducts',
            'bestSellers',
            'articles',
            'featuredArticle',
            'marketplaces'
        ));
    }
}