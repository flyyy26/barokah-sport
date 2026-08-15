<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CustomerHomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->limit(6)
            ->get();

        $latestProducts = Product::with(['category', 'images', 'variants'])
            ->where('is_active', true)
            ->latest()
            ->limit(8)
            ->get();

        $bestSellers = Product::with(['category', 'images', 'variants'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->limit(8)
            ->get();

        return view('customer.home', compact(
            'banners',
            'categories',
            'latestProducts',
            'bestSellers'
        ));
    }
}