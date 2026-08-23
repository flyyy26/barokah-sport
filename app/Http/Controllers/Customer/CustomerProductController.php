<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductOptionValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
                    $q->where(function($sub) use ($request) {
                        $sub->where('price', '>=', (float) $request->min_price)
                            ->orWhere('discount_price', '>=', (float) $request->min_price);
                    });
                }
                if ($request->filled('max_price')) {
                    $q->where(function($sub) use ($request) {
                        $sub->where('price', '<=', (float) $request->max_price)
                            ->orWhere('discount_price', '<=', (float) $request->max_price);
                    });
                }
            });
        }

        // 🔥 SORTING
        switch ($request->sort) {
            case 'price_asc':
                $query->select('products.*')
                    ->addSelect(DB::raw('(SELECT MIN(COALESCE(discount_price, price)) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
                    ->orderBy('min_price', 'asc');
                break;
            case 'price_desc':
                $query->select('products.*')
                    ->addSelect(DB::raw('(SELECT MIN(COALESCE(discount_price, price)) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
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

        // 🔥 TAMBAHKAN DATA DISKON KE SETIAP PRODUK
        foreach ($products as $product) {
            $this->attachDiscountData($product);
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

        // 🔥 TAMBAHKAN DATA DISKON KE PRODUK
        $this->attachDiscountData($product);

        // 🔥 BUILD VARIANT DATA UNTUK JAVASCRIPT
        $variantData = $product->variants->map(function($variant) {
            return [
                'id' => $variant->id,
                'price' => (float) $variant->price,
                'discount_price' => $variant->discount_price ? (float) $variant->discount_price : null,
                'discount_percent' => $variant->discount_percent ?? 0,
                'stock' => (int) $variant->stock,
                'weight' => (int) $variant->weight,
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

        // ============================================
        // 🔥 REKOMENDASI PRODUK - DATA REAL DARI DATABASE
        // ============================================
        
        $allProducts = Product::with(['images', 'variants', 'category'])
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->get();

        // 🔥 TAMBAHKAN DATA DISKON KE REKOMENDASI
        foreach ($allProducts as $item) {
            $this->attachDiscountData($item);
        }

        if ($allProducts->isEmpty()) {
            $recommendedProducts = collect();
        } else {
            $sameCategory = $allProducts->filter(function($item) use ($product) {
                return $item->category_id == $product->category_id;
            });

            $otherCategory = $allProducts->filter(function($item) use ($product) {
                return $item->category_id != $product->category_id;
            });

            $recommendedProducts = $sameCategory->concat($otherCategory)->take(10);

            if ($recommendedProducts->isEmpty()) {
                $fallback = Product::with(['images', 'variants', 'category'])
                    ->where('is_active', true)
                    ->where('id', '!=', $product->id)
                    ->inRandomOrder()
                    ->limit(4)
                    ->get();
                
                foreach ($fallback as $item) {
                    $this->attachDiscountData($item);
                }
                
                $recommendedProducts = $fallback;
            }
        }

        return view('customer.products.show', compact(
            'product', 
            'variantData', 
            'firstVariant', 
            'recommendedProducts',
            'colors',
            'sizes'
        ));
    }

    public function latest(Request $request)
    {
        $query = Product::with(['category', 'images', 'variants'])
            ->where('is_active', true)
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

        // 🔥 SIZE FILTER
        if ($request->filled('size')) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->whereHas('values', function($qq) use ($request) {
                    $qq->where('value', $request->size);
                });
            });
        }

        // 🔥 COLOR FILTER
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
                    $q->where(function($sub) use ($request) {
                        $sub->where('price', '>=', (float) $request->min_price)
                            ->orWhere('discount_price', '>=', (float) $request->min_price);
                    });
                }
                if ($request->filled('max_price')) {
                    $q->where(function($sub) use ($request) {
                        $sub->where('price', '<=', (float) $request->max_price)
                            ->orWhere('discount_price', '<=', (float) $request->max_price);
                    });
                }
            });
        }

        // 🔥 SORTING
        switch ($request->sort) {
            case 'price_asc':
                $query->select('products.*')
                      ->addSelect(DB::raw('(SELECT MIN(COALESCE(discount_price, price)) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
                      ->orderBy('min_price', 'asc');
                break;
            case 'price_desc':
                $query->select('products.*')
                      ->addSelect(DB::raw('(SELECT MIN(COALESCE(discount_price, price)) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
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

        // 🔥 TAMBAHKAN DATA DISKON
        foreach ($products as $product) {
            $this->attachDiscountData($product);
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();

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

        // 🔥 SIZE FILTER
        if ($request->filled('size')) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->whereHas('values', function($qq) use ($request) {
                    $qq->where('value', $request->size);
                });
            });
        }

        // 🔥 COLOR FILTER
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
                    $q->where(function($sub) use ($request) {
                        $sub->where('price', '>=', (float) $request->min_price)
                            ->orWhere('discount_price', '>=', (float) $request->min_price);
                    });
                }
                if ($request->filled('max_price')) {
                    $q->where(function($sub) use ($request) {
                        $sub->where('price', '<=', (float) $request->max_price)
                            ->orWhere('discount_price', '<=', (float) $request->max_price);
                    });
                }
            });
        }

        // 🔥 SORTING
        switch ($request->sort) {
            case 'discount_desc':
                $query->select('products.*')
                      ->addSelect(DB::raw('(SELECT MAX(CASE WHEN discount_price IS NOT NULL AND discount_price < price THEN ((price - discount_price) / price * 100) ELSE 0 END) FROM product_variants WHERE product_variants.product_id = products.id) as max_discount'))
                      ->orderBy('max_discount', 'desc');
                break;
            case 'price_asc':
                $query->select('products.*')
                      ->addSelect(DB::raw('(SELECT MIN(COALESCE(discount_price, price)) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
                      ->orderBy('min_price', 'asc');
                break;
            case 'price_desc':
                $query->select('products.*')
                      ->addSelect(DB::raw('(SELECT MIN(COALESCE(discount_price, price)) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
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

        // 🔥 TAMBAHKAN DATA DISKON
        foreach ($products as $product) {
            $this->attachDiscountData($product);
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();

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

    /**
     * 🔥 ATTACH DISCOUNT DATA TO PRODUCT
     * Menambahkan data diskon ke produk
     */
    private function attachDiscountData($product)
    {
        if (!$product || !$product->relationLoaded('variants')) {
            return;
        }

        // Cari varian dengan diskon terbaik (diskon terbesar)
        $bestDiscountVariant = null;
        $maxDiscountPercent = 0;
        $hasDiscount = false;

        foreach ($product->variants as $variant) {
            if ($variant->discount_price && $variant->discount_price < $variant->price) {
                $discountPercent = round((($variant->price - $variant->discount_price) / $variant->price) * 100);
                $variant->discount_percent = $discountPercent;
                
                if ($discountPercent > $maxDiscountPercent) {
                    $maxDiscountPercent = $discountPercent;
                    $bestDiscountVariant = $variant;
                    $hasDiscount = true;
                }
            } else {
                $variant->discount_percent = 0;
            }
        }

        // Tambahkan properti ke product
        $product->has_discount = $hasDiscount;
        $product->max_discount_percent = $maxDiscountPercent;
        $product->best_discount_variant = $bestDiscountVariant;

        // Hitung harga termurah (termasuk diskon)
        $minEffectivePrice = null;
        foreach ($product->variants as $variant) {
            $effectivePrice = $variant->discount_price ?? $variant->price;
            if ($minEffectivePrice === null || $effectivePrice < $minEffectivePrice) {
                $minEffectivePrice = $effectivePrice;
            }
        }
        $product->min_effective_price = $minEffectivePrice;

        // Hitung harga tertinggi
        $maxPrice = null;
        foreach ($product->variants as $variant) {
            if ($maxPrice === null || $variant->price > $maxPrice) {
                $maxPrice = $variant->price;
            }
        }
        $product->max_price = $maxPrice;

        // Label harga dengan diskon
        if ($hasDiscount && $minEffectivePrice < $maxPrice) {
            $product->price_label = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.') . 
                                    ' - Rp ' . number_format($maxPrice, 0, ',', '.');
            $product->discount_label = 'Diskon ' . $maxDiscountPercent . '%';
        } elseif ($hasDiscount) {
            $product->price_label = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.');
            $product->discount_label = 'Diskon ' . $maxDiscountPercent . '%';
        } else {
            if ($minEffectivePrice && $maxPrice && $minEffectivePrice < $maxPrice) {
                $product->price_label = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.') . 
                                        ' - Rp ' . number_format($maxPrice, 0, ',', '.');
            } else {
                $product->price_label = 'Rp ' . number_format($minEffectivePrice ?? 0, 0, ',', '.');
            }
            $product->discount_label = '';
        }

        return $product;
    }
}