<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;      // ✅ Tambahkan ini
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

    public function index()
    {
        $products = Product::with([
            'category',
            'images',
            'variants',
        ])
        ->latest()
        ->paginate(10);

        return view(
            'admin.products.index',
            compact('products')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $categories = Category::where(
            'is_active',
            true
        )
        ->orderBy('name')
        ->get();

        return view(
            'admin.products.create',
            compact('categories')
        );
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
                    'is_featured' => $request->boolean('is_featured'),
                    'is_active' => $request->boolean('is_active'),
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

                // 3. CREATE OPTIONS
                $optionValueMap = $this->createProductOptions(
                    $product,
                    $validated['options'] ?? []
                );

                // 🔥 DEBUG: Log optionValueMap
                \Log::info('Option Value Map:', $optionValueMap);

                // 4. CREATE VARIANTS
                $this->createProductVariants(
                    $product,
                    $validated['variants'] ?? [],
                    $optionValueMap
                );
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
        ]);

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
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

        // 🔥 CEK DUPLIKAT DI DALAM FORM (lebih cepat)
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

        // 🔥 CEK DI DATABASE (1 query untuk semua SKU)
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
                    'is_featured' => $request->boolean('is_featured'),
                    'is_active' => $request->boolean('is_active'),
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

                // 4. DELETE OLD OPTIONS
                $product->options()->delete();

                // 5. DELETE OLD VARIANTS
                $product->variants()->delete();

                // 6. CREATE OPTIONS AGAIN
                $optionValueMap = $this->createProductOptions(
                    $product,
                    $validated['options'] ?? []
                );

                // 🔥 DEBUG: Log optionValueMap
                \Log::info('Update Option Value Map:', $optionValueMap);

                // 7. CREATE VARIANTS AGAIN
                $this->createProductVariants(
                    $product,
                    $validated['variants'] ?? [],
                    $optionValueMap
                );
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


    /*
    |--------------------------------------------------------------------------
    | DESTROY PRODUCT
    |--------------------------------------------------------------------------
    */

    public function destroy(Product $product)
    {
        /*
        |--------------------------------------------------------------------------
        | LOAD RELASI YANG DIPERLUKAN
        |--------------------------------------------------------------------------
        */
        
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

        /*
        |--------------------------------------------------------------------------
        | SIMPAN PATH GAMBAR
        |--------------------------------------------------------------------------
        */

        $imagePaths = $product
            ->images
            ->pluck('image')
            ->filter()
            ->values()
            ->toArray();

        try {
            DB::transaction(function () use ($product) {

                /*
                |--------------------------------------------------------------------------
                | 1. HAPUS VARIANT VALUES (PIVOT)
                |--------------------------------------------------------------------------
                */

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

                /*
                |--------------------------------------------------------------------------
                | 2. HAPUS VARIANTS
                |--------------------------------------------------------------------------
                */

                $product
                    ->variants()
                    ->delete();

                /*
                |--------------------------------------------------------------------------
                | 3. HAPUS OPTION VALUES
                |--------------------------------------------------------------------------
                */

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

                /*
                |--------------------------------------------------------------------------
                | 4. HAPUS OPTIONS
                |--------------------------------------------------------------------------
                */

                $product
                    ->options()
                    ->delete();

                /*
                |--------------------------------------------------------------------------
                | 5. HAPUS IMAGES (DATABASE)
                |--------------------------------------------------------------------------
                */

                $product
                    ->images()
                    ->delete();

                /*
                |--------------------------------------------------------------------------
                | 6. HAPUS PRODUCT
                |--------------------------------------------------------------------------
                */

                $product->delete();

            });

            /*
            |--------------------------------------------------------------------------
            | HAPUS FILE GAMBAR DARI STORAGE
            |--------------------------------------------------------------------------
            */

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
    | DESTROY IMAGE
    |--------------------------------------------------------------------------
    */

    public function destroyImage(
        Product $product,
        $image
    ) {

        $productImage =
            $product->images()
                ->findOrFail($image);


        $path =
            $productImage->image;


        DB::transaction(
            function () use (
                $productImage,
                $path
            ) {

                /*
                |--------------------------------------------------------------------------
                | Hapus data database
                |--------------------------------------------------------------------------
                */

                $productImage->delete();


                /*
                |--------------------------------------------------------------------------
                | Hapus file
                |--------------------------------------------------------------------------
                */

                if (
                    Storage::disk('public')
                        ->exists($path)
                ) {

                    Storage::disk('public')
                        ->delete($path);
                }
            }
        );


        return back()
            ->with(
                'success',
                'Gambar produk berhasil dihapus.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATE PRODUCT
    |--------------------------------------------------------------------------
    */

    private function validateProduct(
        Request $request,
        ?Product $product = null
    ): array {

        $rules = [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            
            // OPSI PRODUK
            'options' => ['nullable', 'array'],
            'options.*.name' => ['required', 'string', 'max:100'],
            'options.*.values' => ['required', 'array', 'min:1'],
            'options.*.values.*' => ['required', 'string', 'max:100'],
            
            // 🔥 TAMBAHKAN VALIDASI GAMBAR OPSI DI SINI:
            'options.*.images' => ['nullable', 'array'],
            'options.*.images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'variants' => ['required', 'array', 'min:1'],
            'variants.*.sku' => ['required', 'string', 'max:100'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.discount_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['required', 'integer', 'min:0'],
            'variants.*.weight' => ['required', 'integer', 'min:0'],
            'variants.*.option_value_indexes' => ['required', 'array', 'min:1'],
            'variants.*.option_value_indexes.*' => ['required', 'integer', 'min:0'],
        ];

        return $request->validate($rules);
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE PRODUCT OPTIONS
    |--------------------------------------------------------------------------
    */

    private function createProductOptions(
        Product $product,
        array $options
    ): array {

        $optionValueMap = [];

        // 🔥 PROSES SEMUA OPSI DARI FORM (TERMASUK UKURAN)
        foreach ($options as $optionIndex => $optionData) {
            $option = $product->options()->create([
                'name' => trim($optionData['name']),
                'sort_order' => $optionIndex,
            ]);

            $optionValueMap[$optionIndex] = [];

            $values = $optionData['values'] ?? [];
            $images = $optionData['images'] ?? [];

            foreach ($values as $valueIndex => $value) {
                $imagePath = null;

                // 🔥 CEK APAKAH ADA GAMBAR UNTUK VALUE INI
                if (isset($images[$valueIndex]) && 
                    $images[$valueIndex] instanceof \Illuminate\Http\UploadedFile) {
                    $imagePath = $images[$valueIndex]->store('products/option-values', 'public');
                }

                $optionValue = $option->values()->create([
                    'value' => trim($value),
                    'image' => $imagePath, // NULL jika tidak ada gambar
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

            // 🔥 BUILD OPTION VALUE IDS DENGAN MAPPING YANG BENAR
            $optionValueIds = [];

            foreach ($optionValueIndexes as $optionIndex => $valueIndex) {
                $optionValueId = null;

                // Coba cari di map
                if (isset($optionValueMap[$optionIndex][$valueIndex])) {
                    $optionValueId = $optionValueMap[$optionIndex][$valueIndex];
                } else {
                    // Coba cari di semua map berdasarkan valueIndex
                    foreach ($optionValueMap as $mapIndex => $mapValues) {
                        if (isset($mapValues[$valueIndex])) {
                            $optionValueId = $mapValues[$valueIndex];
                            break;
                        }
                    }
                }

                if ($optionValueId && !in_array($optionValueId, $optionValueIds)) {
                    $optionValueIds[] = $optionValueId;
                }
            }

            // 🔥 CREATE VARIANT
            $variant = $product->variants()->create([
                'sku' => trim($variantData['sku']),
                'price' => $variantData['price'],
                'discount_price' => $variantData['discount_price'] ?? null,
                'stock' => $variantData['stock'],
                'weight' => $variantData['weight'],
                'is_active' => true,
            ]);

            // 🔥 HUBUNGKAN DENGAN OPTION VALUES
            foreach ($optionValueIds as $optionValueId) {
                $variant->variantValues()->create([
                    'product_option_value_id' => $optionValueId,
                ]);
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE UNIQUE SLUG
    |--------------------------------------------------------------------------
    */

    private function generateUniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {

        $slug =
            Str::slug($name);


        $originalSlug =
            $slug;


        $counter = 1;


        while (
            Product::query()
                ->where(
                    'slug',
                    $slug
                )
                ->when(
                    $ignoreId,
                    function ($query)
                    use ($ignoreId) {

                        $query->where(
                            'id',
                            '!=',
                            $ignoreId
                        );
                    }
                )
                ->exists()
        ) {

            $slug =
                $originalSlug
                . '-'
                . $counter;


            $counter++;
        }


        return $slug;
    }
}