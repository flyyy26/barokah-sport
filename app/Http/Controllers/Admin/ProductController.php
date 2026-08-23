<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Feature;
use App\Models\Product;
use App\Models\StockHistory;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductVariantValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Product::with([
            'category',
            'images',
            'variants',
        ]);

        // 🔥 FILTER STOK
        if ($request->filled('stock')) {
            switch ($request->stock) {
                case 'critical':
                    $query->criticalStock();
                    break;
                case 'low':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->whereHas('variants', function($q) {
                        $q->selectRaw('SUM(stock) as total_stock')
                        ->havingRaw('SUM(stock) = 0');
                    });
                    break;
                case 'in_stock':
                    $query->inStock();
                    break;
                // 'all' atau lainnya: tanpa filter
            }
        }

        $products = $query
            ->latest()
            ->paginate(10);

        // 🔥 TAMBAHKAN TOTAL STOCK KE SETIAP PRODUK
        foreach ($products as $product) {
            $product->total_stock = $product->variants->sum('stock');
            $product->stock_status = $product->stock_status;
            $product->stock_status_label = $product->stock_status_label;
            $product->stock_status_color = $product->stock_status_color;
        }

        return view('admin.products.index', compact('products'));
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        $features = Feature::active()->orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'features'));
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        $uploadedFiles = [];

        try {
            DB::transaction(function () use (
                $request,
                $validated,
                &$uploadedFiles
            ) {
                // 1. CREATE PRODUCT
                $product = Product::create([
                    'category_id' => $validated['category_id'],
                    'name' => $validated['name'],
                    'slug' => $this->generateUniqueSlug($validated['name']),
                    'description' => $validated['description'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'material' => $validated['material'] ?? null,
                    'is_featured' => $request->boolean('is_featured'),
                    'is_best_seller' => $request->boolean('is_best_seller'),
                    'is_active' => $request->boolean('is_active'),
                    'minimum_stock' => $request->input('minimum_stock', 5),
                    'restock_threshold' => $request->input('restock_threshold', 10),
                ]);

                // 2. UPLOAD IMAGES
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $index => $image) {
                        $path = $image->store('products', 'public');
                        $uploadedFiles[] = $path;
                        $product->images()->create([
                            'image' => $path,
                            'sort_order' => $index,
                        ]);
                    }
                }

                // 🔥 3. CREATE OPTIONS (WARNA & UKURAN) - DENGAN GAMBAR
                if (!empty($validated['options'])) {
                    $optionValueMap = $this->createProductOptions(
                        $product,
                        $validated['options'],
                        $request->file('options') ?? []
                    );

                    // 4. CREATE VARIANTS
                    if (!empty($validated['variants'])) {
                        $this->createProductVariants(
                            $product,
                            $validated['variants'],
                            $optionValueMap
                        );
                    }
                }

                if (!empty($validated['features'])) {
                    $product->features()->sync($validated['features']);
                }
            });

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produk berhasil ditambahkan.');

        } catch (Throwable $e) {
            // Hapus file jika transaksi gagal
            foreach ($uploadedFiles as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            throw $e;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Product $product)
    {
        $product->load([
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
                $query->orderBy('price', 'asc');
            },
            'variants.variantValues',
            'variants.variantValues.optionValue',
            'features',
        ]);

        $existingOptions = $product->options->map(function ($option) {
            return [
                'id' => $option->id,
                'name' => $option->name,
                'values' => $option->values->map(function ($value) {
                    return [
                        'id' => $value->id,
                        'value' => $value->value,
                        'image' => $value->image ? Storage::url($value->image) : null,
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        $existingVariants = $product->variants->map(function ($variant) {
            $discountPercent = 0;
            if ($variant->discount_price && $variant->price > 0 && $variant->discount_price < $variant->price) {
                $discountPercent = round((($variant->price - $variant->discount_price) / $variant->price) * 100);
            }

            // 🔥 AMBIL VALUE NAMES DENGAN SORT
            $valueNames = $variant->variantValues
                ->map(function($vv) {
                    return $vv->optionValue->value ?? '';
                })
                ->filter()
                ->sort()
                ->values()
                ->toArray();

            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'discount_price' => $variant->discount_price,
                'discount_percent' => $discountPercent,
                'stock' => (int) $variant->stock, // ← PASTIKAN INTEGER
                'weight' => $variant->weight,
                'image' => $variant->image ? Storage::url($variant->image) : null,
                'option_value_ids' => $variant->variantValues->pluck('product_option_value_id')->values()->toArray(),
                'option_value_names' => $valueNames,
            ];
        })->values()->toArray();

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        $features = Feature::active()->orderBy('name')->get();

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'existingOptions',
            'existingVariants',
            'features'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request, $product);

        $uploadedFiles = [];

        try {
            DB::transaction(function () use (
                $request,
                $product,
                $validated,
                &$uploadedFiles
            ) {
                // 1. UPDATE PRODUCT
                $product->update([
                    'category_id' => $validated['category_id'],
                    'name' => $validated['name'],
                    'slug' => $this->generateUniqueSlug($validated['name'], $product->id),
                    'description' => $validated['description'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'material' => $validated['material'] ?? null,
                    'is_featured' => $request->boolean('is_featured'),
                    'is_best_seller' => $request->boolean('is_best_seller'),
                    'is_active' => $request->boolean('is_active'),
                    'minimum_stock' => $request->input('minimum_stock', 5),
                    'restock_threshold' => $request->input('restock_threshold', 10),
                ]);

                // 2. DELETE SELECTED OLD IMAGES
                $keepImageIds = collect($request->input('existing_images', []))
                    ->map(fn ($id) => (int) $id)
                    ->toArray();

                $oldImages = $product
                    ->images()
                    ->when(!empty($keepImageIds), function ($query) use ($keepImageIds) {
                        return $query->whereNotIn('id', $keepImageIds);
                    })
                    ->get();

                foreach ($oldImages as $oldImage) {
                    if (Storage::disk('public')->exists($oldImage->image)) {
                        Storage::disk('public')->delete($oldImage->image);
                    }
                    $oldImage->delete();
                }

                // 3. UPLOAD NEW IMAGES
                if ($request->hasFile('images')) {
                    $lastSortOrder = $product->images()->max('sort_order') ?? -1;

                    foreach ($request->file('images') as $index => $image) {
                        $path = $image->store('products', 'public');
                        $uploadedFiles[] = $path;
                        $product->images()->create([
                            'image' => $path,
                            'sort_order' => $lastSortOrder + $index + 1,
                        ]);
                    }
                }

                // ============================================
                // 🔥 UPDATE OPTIONS (WARNA & UKURAN)
                // ============================================

                $oldOptions = $product->options()->with('values')->get();
                $oldOptionValuesMap = [];

                foreach ($oldOptions as $oldOption) {
                    foreach ($oldOption->values as $oldValue) {
                        $oldOptionValuesMap[$oldOption->id][$oldValue->id] = $oldValue->image;
                    }
                }

                // Hapus old options & variants
                $product->options()->delete();
                $product->variants()->delete();

                // Create new options & variants
                if (!empty($validated['options'])) {
                    $optionValueMap = $this->createProductOptionsWithExistingImages(
                        $product,
                        $validated['options'],
                        $request->file('options') ?? [],
                        $oldOptionValuesMap
                    );

                    if (!empty($validated['variants'])) {
                        $this->createProductVariants(
                            $product,
                            $validated['variants'],
                            $optionValueMap
                        );
                    }
                }

                if (!empty($validated['features'])) {
                    $product->features()->sync($validated['features']);
                } else {
                    $product->features()->detach();
                }
            });

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produk berhasil diperbarui.');

        } catch (Throwable $e) {
            // Hapus file baru jika update gagal
            foreach ($uploadedFiles as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            throw $e;
        }
    }

    public function dashboard()
    {
        // Produk dengan stok kritis
        $criticalProducts = Product::with(['variants', 'category'])
            ->where('is_active', true)
            ->criticalStock()
            ->get()
            ->map(function($product) {
                $product->total_stock = $product->variants->sum('stock');
                return $product;
            });

        // Produk dengan stok menipis
        $lowProducts = Product::with(['variants', 'category'])
            ->where('is_active', true)
            ->lowStock()
            ->get()
            ->map(function($product) {
                $product->total_stock = $product->variants->sum('stock');
                return $product;
            });

        // Produk habis
        $outOfStockProducts = Product::with(['variants', 'category'])
            ->where('is_active', true)
            ->whereHas('variants', function($q) {
                $q->selectRaw('SUM(stock) as total_stock')
                  ->havingRaw('SUM(stock) = 0');
            })
            ->get()
            ->map(function($product) {
                $product->total_stock = $product->variants->sum('stock');
                return $product;
            });

        // Produk aman
        $inStockProducts = Product::with(['variants', 'category'])
            ->where('is_active', true)
            ->inStock()
            ->get()
            ->map(function($product) {
                $product->total_stock = $product->variants->sum('stock');
                return $product;
            });

        return view('admin.dashboard', compact(
            'criticalProducts',
            'lowProducts',
            'outOfStockProducts',
            'inStockProducts'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE PRODUCT OPTIONS WITH EXISTING IMAGES
    |--------------------------------------------------------------------------
    */

    private function createProductOptionsWithExistingImages(
        Product $product,
        array $options,
        array $optionFiles = [],
        array $oldOptionValuesMap = []
    ): array {
        $optionValueMap = [];

        foreach ($options as $optionIndex => $optionData) {
            $option = $product->options()->create([
                'name' => trim($optionData['name']),
                'sort_order' => $optionIndex,
            ]);

            $optionValueMap[$optionIndex] = [];

            $values = $optionData['values'] ?? [];
            $newImages = $optionFiles[$optionIndex]['images'] ?? [];
            $oldOptionId = $optionData['old_id'] ?? null;
            $oldValueIds = $optionData['old_value_ids'] ?? [];
            $existingImages = $optionData['existing_images'] ?? [];

            foreach ($values as $valueIndex => $value) {
                $imagePath = null;
                $newImage = $newImages[$valueIndex] ?? null;
                $selectedExistingImage = $existingImages[$valueIndex] ?? null;
                $oldValueId = $oldValueIds[$valueIndex] ?? null;

                // 1. Prioritas: upload baru dari input file
                if ($newImage instanceof \Illuminate\Http\UploadedFile) {
                    if ($oldOptionId && $oldValueId && isset($oldOptionValuesMap[$oldOptionId][$oldValueId])) {
                        $oldImagePath = $oldOptionValuesMap[$oldOptionId][$oldValueId];
                        if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                            Storage::disk('public')->delete($oldImagePath);
                        }
                    }

                    $imagePath = $newImage->store('products/option-values', 'public');
                }
                // 2. Jika tidak ada upload baru, pertahankan gambar lama yang sudah dikirim dari form
                elseif (!empty($selectedExistingImage)) {
                    $imagePath = $this->normalizeStoredImagePath($selectedExistingImage);
                }
                // 3. Jika masih tidak ada, cek mapping value lama berdasarkan id lama / posisi index
                elseif ($oldOptionId && isset($oldOptionValuesMap[$oldOptionId])) {
                    if ($oldValueId && isset($oldOptionValuesMap[$oldOptionId][$oldValueId])) {
                        $imagePath = $oldOptionValuesMap[$oldOptionId][$oldValueId];
                    } else {
                        $oldValueIdList = array_keys($oldOptionValuesMap[$oldOptionId]);
                        if (isset($oldValueIdList[$valueIndex])) {
                            $mappedOldValueId = $oldValueIdList[$valueIndex];
                            $imagePath = $oldOptionValuesMap[$oldOptionId][$mappedOldValueId] ?? null;
                        }
                    }
                }

                $optionValue = $option->values()->create([
                    'value' => trim($value),
                    'image' => $imagePath,
                    'sort_order' => $valueIndex,
                ]);

                $optionValueMap[$optionIndex][$valueIndex] = $optionValue->id;
            }
        }

        return $optionValueMap;
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZE STORED IMAGE PATH
    |--------------------------------------------------------------------------
    */

    private function normalizeStoredImagePath(?string $imagePath): ?string
    {
        if (empty($imagePath)) {
            return null;
        }

        if (preg_match('#^https?://#i', $imagePath)) {
            $parsed = parse_url($imagePath, PHP_URL_PATH);
            $imagePath = $parsed ?: $imagePath;
        }

        $storagePrefix = '/storage/';
        if (str_starts_with($imagePath, $storagePrefix)) {
            $imagePath = substr($imagePath, strlen($storagePrefix));
        }

        return ltrim($imagePath, '/');
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY PRODUCT
    |--------------------------------------------------------------------------
    */

    public function destroy(Product $product)
    {
        $product->load([
            'images',
            'options',
            'variants',
            'orderItems',
        ]);

        if ($product->orderItems()->exists()) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Produk tidak dapat dihapus karena sudah digunakan pada pesanan.');
        }

        $imagePaths = $product
            ->images
            ->pluck('image')
            ->filter()
            ->values()
            ->toArray();

        try {
            DB::transaction(function () use ($product) {
                // 1. HAPUS VARIANT VALUES (PIVOT)
                $variantIds = $product
                    ->variants
                    ->pluck('id')
                    ->toArray();

                if (!empty($variantIds)) {
                    ProductVariantValue::whereIn(
                        'product_variant_id',
                        $variantIds
                    )->delete();
                }

                // 2. HAPUS VARIANTS
                $product->variants()->delete();

                // 3. HAPUS OPTION VALUES
                $optionIds = $product
                    ->options
                    ->pluck('id')
                    ->toArray();

                if (!empty($optionIds)) {
                    ProductOptionValue::whereIn(
                        'product_option_id',
                        $optionIds
                    )->delete();
                }

                // 4. HAPUS OPTIONS
                $product->options()->delete();

                // 5. HAPUS IMAGES (DATABASE)
                $product->images()->delete();

                // 6. HAPUS PRODUCT
                $product->delete();
            });

            // HAPUS FILE GAMBAR DARI STORAGE
            foreach ($imagePaths as $imagePath) {
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produk berhasil dihapus.');

        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Produk gagal dihapus: ' . $e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | BULK DESTROY
    |--------------------------------------------------------------------------
    */

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'required|integer|exists:products,id',
        ]);

        $productIds = $request->product_ids;
        $deletedCount = 0;
        $failedIds = [];
        $failedNames = [];

        try {
            DB::transaction(function () use ($productIds, &$deletedCount, &$failedIds, &$failedNames) {
                
                $products = Product::with(['images', 'orderItems'])
                    ->whereIn('id', $productIds)
                    ->get();

                foreach ($products as $product) {
                    if ($product->orderItems()->exists()) {
                        $failedIds[] = $product->id;
                        $failedNames[] = $product->name;
                        continue;
                    }

                    $imagePaths = $product->images->pluck('image')->filter()->toArray();

                    $variantIds = $product->variants->pluck('id')->toArray();
                    if (!empty($variantIds)) {
                        ProductVariantValue::whereIn('product_variant_id', $variantIds)->delete();
                    }

                    $product->variants()->delete();

                    $optionIds = $product->options->pluck('id')->toArray();
                    if (!empty($optionIds)) {
                        ProductOptionValue::whereIn('product_option_id', $optionIds)->delete();
                    }

                    $product->options()->delete();
                    $product->images()->delete();
                    $product->delete();

                    foreach ($imagePaths as $imagePath) {
                        if (Storage::disk('public')->exists($imagePath)) {
                            Storage::disk('public')->delete($imagePath);
                        }
                    }

                    $deletedCount++;
                }
            });

            $message = "Berhasil menghapus {$deletedCount} produk.";

            if (!empty($failedIds)) {
                $message .= " Gagal menghapus " . count($failedIds) . " produk: " . implode(', ', $failedNames) . " (sudah digunakan di pesanan).";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted' => $deletedCount,
                'failed' => $failedIds,
                'failed_names' => $failedNames,
            ]);

        } catch (Throwable $e) {
            \Log::error('Bulk delete error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk: ' . $e->getMessage(),
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY IMAGE
    |--------------------------------------------------------------------------
    */

    public function destroyImage(Product $product, $image)
    {
        $productImage = $product->images()->findOrFail($image);
        $path = $productImage->image;

        DB::transaction(function () use ($productImage, $path) {
            $productImage->delete();

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        });

        return back()
            ->with('success', 'Gambar produk berhasil dihapus.');
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK SKU
    |--------------------------------------------------------------------------
    */

    public function checkSku(Request $request)
    {
        $request->validate([
            'skus' => 'required|array',
            'skus.*' => 'required|string',
            'product_id' => 'nullable|exists:products,id',
            'is_edit' => 'nullable|boolean',
        ]);

        $skus = array_map('trim', $request->skus);
        $skus = array_filter($skus, function($sku) {
            return !empty($sku);
        });

        if (empty($skus)) {
            return response()->json(['success' => true]);
        }

        $counts = array_count_values($skus);
        $duplicates = [];
        foreach ($counts as $sku => $count) {
            if ($count > 1 && !empty($sku)) {
                $duplicates[] = $sku;
            }
        }

        if (!empty($duplicates)) {
            return response()->json([
                'success' => false,
                'message' => 'SKU ' . implode(', ', $duplicates) . ' duplikat dalam form.',
            ], 400);
        }

        $query = \App\Models\ProductVariant::whereIn('sku', $skus);
        
        if ($request->is_edit && $request->product_id) {
            $query->where('product_id', '!=', $request->product_id);
        }
        
        $existingSkus = $query->pluck('sku')->toArray();

        if (!empty($existingSkus)) {
            return response()->json([
                'success' => false,
                'message' => 'SKU ' . implode(', ', $existingSkus) . ' sudah digunakan di produk lain.',
            ], 400);
        }

        return response()->json(['success' => true]);
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATE PRODUCT
    |--------------------------------------------------------------------------
    */

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $rules = [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gender' => ['nullable', 'string', 'in:pria,wanita,unisex'],
            'material' => ['nullable', 'string', 'max:255'],
            'features' => ['nullable', 'array'],
            'features.*' => ['exists:features,id'],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
            'restock_threshold' => ['nullable', 'integer', 'min:0'],
            'options' => ['nullable', 'array'],
            'options.*.name' => ['required_with:options', 'string', 'max:100'],
            'options.*.values' => ['required_with:options', 'array', 'min:1'],
            'options.*.values.*' => ['required_with:options', 'string', 'max:100'],
            'options.*.old_id' => ['nullable', 'integer'],
            'options.*.old_value_ids' => ['nullable', 'array'],
            'options.*.old_value_ids.*' => ['nullable', 'integer'],
            'options.*.existing_images' => ['nullable', 'array'],
            'options.*.existing_images.*' => ['nullable', 'string'],
            'options.*.images' => ['nullable', 'array'],
            'options.*.images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer'],
            'variants.*.sku' => ['required_with:variants', 'string', 'max:100'],
            'variants.*.price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'variants.*.discount_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'], // 🔥 PERBAIKI: nullable
            'variants.*.weight' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.option_value_indexes' => ['required_with:variants', 'array', 'min:1'],
            'variants.*.option_value_indexes.*' => ['required_with:variants', 'integer', 'min:0'],
        ];

        return $request->validate($rules);
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE PRODUCT OPTIONS
    |--------------------------------------------------------------------------
    */

    private function createProductOptions(Product $product, array $options, array $optionFiles = []): array
    {
        $optionValueMap = [];

        foreach ($options as $optionIndex => $optionData) {
            $option = $product->options()->create([
                'name' => trim($optionData['name']),
                'sort_order' => $optionIndex,
            ]);

            $optionValueMap[$optionIndex] = [];

            $values = $optionData['values'] ?? [];
            
            $images = [];
            if (isset($optionFiles[$optionIndex]['images'])) {
                $images = $optionFiles[$optionIndex]['images'];
            }

            foreach ($values as $valueIndex => $value) {
                $imagePath = null;

                if (isset($images[$valueIndex]) && 
                    $images[$valueIndex] instanceof \Illuminate\Http\UploadedFile) {
                    $imagePath = $images[$valueIndex]->store('products/option-values', 'public');
                }

                $optionValue = $option->values()->create([
                    'value' => trim($value),
                    'image' => $imagePath,
                    'sort_order' => $valueIndex,
                ]);

                $optionValueMap[$optionIndex][$valueIndex] = $optionValue->id;
            }
        }

        return $optionValueMap;
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE PRODUCT VARIANTS
    |--------------------------------------------------------------------------
    */

    private function createProductVariants(
        Product $product,
        array $variants,
        array $optionValueMap
    ): void {
        foreach ($variants as $variantData) {
            $optionValueIndexes = $variantData['option_value_indexes'] ?? [];
            $optionValueIds = [];

            // 🔥 PERBAIKI: Mapping yang lebih robust
            foreach ($optionValueIndexes as $optionIndex => $valueIndex) {
                $optionIndex = (int) $optionIndex;
                $valueIndex = (int) $valueIndex;
                
                // Coba cari di map berdasarkan optionIndex dan valueIndex
                if (isset($optionValueMap[$optionIndex][$valueIndex])) {
                    $optionValueIds[] = $optionValueMap[$optionIndex][$valueIndex];
                } else {
                    // Jika tidak ditemukan, coba cari berdasarkan valueIndex saja
                    foreach ($optionValueMap as $mapOptionIndex => $mapValues) {
                        if (isset($mapValues[$valueIndex])) {
                            $optionValueIds[] = $mapValues[$valueIndex];
                            break;
                        }
                    }
                }
            }

            // Jika masih kosong, coba ambil option value pertama yang tersedia
            if (empty($optionValueIds) && !empty($optionValueMap)) {
                $firstOptionIndex = array_key_first($optionValueMap);
                if ($firstOptionIndex !== null && !empty($optionValueMap[$firstOptionIndex])) {
                    $firstValueIndex = array_key_first($optionValueMap[$firstOptionIndex]);
                    if ($firstValueIndex !== null) {
                        $optionValueIds[] = $optionValueMap[$firstOptionIndex][$firstValueIndex];
                    }
                }
            }

            // 🔥 HITUNG HARGA DAN DISKON
            $price = (float) ($variantData['price'] ?? 0);
            $discountPercent = (float) ($variantData['discount_percent'] ?? 0);
            
            $discountPrice = null;
            if ($discountPercent > 0 && $price > 0) {
                $discountPrice = round($price - ($price * ($discountPercent / 100)), 2);
            }
            
            // Jika ada discount_price langsung dari input (untuk edit)
            if (isset($variantData['discount_price']) && $variantData['discount_price'] !== '' && $variantData['discount_price'] !== null) {
                $discountPrice = (float) $variantData['discount_price'];
                // Recalculate discount percent
                if ($price > 0 && $discountPrice > 0 && $discountPrice < $price) {
                    $discountPercent = round((($price - $discountPrice) / $price) * 100);
                }
            }

            $stock = 0;
            if (isset($variantData['stock']) && $variantData['stock'] !== '' && $variantData['stock'] !== null) {
                $stock = (int) $variantData['stock'];
            }

            // 🔥 BUAT VARIAN
            $variant = $product->variants()->create([
                'sku' => trim($variantData['sku'] ?? ''),
                'price' => $price,
                'discount_price' => $discountPrice,
                'stock' => $stock,
                'weight' => (int) ($variantData['weight'] ?? 1000),
                'is_active' => true,
            ]);

            // 🔥 SIMPAN OPTION VALUES
            foreach ($optionValueIds as $optionValueId) {
                $variant->variantValues()->create([
                    'product_option_value_id' => $optionValueId,
                ]);
            }
        }
    }

    public function stockHistory(Product $product)
    {
        $histories = StockHistory::with(['variant', 'user'])
            ->where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.products.stock-history', compact('product', 'histories'));
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE UNIQUE SLUG
    |--------------------------------------------------------------------------
    */

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (
            Product::query()
                ->where('slug', $slug)
                ->when($ignoreId, function ($query) use ($ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                })
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}