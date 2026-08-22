<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductOptionValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images', 'variants'])
            ->where('is_active', true);

        // 🔥 SEARCH - PERBAIKI UNTUK MENCARI PRODUK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhereHas('category', function($cat) use ($search) {
                    $cat->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        // 🔥 CATEGORY FILTER
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 🔥 GENDER FILTER
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // 🔥 SIZE FILTER (melalui variants)
        if ($request->filled('size')) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->whereHas('values', function($qq) use ($request) {
                    $qq->where('value', $request->size);
                });
            });
        }

        // 🔥 COLOR FILTER (melalui variants)
        if ($request->filled('color')) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->whereHas('values', function($qq) use ($request) {
                    $qq->where('value', $request->color);
                });
            });
        }

        // 🔥 PRICE RANGE FILTER
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('variants', function($q) use ($request) {
                if ($request->filled('min_price')) {
                    $q->where('price', '>=', (float) $request->min_price);
                }
                if ($request->filled('max_price')) {
                    $q->where('price', '<=', (float) $request->max_price);
                }
            });
        }

        // 🔥 SORTING
        switch ($request->sort) {
            case 'price_asc':
                $query->select('products.*')
                    ->addSelect(\DB::raw('(SELECT MIN(price) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
                    ->orderBy('min_price', 'asc');
                break;
            case 'price_desc':
                $query->select('products.*')
                    ->addSelect(\DB::raw('(SELECT MIN(price) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
                    ->orderBy('min_price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->latest('created_at');
                break;
        }

        $products = $query->paginate(12);
        
        // 🔥 TAMBAHKAN SEARCH KEYWORD KE PAGINATION
        if ($request->filled('search')) {
            $products->appends(['search' => $request->search]);
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        // 🔥 AMBIL DATA UNTUK FILTER
        $genders = ['pria', 'wanita', 'unisex'];
        
        $sizes = ProductOptionValue::whereHas('option', function($q) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%ukuran%'])
            ->orWhereRaw('LOWER(name) LIKE ?', ['%size%']);
        })->distinct()->pluck('value')->toArray();
        sort($sizes);

        $colors = ProductOptionValue::whereHas('option', function($q) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%warna%'])
            ->orWhereRaw('LOWER(name) LIKE ?', ['%color%']);
        })->distinct()->pluck('value')->toArray();
        sort($colors);

        return view('customer.products.index', compact('products', 'categories', 'genders', 'sizes', 'colors'));
    }

    public function show($slug)
    {
        $product = Product::with([
            'category',
            'images',
            'options' => function($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'options.values' => function($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'variants' => function($query) {
                $query->orderBy('price', 'asc');
            },
            'variants.variantValues',
            'variants.variantValues.optionValue',
        ])->where('slug', $slug)->firstOrFail();

        // 🔥 BUILD VARIANT DATA UNTUK JAVASCRIPT
        $variantData = $product->variants->map(function($variant) {
            return [
                'id' => $variant->id,
                'price' => $variant->price,
                'discount_price' => $variant->discount_price,
                'stock' => $variant->stock,
                'weight' => $variant->weight,
                'image' => $variant->image ? Storage::url($variant->image) : null,
                'values' => $variant->variantValues->pluck('product_option_value_id')->map(function($id) {
                    return (int) $id;
                })->toArray(),
            ];
        })->toArray();

        $firstVariant = $product->variants->first();

        // 🔥 AMBIL DATA WARNA DAN UKURAN DARI OPSI PRODUK
        $colors = [];
        $sizes = [];
        
        foreach ($product->options as $option) {
            if (strtolower($option->name) === 'warna' || strtolower($option->name) === 'color') {
                $colors = $option->values->pluck('value')->toArray();
            }
            if (strtolower($option->name) === 'ukuran' || strtolower($option->name) === 'size') {
                $sizes = $option->values->pluck('value')->toArray();
            }
        }

        // RELATED PRODUCTS
        $relatedProducts = Product::with(['images', 'variants'])
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->when($product->category_id, function($query) use ($product) {
                return $query->where('category_id', $product->category_id);
            })
            ->limit(5)
            ->get();

        return view('customer.products.show', compact(
            'product', 
            'variantData', 
            'firstVariant', 
            'relatedProducts',
            'colors',
            'sizes'
        ));
    }

    public function latest(Request $request)
    {
        $query = Product::with(['category', 'images', 'variants'])
            ->where('is_active', true)
            // 🔥 FILTER PRODUK 1 BULAN TERAKHIR
            ->where('created_at', '>=', now()->subMonth());

        // 🔥 SEARCH
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 🔥 CATEGORY FILTER
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 🔥 GENDER FILTER
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // 🔥 SIZE FILTER (melalui variants)
        if ($request->filled('size')) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->whereHas('values', function($qq) use ($request) {
                    $qq->where('value', $request->size);
                });
            });
        }

        // 🔥 COLOR FILTER (melalui variants)
        if ($request->filled('color')) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->whereHas('values', function($qq) use ($request) {
                    $qq->where('value', $request->color);
                });
            });
        }

        // 🔥 PRICE RANGE FILTER
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('variants', function($q) use ($request) {
                if ($request->filled('min_price')) {
                    $q->where('price', '>=', (float) $request->min_price);
                }
                if ($request->filled('max_price')) {
                    $q->where('price', '<=', (float) $request->max_price);
                }
            });
        }

        // 🔥 SORTING
        switch ($request->sort) {
            case 'price_asc':
                $query->select('products.*')
                      ->addSelect(DB::raw('(SELECT MIN(price) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
                      ->orderBy('min_price', 'asc');
                break;
            case 'price_desc':
                $query->select('products.*')
                      ->addSelect(DB::raw('(SELECT MIN(price) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
                      ->orderBy('min_price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->latest('created_at');
                break;
        }

        $products = $query->paginate(12);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        // 🔥 AMBIL DATA UNTUK FILTER
        $genders = ['pria', 'wanita', 'unisex'];
        
        $sizes = ProductOptionValue::whereHas('option', function($q) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%ukuran%'])
              ->orWhereRaw('LOWER(name) LIKE ?', ['%size%']);
        })->distinct()->pluck('value')->toArray();
        sort($sizes);

        $colors = ProductOptionValue::whereHas('option', function($q) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%warna%'])
              ->orWhereRaw('LOWER(name) LIKE ?', ['%color%']);
        })->distinct()->pluck('value')->toArray();
        sort($colors);

        return view('customer.products.latest', compact('products', 'categories', 'genders', 'sizes', 'colors'));
    }

    public function promo(Request $request)
    {
        $query = Product::with(['category', 'images', 'variants'])
            ->where('is_active', true)
            // 🔥 FILTER PRODUK YANG MEMILIKI DISKON
            ->whereHas('variants', function($q) {
                $q->whereNotNull('discount_price')
                  ->whereColumn('discount_price', '<', 'price');
            });

        // 🔥 SEARCH
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 🔥 CATEGORY FILTER
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 🔥 GENDER FILTER
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // 🔥 SIZE FILTER (melalui variants)
        if ($request->filled('size')) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->whereHas('values', function($qq) use ($request) {
                    $qq->where('value', $request->size);
                });
            });
        }

        // 🔥 COLOR FILTER (melalui variants)
        if ($request->filled('color')) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->whereHas('values', function($qq) use ($request) {
                    $qq->where('value', $request->color);
                });
            });
        }

        // 🔥 PRICE RANGE FILTER
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('variants', function($q) use ($request) {
                if ($request->filled('min_price')) {
                    $q->where('price', '>=', (float) $request->min_price);
                }
                if ($request->filled('max_price')) {
                    $q->where('price', '<=', (float) $request->max_price);
                }
            });
        }

        // 🔥 SORTING
        switch ($request->sort) {
            case 'discount_desc':
                // Urutkan berdasarkan diskon terbesar
                $query->select('products.*')
                      ->addSelect(DB::raw('(SELECT MAX((price - discount_price) / price * 100) FROM product_variants WHERE product_variants.product_id = products.id AND discount_price IS NOT NULL) as max_discount'))
                      ->orderBy('max_discount', 'desc');
                break;
            case 'price_asc':
                $query->select('products.*')
                      ->addSelect(DB::raw('(SELECT MIN(price) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
                      ->orderBy('min_price', 'asc');
                break;
            case 'price_desc':
                $query->select('products.*')
                      ->addSelect(DB::raw('(SELECT MIN(price) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
                      ->orderBy('min_price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->latest('created_at');
                break;
        }

        $products = $query->paginate(12);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        // 🔥 AMBIL DATA UNTUK FILTER
        $genders = ['pria', 'wanita', 'unisex'];
        
        $sizes = ProductOptionValue::whereHas('option', function($q) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%ukuran%'])
              ->orWhereRaw('LOWER(name) LIKE ?', ['%size%']);
        })->distinct()->pluck('value')->toArray();
        sort($sizes);

        $colors = ProductOptionValue::whereHas('option', function($q) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%warna%'])
              ->orWhereRaw('LOWER(name) LIKE ?', ['%color%']);
        })->distinct()->pluck('value')->toArray();
        sort($colors);

        return view('customer.products.promo', compact('products', 'categories', 'genders', 'sizes', 'colors'));
    }
}