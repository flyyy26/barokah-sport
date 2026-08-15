@php
    $isEdit = $isEdit ?? false;
    $existingOptions = [];
    $existingVariants = [];

    if ($isEdit && isset($product)) {
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
            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'discount_price' => $variant->discount_price,
                'stock' => $variant->stock,
                'weight' => $variant->weight,
                'image' => $variant->image ? Storage::url($variant->image) : null,
                'option_value_ids' => $variant->variantValues->pluck('product_option_value_id')->values()->toArray(),
            ];
        })->values()->toArray();
    }
@endphp

@csrf

<div class="space-y-8">

    {{-- Informasi Produk --}}
    <div>
        <h2 class="text-lg font-semibold text-gray-900">Informasi Produk</h2>
        <p class="mt-1 text-sm text-gray-500">Masukkan informasi dasar produk.</p>
    </div>

    {{-- Nama Produk --}}
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk</label>
        <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}" required
            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Contoh: Sepatu Running">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Kategori --}}
    <div>
        <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
        <select name="category_id" id="category_id" required
            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">-- Pilih Kategori --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Deskripsi --}}
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Produk</label>
        <textarea name="description" id="description" rows="5"
            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Masukkan deskripsi produk...">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Gambar Produk --}}
    <div>
        <h2 class="text-lg font-semibold text-gray-900">Gambar Produk</h2>
        <p class="mt-1 text-sm text-gray-500">Kamu dapat mengunggah beberapa gambar sekaligus.</p>

        @if ($isEdit && isset($product) && $product->images->isNotEmpty())
            <div class="mt-5">
                <p class="mb-3 text-sm font-medium text-gray-700">Gambar Saat Ini</p>
                <div id="existing-images-container" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                    @foreach ($product->images as $image)
                        <div class="existing-image relative overflow-hidden rounded-xl border border-gray-200 bg-gray-100"
                            data-image-id="{{ $image->id }}">
                            <img src="{{ Storage::url($image->image) }}" alt="{{ $product->name }}"
                                class="aspect-square w-full object-cover">
                            <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                            <button type="button"
                                class="remove-existing-image absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-lg font-bold text-red-600 shadow transition hover:bg-red-50">
                                ×
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-5">
            <label for="images" class="block text-sm font-medium text-gray-700">
                {{ $isEdit ? 'Tambah Gambar Baru' : 'Upload Gambar' }}
            </label>
            <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/png,image/webp"
                class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
        </div>
        @error('images') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        @error('images.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Opsi Produk --}}
    <div>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Opsi Produk</h2>
                <p class="mt-1 text-sm text-gray-500">Tambahkan opsi seperti Warna atau Ukuran.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" id="add-color-option"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    🎨 + Tambah Warna
                </button>
                <button type="button" id="add-size-option"
                    class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                    📏 + Tambah Ukuran
                </button>
            </div>
        </div>

        <div id="options-container" class="mt-5 space-y-4"></div>

        <div id="options-empty" class="mt-4 rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
            Belum ada opsi produk. Tambahkan opsi Warna atau Ukuran.
        </div>
    </div>

    {{-- Varian Produk --}}
    <div>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Varian Produk</h2>
                <p class="mt-1 text-sm text-gray-500">Buat kombinasi varian berdasarkan opsi produk.</p>
            </div>
            <button type="button" id="generate-variants"
                class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                Generate Varian
            </button>
        </div>
        <div id="variants-container" class="mt-5 space-y-4"></div>
        <div id="variants-empty" class="mt-4 rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
            Belum ada varian. Tambahkan opsi lalu klik <strong>Generate Varian</strong>.
        </div>
    </div>

    {{-- Status --}}
    <div class="border-t pt-6">
        <div class="flex flex-wrap items-center gap-8">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_featured" value="1"
                    @checked(old('is_featured', $product->is_featured ?? false))
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-700">Produk Unggulan</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1"
                    @checked(old('is_active', $product->is_active ?? true))
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-700">Produk Aktif</span>
            </label>
        </div>
    </div>

    {{-- Submit --}}
    <div class="flex justify-end gap-3 border-t pt-6">
        <a href="{{ route('admin.products.index') }}"
            class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Batal
        </a>
        <button type="submit" id="submit-btn"
            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Produk' }}
        </button>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const optionsContainer = document.getElementById('options-container');
    const optionsEmpty = document.getElementById('options-empty');
    const variantsContainer = document.getElementById('variants-container');
    const variantsEmpty = document.getElementById('variants-empty');
    const addColorOptionButton = document.getElementById('add-color-option');
    const addSizeOptionButton = document.getElementById('add-size-option');
    const generateVariantsButton = document.getElementById('generate-variants');
    const existingImagesContainer = document.getElementById('existing-images-container');
    const submitBtn = document.getElementById('submit-btn');

    const existingOptions = @json($existingOptions);
    const existingVariants = @json($existingVariants);

    let optionIndex = 0;

    const DEFAULT_SIZES = ['S', 'M', 'L', 'XL', 'XXL'];
    const isEdit = @json($isEdit ?? false);
    const productId = @json($product->id ?? null);

    // ============================================
    // UTILITY FUNCTIONS
    // ============================================

    function updateOptionsEmptyState() {
        const total = optionsContainer.querySelectorAll('.option-item').length;
        optionsEmpty.classList.toggle('hidden', total > 0);
    }

    function updateVariantsEmptyState() {
        const total = variantsContainer.querySelectorAll('.variant-item').length;
        variantsEmpty.classList.toggle('hidden', total > 0);
    }

    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        const div = document.createElement('div');
        div.textContent = String(text);
        return div.innerHTML;
    }

    function isValidValue(value) {
        if (!value || value === '') return false;
        
        var isPath = value.includes('fakepath') ||
                    value.includes(':\\') ||
                    value.includes('/') ||
                    value.match(/\.(jpg|jpeg|png|webp|gif|svg)$/i);
        
        var hasSlash = value.includes('/') && !isPath;
        var isTooLong = value.length > 50;
        
        return !isPath && !hasSlash && !isTooLong;
    }

    // ============================================
    // 🔥 TAMBAH OPSI UKURAN (TANPA GAMBAR)
    // ============================================

    function addSizeOption(optionName = 'Ukuran', values = []) {
        const currentIndex = optionIndex++;
        const optionElement = document.createElement('div');
        optionElement.className = 'option-item rounded-xl border border-green-200 bg-green-50 p-5';
        optionElement.dataset.isSize = 'true';

        const sizeValues = values.length > 0 ? values : DEFAULT_SIZES;

        optionElement.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">📏</span>
                        <label class="block text-sm font-medium text-gray-700">Nama Opsi</label>
                    </div>
                    <input type="text" name="options[${currentIndex}][name]" value="${escapeHtml(optionName)}" required
                        class="option-name mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        placeholder="Contoh: Ukuran">
                    <p class="mt-1 text-xs text-green-600">✅ Opsi ukuran tidak memerlukan gambar</p>
                </div>
                <button type="button" class="remove-option mt-7 rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-100">
                    Hapus
                </button>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Nilai Opsi (Ukuran)</label>
                <div class="option-values mt-2 space-y-2">
                    ${sizeValues.map((size, index) => `
                        <div class="option-value-row flex flex-wrap items-center gap-2 mb-2">
                            <div class="flex-1 min-w-[120px]">
                                <input type="text" name="options[${currentIndex}][values][${index}]" value="${escapeHtml(size)}" required
                                    class="option-value-input block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                    placeholder="Contoh: S">
                            </div>
                            <div class="flex-1 min-w-[100px]">
                                <input type="hidden" name="options[${currentIndex}][images][${index}]" value="">
                                <span class="text-sm text-gray-400">(tanpa gambar)</span>
                            </div>
                            <button type="button" class="remove-option-value rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600 hover:bg-red-100 flex-shrink-0">
                                Hapus
                            </button>
                        </div>
                    `).join('')}
                </div>
                <button type="button" class="add-option-value mt-3 text-sm font-medium text-green-600 hover:text-green-700">
                    + Tambah Ukuran
                </button>
            </div>
        `;

        optionsContainer.appendChild(optionElement);
        updateOptionsEmptyState();
    }

    // ============================================
    // 🔥 TAMBAH OPSI WARNA (DENGAN GAMBAR)
    // ============================================

    function addColorOption(optionName = 'Warna', values = [], images = []) {
        const currentIndex = optionIndex++;
        const optionElement = document.createElement('div');
        optionElement.className = 'option-item rounded-xl border border-blue-200 bg-blue-50 p-5';
        optionElement.dataset.isColor = 'true';

        optionElement.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🎨</span>
                        <label class="block text-sm font-medium text-gray-700">Nama Opsi</label>
                    </div>
                    <input type="text" name="options[${currentIndex}][name]" value="${escapeHtml(optionName)}" required
                        class="option-name mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Contoh: Warna">
                    <p class="mt-1 text-xs text-blue-600">✅ Opsi warna dapat memiliki gambar</p>
                </div>
                <button type="button" class="remove-option mt-7 rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-100">
                    Hapus
                </button>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Nilai Opsi (Warna)</label>
                <div class="option-values mt-2 space-y-2"></div>
                <button type="button" class="add-option-value mt-3 text-sm font-medium text-blue-600 hover:text-blue-700">
                    + Tambah Warna
                </button>
            </div>
        `;

        optionsContainer.appendChild(optionElement);

        if (values.length > 0) {
            values.forEach(function(value, index) {
                var image = (images && images[index]) ? images[index] : '';
                addOptionValue(optionElement, currentIndex, value, image);
            });
        } else {
            addOptionValue(optionElement, currentIndex);
        }

        updateOptionsEmptyState();
    }

    // ============================================
    // 🔥 TAMBAH NILAI OPSI (GENERIK)
    // ============================================

    function addOptionValue(optionElement, optionIndex, value = '', image = '') {
        const valuesContainer = optionElement.querySelector('.option-values');
        const valueIndex = valuesContainer.querySelectorAll('.option-value-row').length;
        
        const valueRow = document.createElement('div');
        valueRow.className = 'option-value-row flex flex-wrap items-center gap-2 mb-2';

        // 🔥 CEK APAKAH INI OPSI UKURAN
        const isSize = optionElement.dataset.isSize === 'true';

        valueRow.innerHTML = `
            <div class="flex-1 min-w-[120px]">
                <input type="text" name="options[${optionIndex}][values][${valueIndex}]" value="${escapeHtml(value)}" required
                    class="option-value-input block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="${isSize ? 'Contoh: XL' : 'Contoh: Merah'}">
                <p class="option-value-hint mt-0.5 text-xs text-gray-400 hidden">${isSize ? 'Masukkan ukuran (contoh: S, M, L, XL)' : 'Masukkan nama warna (contoh: Merah, Biru, Hijau)'}</p>
            </div>
            <div class="flex-1 min-w-[100px]">
                ${isSize ? `
                    <input type="hidden" name="options[${optionIndex}][images][${valueIndex}]" value="">
                    <span class="text-sm text-gray-400">(tanpa gambar)</span>
                ` : `
                    <input type="file" name="options[${optionIndex}][images][${valueIndex}]" accept="image/*"
                        class="option-image-input block w-full text-sm text-gray-500 file:mr-2 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                `}
            </div>
            ${!isSize && image ? `
                <div class="flex-shrink-0">
                    <img src="${image}" alt="Preview" class="option-image-preview h-10 w-10 object-cover rounded-lg border border-gray-200">
                </div>
            ` : !isSize ? `
                <div class="flex-shrink-0 option-image-preview-container hidden">
                    <img src="" alt="Preview" class="option-image-preview h-10 w-10 object-cover rounded-lg border border-gray-200">
                </div>
            ` : ''}
            <button type="button" class="remove-option-value rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600 hover:bg-red-100 flex-shrink-0">
                Hapus
            </button>
        `;

        valuesContainer.appendChild(valueRow);

        // 🔥 PREVIEW GAMBAR (HANYA UNTUK WARNA)
        if (!isSize) {
            var fileInput = valueRow.querySelector('.option-image-input');
            var previewContainer = valueRow.querySelector('.option-image-preview-container');
            var previewImg = valueRow.querySelector('.option-image-preview');

            if (fileInput && previewImg) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            if (previewContainer) {
                                previewContainer.classList.remove('hidden');
                            }
                        };
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }
        }

        // 🔥 VALIDASI VALUE
        var valueInput = valueRow.querySelector('.option-value-input');
        var hint = valueRow.querySelector('.option-value-hint');

        if (valueInput) {
            valueInput.addEventListener('input', function() {
                var val = this.value.trim();
                
                if (val.includes('/') || val.includes('\\') || val.includes('fakepath')) {
                    this.classList.add('border-red-500');
                    this.classList.remove('border-gray-300');
                    hint.classList.remove('hidden');
                    hint.textContent = '⚠️ Hindari penggunaan "/" atau path file.';
                    hint.classList.add('text-red-500');
                } else if (val.length > 50) {
                    this.classList.add('border-red-500');
                    hint.classList.remove('hidden');
                    hint.textContent = '⚠️ Nilai terlalu panjang.';
                    hint.classList.add('text-red-500');
                } else {
                    this.classList.remove('border-red-500');
                    this.classList.add('border-gray-300');
                    hint.classList.add('hidden');
                }
            });
        }
    }

    // ============================================
    // EVENT LISTENERS
    // ============================================

    // 🔥 TOMBOL TAMBAH WARNA
    addColorOptionButton.addEventListener('click', function() {
        addColorOption();
    });

    // 🔥 TOMBOL TAMBAH UKURAN
    addSizeOptionButton.addEventListener('click', function() {
        addSizeOption();
    });

    optionsContainer.addEventListener('click', function(event) {
        const removeOptionButton = event.target.closest('.remove-option');
        if (removeOptionButton) {
            const optionElement = removeOptionButton.closest('.option-item');
            if (optionElement) {
                if (confirm('Hapus opsi ini dan semua nilainya?')) {
                    optionElement.remove();
                    updateOptionsEmptyState();
                }
            }
            return;
        }

        const addValueButton = event.target.closest('.add-option-value');
        if (addValueButton) {
            const optionElement = addValueButton.closest('.option-item');
            const optionNameInput = optionElement.querySelector('.option-name');
            const match = optionNameInput.name.match(/options\[(\d+)\]/);
            const index = match ? match[1] : 0;
            addOptionValue(optionElement, index);
            return;
        }

        const removeValueButton = event.target.closest('.remove-option-value');
        if (removeValueButton) {
            const valueRow = removeValueButton.closest('.option-value-row');
            if (valueRow) {
                const valuesContainer = valueRow.closest('.option-values');
                const totalRows = valuesContainer.querySelectorAll('.option-value-row').length;
                if (totalRows <= 1) {
                    if (!confirm('Ini adalah nilai terakhir. Hapus?')) {
                        return;
                    }
                }
                valueRow.remove();
            }
        }
    });

    // ============================================
    // GET OPTIONS
    // ============================================

    function getOptions() {
        const optionElements = optionsContainer.querySelectorAll('.option-item');
        var options = [];

        optionElements.forEach(function(optionElement, currentOptionIndex) {
            const nameInput = optionElement.querySelector('.option-name');
            const name = nameInput ? nameInput.value.trim() : '';
            
            const valueInputs = optionElement.querySelectorAll('.option-value-input');
            var values = [];
            
            valueInputs.forEach(function(input, currentValueIndex) {
                var value = input.value.trim();
                if (value !== '') {
                    values.push({
                        index: currentValueIndex,
                        value: value
                    });
                }
            });

            if (name && values.length > 0) {
                options.push({
                    index: currentOptionIndex,
                    name: name,
                    values: values
                });
            }
        });

        return options;
    }

    // ============================================
    // GENERATE COMBINATIONS
    // ============================================

    function generateCombinations(options) {
        let result = [[]];
        options.forEach(function(option) {
            const newResult = [];
            result.forEach(function(combination) {
                option.values.forEach(function(value) {
                    newResult.push([...combination, {
                        optionIndex: option.index,
                        valueIndex: value.index,
                        optionName: option.name,
                        value: value.value
                    }]);
                });
            });
            result = newResult;
        });
        return result;
    }

    // ============================================
    // FIND EXISTING VARIANT
    // ============================================

    function arraysEqual(first, second) {
        if (first.length !== second.length) return false;
        return first.every(function(value, index) {
            return Number(value) === Number(second[index]);
        });
    }

    function getExistingOptionValueId(item) {
        if (!existingOptions[item.optionIndex]) return null;
        const existingOption = existingOptions[item.optionIndex];
        const existingValue = existingOption.values[item.valueIndex];
        if (!existingValue) return null;
        return Number(existingValue.id);
    }

    function findExistingVariant(combination) {
        const selectedValueIds = combination
            .map(function(item) { return getExistingOptionValueId(item); })
            .filter(function(id) { return id !== null; })
            .sort(function(a, b) { return a - b; });

        if (selectedValueIds.length === 0) return null;

        return existingVariants.find(function(variant) {
            const variantValueIds = (variant.option_value_ids || []).map(function(id) { return Number(id); }).sort(function(a, b) { return a - b; });
            return arraysEqual(selectedValueIds, variantValueIds);
        }) || null;
    }

    // ============================================
    // VALIDASI SKU
    // ============================================

    function validateSKUs() {
        var skuInputs = document.querySelectorAll('input[name*="[sku]"]');
        var skuValues = [];
        var errors = [];

        skuInputs.forEach(function(input) {
            var sku = input.value.trim();
            if (sku === '') {
                errors.push('SKU tidak boleh kosong.');
                input.classList.add('border-red-500');
                return;
            }

            if (skuValues.includes(sku)) {
                errors.push('SKU "' + sku + '" duplikat dalam form.');
                input.classList.add('border-red-500');
            } else {
                skuValues.push(sku);
                input.classList.remove('border-red-500');
            }
        });

        return {
            valid: errors.length === 0,
            errors: errors
        };
    }

    // ============================================
    // SKU REAL-TIME VALIDATION
    // ============================================

    variantsContainer.addEventListener('input', function(e) {
        if (e.target.name && e.target.name.includes('[sku]')) {
            var input = e.target;
            var sku = input.value.trim();
            var parent = input.closest('div');
            var existingError = parent.querySelector('.sku-error');
            
            if (existingError) {
                existingError.remove();
            }

            if (sku === '') {
                input.classList.add('border-red-500');
                return;
            }

            var allSkuInputs = document.querySelectorAll('input[name*="[sku]"]');
            var duplicate = false;
            var count = 0;
            
            allSkuInputs.forEach(function(otherInput) {
                if (otherInput !== input && otherInput.value.trim() === sku) {
                    count++;
                }
            });

            if (count > 0) {
                duplicate = true;
            }

            if (duplicate) {
                input.classList.add('border-red-500');
                var error = document.createElement('p');
                error.className = 'sku-error mt-1 text-xs text-red-600';
                error.textContent = '⚠️ SKU "' + sku + '" duplikat dalam form!';
                parent.appendChild(error);
            } else {
                input.classList.remove('border-red-500');
            }
        }
    });

    // ============================================
    // GENERATE VARIAN
    // ============================================

    generateVariantsButton.addEventListener('click', function() {
        const options = getOptions();

        if (options.length === 0) {
            alert('⚠️ Tambahkan minimal satu opsi produk (Ukuran atau Warna).');
            return;
        }

        var validOptions = options.filter(function(option) {
            return option.name && option.values && option.values.length > 0;
        });

        if (validOptions.length === 0) {
            alert('⚠️ Tambahkan minimal satu opsi produk dengan nilai.');
            return;
        }

        const combinations = generateCombinations(validOptions);

        var seen = {};
        var uniqueCombinations = combinations.filter(function(combination) {
            var key = combination.map(function(item) {
                return item.optionIndex + ':' + item.valueIndex;
            }).join('|');

            if (seen[key]) {
                return false;
            }
            seen[key] = true;
            return true;
        });

        renderVariants(uniqueCombinations);
    });

    // ============================================
    // RENDER VARIANTS
    // ============================================

    function renderVariants(combinations) {
        variantsContainer.innerHTML = '';

        var validCombinations = combinations.filter(function(combination) {
            return combination.every(function(item) {
                var value = item.value;
                var isValid = value && 
                            !value.includes('/') && 
                            !value.includes('\\') && 
                            !value.includes('fakepath') &&
                            !value.match(/\.(jpg|jpeg|png|webp|gif|svg)$/i) &&
                            value.length < 50;
                return isValid;
            });
        });

        if (validCombinations.length === 0) {
            variantsContainer.innerHTML = `
                <div class="p-6 text-center text-sm text-gray-500 bg-yellow-50 rounded-lg border border-yellow-200">
                    ⚠️ Tidak ada kombinasi varian yang valid.
                    <br>Pastikan nilai opsi tidak mengandung path file atau karakter khusus.
                </div>
            `;
            updateVariantsEmptyState();
            return;
        }

        validCombinations.forEach(function(combination, index) {
            const existingVariant = findExistingVariant(combination);

            const variant = document.createElement('div');
            variant.className = 'variant-item rounded-xl border border-gray-200 bg-white p-5 shadow-sm';

            const optionValueIndexes = combination.map(function(item) {
                return `<input type="hidden" name="variants[${index}][option_value_indexes][${item.optionIndex}]" value="${item.valueIndex}">`;
            }).join('');

            const existingVariantId = existingVariant ?
                `<input type="hidden" name="variants[${index}][id]" value="${existingVariant.id}">` : '';

            var variantImage = null;
            var colorValue = null;
            combination.forEach(function(item) {
                if (item.optionName.toLowerCase() === 'warna' || item.optionName.toLowerCase() === 'color') {
                    colorValue = item.value;
                    if (existingOptions[item.optionIndex]) {
                        var optionValues = existingOptions[item.optionIndex].values;
                        if (optionValues && optionValues[item.valueIndex]) {
                            variantImage = optionValues[item.valueIndex].image || null;
                        }
                    }
                }
            });

            const combinationText = combination.map(function(item) {
                var displayValue = item.value;
                if (displayValue && (displayValue.includes('fakepath') || displayValue.includes(':\\') || displayValue.includes('/'))) {
                    var parts = displayValue.split(/[\\\/]/);
                    displayValue = parts[parts.length - 1] || 'Gambar';
                }
                return `${item.optionName}: ${displayValue}`;
            }).join(' / ');

            const sku = existingVariant?.sku ?? '';
            const price = existingVariant?.price ?? '';
            const discountPrice = existingVariant?.discount_price ?? '';
            const stock = existingVariant?.stock ?? '';
            const weight = existingVariant?.weight ?? '';

            variant.innerHTML = `
                ${optionValueIndexes}
                ${existingVariantId}

                <div class="mb-4 flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-900">${escapeHtml(combinationText)}</p>
                    ${variantImage ? `<img src="${variantImage}" alt="${colorValue}" class="h-10 w-10 object-cover rounded-lg border border-gray-200">` : ''}
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-600">SKU</label>
                        <input type="text" name="variants[${index}][sku]" value="${escapeHtml(sku)}" required
                            class="sku-input mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Masukkan SKU">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Harga</label>
                        <input type="number" name="variants[${index}][price]" value="${escapeHtml(price)}" min="0" required
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Harga Diskon</label>
                        <input type="number" name="variants[${index}][discount_price]" value="${escapeHtml(discountPrice)}" min="0"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Stok</label>
                        <input type="number" name="variants[${index}][stock]" value="${escapeHtml(stock)}" min="0" required
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Berat (gram)</label>
                        <input type="number" name="variants[${index}][weight]" value="${escapeHtml(weight)}" min="0" required
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            `;

            variantsContainer.appendChild(variant);
        });

        updateVariantsEmptyState();
    }

    // ============================================
    // HAPUS GAMBAR LAMA PRODUK
    // ============================================

    if (existingImagesContainer) {
        existingImagesContainer.addEventListener('click', function(event) {
            const button = event.target.closest('.remove-existing-image');
            if (!button) return;
            const imageElement = button.closest('.existing-image');
            if (!imageElement) return;
            if (!confirm('Hapus gambar ini dari produk?')) return;
            imageElement.remove();
        });
    }

    // ============================================
    // SUBMIT
    // ============================================

    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();

        var form = submitBtn.closest('form');
        if (!form) {
            alert('❌ Form tidak ditemukan.');
            return;
        }

        // Validasi SKU
        var result = validateSKUs();
        if (!result.valid) {
            alert('❌ ' + result.errors.join('\n'));
            return;
        }

        // Kumpulkan SKU
        var skuInputs = form.querySelectorAll('input[name*="[sku]"]');
        var skus = [];
        var hasEmptySku = false;
        
        skuInputs.forEach(function(input) {
            var sku = input.value.trim();
            if (sku === '') {
                hasEmptySku = true;
                input.classList.add('border-red-500');
            } else {
                skus.push(sku);
                input.classList.remove('border-red-500');
            }
        });

        if (hasEmptySku) {
            alert('❌ Ada SKU yang kosong. Harap isi semua SKU.');
            return;
        }

        if (skus.length === 0) {
            alert('❌ Minimal satu SKU harus diisi.');
            return;
        }

        var uniqueSkus = [...new Set(skus)];
        if (skus.length !== uniqueSkus.length) {
            alert('❌ Ada SKU duplikat dalam form. Periksa kembali!');
            return;
        }

        form.submit();
    });

    // ============================================
    // LOAD DATA EDIT
    // ============================================

    if (existingOptions.length > 0) {
        existingOptions.forEach(function(option) {
            var values = (option.values || []).map(function(item) {
                return isValidValue(item.value) ? item.value : '';
            }).filter(function(v) { return v !== ''; });
            var images = (option.values || []).map(function(item) { return item.image || ''; });
            
            if (values.length > 0) {
                // 🔥 CEK APAKAH INI UKURAN
                if (option.name.toLowerCase() === 'ukuran' || option.name.toLowerCase() === 'size') {
                    addSizeOption(option.name, values);
                } else {
                    addColorOption(option.name, values, images);
                }
            }
        });

        setTimeout(function() {
            var options = getOptions();
            if (options.length > 0) {
                var combinations = generateCombinations(options);
                renderVariants(combinations);
            }
        }, 200);
    }

    updateOptionsEmptyState();
    updateVariantsEmptyState();

});
</script>