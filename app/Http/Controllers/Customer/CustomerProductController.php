<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomerProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants', 'images'])
            ->where('is_active', true)
            ->whereHas('variants', function($q) {
                $q->where('stock', '>', 0);
            });

        // ============================================
        // FILTER: PRODUK UNGGULAN
        // ============================================
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by price
        if ($request->filled('min_price')) {
            $query->whereExists(function($q) use ($request) {
                $q->selectRaw('1')
                  ->from('product_variants')
                  ->whereColumn('product_variants.product_id', 'products.id')
                  ->where('price', '>=', $request->min_price);
            });
        }
        if ($request->filled('max_price')) {
            $query->whereExists(function($q) use ($request) {
                $q->selectRaw('1')
                  ->from('product_variants')
                  ->whereColumn('product_variants.product_id', 'products.id')
                  ->where('price', '<=', $request->max_price);
            });
        }

        // Sort
        $sort = $request->sort ?? 'newest';
        switch ($sort) {
            case 'price_asc':
                $query->orderBy(
                    Product::select('price')
                        ->from('product_variants')
                        ->whereColumn('product_variants.product_id', 'products.id')
                        ->orderBy('price')
                        ->limit(1),
                    'asc'
                );
                break;
            case 'price_desc':
                $query->orderBy(
                    Product::select('price')
                        ->from('product_variants')
                        ->whereColumn('product_variants.product_id', 'products.id')
                        ->orderBy('price', 'desc')
                        ->limit(1),
                    'desc'
                );
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('customer.products.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::with([
            'category',
            'images' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'options' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'options.values' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'variants' => function ($query) {
                $query->where('is_active', true)->orderBy('price', 'asc');
            },
            'variants.variantValues',
            'variants.variantValues.optionValue',
        ])->where('slug', $slug)->firstOrFail();

        // 🔥 BUILD VARIANT DATA DENGAN GAMBAR DARI OPTION VALUE
        $variantData = $product->variants->map(function ($variant) {
            // Cari gambar dari option values (prioritas: warna)
            $variantImage = null;
            foreach ($variant->variantValues as $vv) {
                if ($vv->optionValue && $vv->optionValue->image) {
                    $variantImage = Storage::url($vv->optionValue->image);
                    break;
                }
            }

            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => (float) $variant->price,
                'discount_price' => $variant->discount_price ? (float) $variant->discount_price : null,
                'stock' => (int) $variant->stock,
                'weight' => (int) $variant->weight,
                'image' => $variantImage, // Gunakan gambar dari option value
                'values' => $variant->variantValues->pluck('product_option_value_id')->toArray(),
            ];
        });

        $relatedProducts = Product::with(['images', 'variants'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(8)
            ->get();

        return view('customer.products.show', compact('product', 'relatedProducts', 'variantData'));
    }
}