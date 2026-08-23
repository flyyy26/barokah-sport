@php
    $isEdit = $isEdit ?? false;
    
    // 🔥 PASTIKAN EXISTING OPTIONS DAN VARIANTS TERSEDIA
    $existingOptions = $existingOptions ?? [];
    $existingVariants = $existingVariants ?? [];
@endphp

<style>
    /* resources/css/admin.css */

    /* Deskripsi editor container */
    .description-editor {
        min-height: 300px;
    }

    /* Toast notification untuk editor */
    .tox-tinymce {
        border-radius: 0.5rem !important;
        border-color: #d1d5db !important;
    }

    .tox-tinymce:focus-within {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }

    /* Dark mode support */
    .dark .tox-tinymce {
        background: #1f2937 !important;
    }

    .dark .tox-toolbar__group {
        background: #1f2937 !important;
    }

    .dark .tox-menubar {
        background: #1f2937 !important;
    }

    .feature-item {
        transition: all 0.2s ease;
    }

    .feature-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>

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
            placeholder="Contoh: Jaket Sport">
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
            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 description-editor"
            placeholder="Masukkan deskripsi produk...">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- GENDER --}}
    <div>
        <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
        <select name="gender" id="gender"
            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">-- Pilih Jenis Kelamin --</option>
            <option value="pria" {{ old('gender', $product->gender ?? '') == 'pria' ? 'selected' : '' }}>Pria</option>
            <option value="wanita" {{ old('gender', $product->gender ?? '') == 'wanita' ? 'selected' : '' }}>Wanita</option>
            <option value="unisex" {{ old('gender', $product->gender ?? '') == 'unisex' ? 'selected' : '' }}>Unisex</option>
        </select>
        @error('gender') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- BAHAN --}}
    <div>
        <label for="material" class="block text-sm font-medium text-gray-700">Bahan</label>
        <input type="text" name="material" id="material" 
            value="{{ old('material', $product->material ?? '') }}"
            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Contoh: Poliester, Diadora, Katun">
        @error('material') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        {{-- Stok Minimum (Critical) --}}
        <div>
            <label for="minimum_stock" class="block text-sm font-medium text-gray-700">
                Stok Minimum (Kritis)
            </label>
            <div class="mt-2 flex items-center gap-2">
                <input type="number" 
                    name="minimum_stock" 
                    id="minimum_stock"
                    value="{{ old('minimum_stock', $product->minimum_stock ?? 5) }}"
                    min="0"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                <span class="text-xs text-gray-500 whitespace-nowrap">unit</span>
            </div>
            <p class="mt-1 text-xs text-red-500">
                🚨 Jika stok mencapai angka ini, produk akan diberi label <strong>"Kritis"</strong>
            </p>
        </div>

        {{-- Restock Threshold (Menipis) --}}
        <div>
            <label for="restock_threshold" class="block text-sm font-medium text-gray-700">
                Ambang Restock (Menipis)
            </label>
            <div class="mt-2 flex items-center gap-2">
                <input type="number" 
                    name="restock_threshold" 
                    id="restock_threshold"
                    value="{{ old('restock_threshold', $product->restock_threshold ?? 10) }}"
                    min="0"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <span class="text-xs text-gray-500 whitespace-nowrap">unit</span>
            </div>
            <p class="mt-1 text-xs text-yellow-500">
                ⚠️ Jika stok di bawah angka ini (tapi di atas minimum), produk diberi label <strong>"Menipis"</strong>
            </p>
        </div>
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

    {{-- ============================================ --}}
    {{-- 🔥 OPSI PRODUK (WARNA & UKURAN) --}}
    {{-- ============================================ --}}

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

    {{-- ============================================ --}}
    {{-- 🔥 VARIAN PRODUK (KOMBINASI WARNA + UKURAN) --}}
    {{-- ============================================ --}}

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

    {{-- ============================================ --}}
    {{-- 🔥 FITUR PRODUK - INLINE LIST --}}
    {{-- ============================================ --}}

    <div id="features-container">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Fitur Produk</h2>
                <p class="mt-1 text-sm text-gray-500">Pilih atau tambahkan fitur yang tersedia untuk produk ini.</p>
            </div>
        </div>

        {{-- Form Tambah Fitur Inline --}}
        <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label for="new-feature-name" class="block text-sm font-medium text-gray-700">Nama Fitur Baru</label>
                    <input type="text" id="new-feature-name" 
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Contoh: Anti Air, Ringan, Berkualitas">
                </div>
                <button type="button" id="add-feature-btn"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 whitespace-nowrap">
                    + Tambah Fitur
                </button>
            </div>
            <div id="feature-feedback" class="mt-2 text-sm hidden"></div>
        </div>

        {{-- Daftar Fitur yang Tersedia --}}
        <div class="mt-4">
            <p class="text-sm font-medium text-gray-700 mb-3">Fitur Tersedia:</p>
            <div id="features-list" class="flex flex-wrap gap-2">
                @php
                    $selectedFeatures = $isEdit && isset($product) ? $product->features->pluck('id')->toArray() : [];
                @endphp
                @foreach ($features as $feature)
                    <label class="feature-item flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 hover:bg-gray-50 cursor-pointer transition shadow-sm">
                        <input type="checkbox" 
                            name="features[]" 
                            value="{{ $feature->id }}"
                            {{ in_array($feature->id, old('features', $selectedFeatures)) ? 'checked' : '' }}
                            class="feature-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">{{ $feature->name }}</span>
                    </label>
                @endforeach
            </div>
            @if($features->isEmpty())
                <p id="no-features-message" class="text-sm text-gray-500">Belum ada fitur. Tambahkan fitur baru di atas.</p>
            @endif
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
                <input type="checkbox" name="is_best_seller" value="1"
                    @checked(old('is_best_seller', $product->is_best_seller ?? false))
                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                <span class="text-sm text-gray-700">Produk Laris Bulan Ini</span>
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
/**
 * 🔥 FUNCTION UNTUK MENGHITUNG DISKON
 * Dihitung dari persentase diskon yang diinput user
 */
function calculateDiscount(element, index) {
    var variantRow = element.closest('.variant-item');
    if (!variantRow) return;
    
    var priceInput = variantRow.querySelector('.price-input');
    var percentInput = variantRow.querySelector('.discount-percent-input');
    var discountPriceInput = variantRow.querySelector('.discount-price-input');
    
    if (!priceInput || !percentInput || !discountPriceInput) return;
    
    var price = parseFloat(priceInput.value) || 0;
    var discountPercent = parseFloat(percentInput.value) || 0;
    
    if (discountPercent > 0 && price > 0) {
        var discountPrice = price - (price * (discountPercent / 100));
        discountPrice = Math.round(discountPrice * 100) / 100;
        discountPriceInput.value = discountPrice.toFixed(2);
        discountPriceInput.style.backgroundColor = '#ecfdf5';
        discountPriceInput.style.color = '#065f46';
        discountPriceInput.style.borderColor = '#6ee7b7';
    } else {
        discountPriceInput.value = '';
        discountPriceInput.style.backgroundColor = '#f3f4f6';
        discountPriceInput.style.color = '#6b7280';
        discountPriceInput.style.borderColor = '#d1d5db';
    }
}

/**
 * 🔥 REKALKULASI SEMUA DISKON SAAT LOAD
 * Menghitung persentase diskon dari harga dan harga diskon yang sudah ada
 */
function recalculateAllDiscounts() {
    document.querySelectorAll('.variant-item').forEach(function(variantRow) {
        var priceInput = variantRow.querySelector('.price-input');
        var percentInput = variantRow.querySelector('.discount-percent-input');
        var discountPriceInput = variantRow.querySelector('.discount-price-input');
        
        if (!priceInput || !discountPriceInput) return;
        
        var price = parseFloat(priceInput.value) || 0;
        var discountPrice = parseFloat(discountPriceInput.value) || 0;
        
        if (price > 0 && discountPrice > 0 && discountPrice < price) {
            var percent = Math.round(((price - discountPrice) / price) * 100);
            if (percentInput) {
                percentInput.value = percent;
            }
            discountPriceInput.style.backgroundColor = '#ecfdf5';
            discountPriceInput.style.color = '#065f46';
            discountPriceInput.style.borderColor = '#6ee7b7';
        } else if (price > 0 && discountPrice > 0 && discountPrice >= price) {
            discountPriceInput.value = '';
            if (percentInput) percentInput.value = 0;
            discountPriceInput.style.backgroundColor = '#f3f4f6';
            discountPriceInput.style.color = '#6b7280';
            discountPriceInput.style.borderColor = '#d1d5db';
        } else if (discountPrice === 0 || discountPrice === '') {
            if (percentInput) percentInput.value = 0;
            discountPriceInput.style.backgroundColor = '#f3f4f6';
            discountPriceInput.style.color = '#6b7280';
            discountPriceInput.style.borderColor = '#d1d5db';
        }
    });
}

/**
 * 🔥 FIX EXISTING VARIANTS
 * Memperbaiki varian yang sudah ada saat load
 */
function fixExistingVariants() {
    document.querySelectorAll('.variant-item').forEach(function(variantRow) {
        var priceInput = variantRow.querySelector('.price-input');
        var discountPriceInput = variantRow.querySelector('.discount-price-input');
        var percentInput = variantRow.querySelector('.discount-percent-input');
        
        if (!priceInput || !discountPriceInput) return;
        
        var price = parseFloat(priceInput.value) || 0;
        var discountPrice = parseFloat(discountPriceInput.value) || 0;
        
        if (price > 0 && discountPrice > 0 && discountPrice < price) {
            var percent = Math.round(((price - discountPrice) / price) * 100);
            if (percentInput) {
                percentInput.value = percent;
            }
            discountPriceInput.style.backgroundColor = '#ecfdf5';
            discountPriceInput.style.color = '#065f46';
            discountPriceInput.style.borderColor = '#6ee7b7';
        } else if (price > 0 && discountPrice > 0 && discountPrice >= price) {
            discountPriceInput.value = '';
            if (percentInput) percentInput.value = 0;
            discountPriceInput.style.backgroundColor = '#f3f4f6';
            discountPriceInput.style.color = '#6b7280';
            discountPriceInput.style.borderColor = '#d1d5db';
        } else if (discountPrice === 0 || discountPrice === '') {
            if (percentInput) percentInput.value = 0;
            discountPriceInput.style.backgroundColor = '#f3f4f6';
            discountPriceInput.style.color = '#6b7280';
            discountPriceInput.style.borderColor = '#d1d5db';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {

    // ============================================
    // 🔥 REMOVE OPTION IMAGE - EVENT LISTENER
    // ============================================
    const optionsContainer = document.getElementById('options-container');
    
    optionsContainer.addEventListener('click', function(event) {
        const removeImageBtn = event.target.closest('.remove-option-image');
        if (!removeImageBtn) return;
        
        const wrapper = removeImageBtn.closest('.option-image-preview-wrapper');
        if (!wrapper) return;
        
        const img = wrapper.querySelector('.option-image-preview');
        const valueRow = wrapper.closest('.option-value-row');
        const hiddenInput = valueRow ? valueRow.querySelector('input[name*="[existing_images]"]') : null;
        const fileInput = valueRow ? valueRow.querySelector('.option-image-input') : null;
        const previewContainer = valueRow ? valueRow.querySelector('.option-image-preview-container') : null;
        
        // Konfirmasi hapus
        if (!confirm('Hapus gambar ini?')) return;
        
        // Hapus gambar dari preview
        if (img) {
            img.src = '';
        }
        
        // Sembunyikan wrapper
        wrapper.classList.add('hidden');
        
        // Tampilkan container preview kosong
        if (previewContainer) {
            previewContainer.classList.remove('hidden');
        }
        
        // Reset hidden input (hapus existing image path)
        if (hiddenInput) {
            hiddenInput.value = '';
        }
        
        // Reset file input
        if (fileInput) {
            fileInput.value = '';
        }
    });

    // ============================================
    // 🔥 INITIALIZE TEXT EDITOR (TinyMCE)
    // ============================================
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '#description',
            height: 400,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            skin: 'oxide',
            skin_url: 'https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/6/skins/ui/oxide',
            branding: false,
            promotion: false,
        });
    }

    // ============================================
    // 🔥 VARIAN SYSTEM
    // ============================================

    const optionsEmpty = document.getElementById('options-empty');
    const variantsContainer = document.getElementById('variants-container');
    const variantsEmpty = document.getElementById('variants-empty');
    const addColorOptionButton = document.getElementById('add-color-option');
    const addSizeOptionButton = document.getElementById('add-size-option');
    const generateVariantsButton = document.getElementById('generate-variants');
    const existingImagesContainer = document.getElementById('existing-images-container');

    const existingOptions = @json($existingOptions ?? []);
    const existingVariants = @json($existingVariants ?? []);

    // Observer untuk recalculate diskon saat variant berubah
    if (variantsContainer) {
        const observer = new MutationObserver(function() {
            setTimeout(recalculateAllDiscounts, 100);
        });
        observer.observe(variantsContainer, { childList: true, subtree: true });
    }

    let optionIndex = 0;
    const DEFAULT_SIZES = ['S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '5XL'];

    // ============================================
    // 🔥 FUNGSI UTILITY
    // ============================================

    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        var div = document.createElement('div');
        div.textContent = String(text);
        return div.innerHTML;
    }

    function updateOptionsEmptyState() {
        var total = optionsContainer.querySelectorAll('.option-item').length;
        optionsEmpty.classList.toggle('hidden', total > 0);
    }
    function updateVariantsEmptyState() {
        var total = variantsContainer.querySelectorAll('.variant-item').length;
        variantsEmpty.classList.toggle('hidden', total > 0);
    }

    // ============================================
    // 🔥 FUNGSI TAMBAH OPSI
    // ============================================

    function addColorOption(optionName = 'Warna', values = [], images = [], oldOptionId = null, valueIds = []) {
        const currentIndex = optionIndex++;
        const optionElement = document.createElement('div');
        optionElement.className = 'option-item rounded-xl border border-blue-200 bg-blue-50 p-5';
        optionElement.dataset.isColor = 'true';

        const oldIdHtml = oldOptionId ? `<input type="hidden" name="options[${currentIndex}][old_id]" value="${oldOptionId}">` : '';

        optionElement.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🎨</span>
                        <label class="block text-sm font-medium text-gray-700">Nama Opsi</label>
                    </div>
                    ${oldIdHtml}
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
                var oldValueId = valueIds[index] || null;
                addOptionValueWithOldId(optionElement, currentIndex, value, image, index, oldValueId);
            });
        } else {
            addOptionValue(optionElement, currentIndex);
        }

        updateOptionsEmptyState();
    }

    function addSizeOption(optionName = 'Ukuran', values = [], oldOptionId = null, valueIds = []) {
        const currentIndex = optionIndex++;
        const optionElement = document.createElement('div');
        optionElement.className = 'option-item rounded-xl border border-green-200 bg-green-50 p-5';
        optionElement.dataset.isSize = 'true';
        optionElement.dataset.optionIndex = currentIndex;
        optionElement.dataset.oldOptionId = oldOptionId || '';

        const sizeValues = values.length > 0 ? values : DEFAULT_SIZES;

        const oldIdHtml = oldOptionId ? `<input type="hidden" name="options[${currentIndex}][old_id]" value="${oldOptionId}">` : '';

        optionElement.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">📏</span>
                        <label class="block text-sm font-medium text-gray-700">Nama Opsi</label>
                    </div>
                    ${oldIdHtml}
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
                <div class="option-values mt-2 space-y-2"></div>
                <button type="button" class="add-option-value mt-3 text-sm font-medium text-green-600 hover:text-green-700">
                    + Tambah Ukuran
                </button>
            </div>
        `;

        optionsContainer.appendChild(optionElement);

        if (sizeValues.length > 0) {
            sizeValues.forEach(function(value, index) {
                const oldValueId = valueIds[index] || null;
                addOptionValueWithOldId(optionElement, currentIndex, value, '', index, oldValueId);
            });
        } else {
            addOptionValue(optionElement, currentIndex);
        }

        updateOptionsEmptyState();
    }

    function addOptionValue(optionElement, optionIndex, value = '', image = '', customValueIndex = null) {
        const valuesContainer = optionElement.querySelector('.option-values');
        
        let valueIndex = customValueIndex !== null ? customValueIndex : valuesContainer.querySelectorAll('.option-value-row').length;
        
        const valueRow = document.createElement('div');
        valueRow.className = 'option-value-row flex flex-wrap items-center gap-2 mb-2';
        valueRow.dataset.valueIndex = valueIndex;

        const isSize = optionElement.dataset.isSize === 'true';

        valueRow.innerHTML = `
            <div class="flex-1 min-w-[120px]">
                <input type="text" name="options[${optionIndex}][values][${valueIndex}]" value="${escapeHtml(value)}" required
                    class="option-value-input block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="${isSize ? 'Contoh: XL' : 'Contoh: Merah'}">
            </div>
            <div class="flex-1 min-w-[100px]">
                ${isSize ? `
                    <input type="hidden" name="options[${optionIndex}][images][${valueIndex}]" value="">
                    <span class="text-sm text-gray-400">(tanpa gambar)</span>
                ` : `
                    <input type="file" name="options[${optionIndex}][images][${valueIndex}]" accept="image/*"
                        class="option-image-input block w-full text-sm text-gray-500 file:mr-2 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                    <input type="hidden" name="options[${optionIndex}][existing_images][${valueIndex}]" value="${image || ''}">
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

        if (!isSize) {
            var fileInput = valueRow.querySelector('.option-image-input');
            var previewContainer = valueRow.querySelector('.option-image-preview-container');
            var previewImg = valueRow.querySelector('.option-image-preview');
            var previewWrapper = valueRow.querySelector('.option-image-preview-wrapper');

            if (fileInput && previewImg) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            if (previewContainer) {
                                previewContainer.classList.remove('hidden');
                            }
                            if (previewWrapper) {
                                previewWrapper.classList.remove('hidden');
                            }
                            // Reset hidden input ketika upload gambar baru
                            var hiddenInput = valueRow.querySelector('input[name*="[existing_images]"]');
                            if (hiddenInput) {
                                hiddenInput.value = '';
                            }
                        };
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }
        }
    }

    function addOptionValueWithOldId(optionElement, optionIndex, value = '', image = '', customValueIndex = null, oldValueId = null) {
        var valuesContainer = optionElement.querySelector('.option-values');
        
        var valueIndex = customValueIndex !== null ? customValueIndex : valuesContainer.querySelectorAll('.option-value-row').length;
        
        var valueRow = document.createElement('div');
        valueRow.className = 'option-value-row flex flex-wrap items-center gap-2 mb-2';
        valueRow.dataset.valueIndex = valueIndex;
        valueRow.dataset.oldValueId = oldValueId || '';

        var isSize = optionElement.dataset.isSize === 'true';
        var normalizedStoredImage = image ? image.replace(/^\/+/, '').replace(/^storage\//, '') : '';
        var previewImage = image ? (image.startsWith('http') || image.startsWith('/storage/') ? image : '/storage/' + normalizedStoredImage) : '';

        valueRow.innerHTML = `
            <div class="flex-1 min-w-[120px]">
                <input type="hidden" name="options[${optionIndex}][old_value_ids][${valueIndex}]" value="${oldValueId || ''}">
                <input type="text" name="options[${optionIndex}][values][${valueIndex}]" value="${escapeHtml(value)}" required
                    class="option-value-input block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="${isSize ? 'Contoh: XL' : 'Contoh: Merah'}">
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
            ${!isSize ? `
                <div class="flex-shrink-0 relative group">
                    <div class="${previewImage ? '' : 'hidden'} option-image-preview-wrapper">
                        <img src="${previewImage}" alt="Preview" class="option-image-preview h-10 w-10 object-cover rounded-lg border border-gray-200">
                        <button type="button" 
                            class="remove-option-image absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white hover:bg-red-600 shadow-md transition-opacity opacity-0 group-hover:opacity-100"
                            title="Hapus gambar">
                            ×
                        </button>
                    </div>
                    <div class="${previewImage ? 'hidden' : ''} option-image-preview-container">
                        <img src="" alt="Preview" class="option-image-preview h-10 w-10 object-cover rounded-lg border border-gray-200">
                    </div>
                    <input type="hidden" name="options[${optionIndex}][existing_images][${valueIndex}]" value="${normalizedStoredImage}">
                </div>
            ` : `
                <input type="hidden" name="options[${optionIndex}][images][${valueIndex}]" value="">
                <span class="text-sm text-gray-400">(tanpa gambar)</span>
            `}
            <button type="button" class="remove-option-value rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600 hover:bg-red-100 flex-shrink-0">
                Hapus
            </button>
        `;

        valuesContainer.appendChild(valueRow);

        if (!isSize) {
            var fileInput = valueRow.querySelector('.option-image-input');
            var previewContainer = valueRow.querySelector('.option-image-preview-container');
            var previewImg = valueRow.querySelector('.option-image-preview');
            var previewWrapper = valueRow.querySelector('.option-image-preview-wrapper');

            if (fileInput && previewImg) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            if (previewContainer) {
                                previewContainer.classList.add('hidden');
                            }
                            if (previewWrapper) {
                                previewWrapper.classList.remove('hidden');
                            }
                            var hiddenInput = valueRow.querySelector('input[name*="[existing_images]"]');
                            if (hiddenInput) {
                                hiddenInput.value = '';
                            }
                        };
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }
        }
    }

    function addColorOptionWithOldId(optionName = 'Warna', values = [], images = [], oldOptionId = null, valueIds = []) {
        var currentIndex = optionIndex++;
        var optionElement = document.createElement('div');
        optionElement.className = 'option-item rounded-xl border border-blue-200 bg-blue-50 p-5';
        optionElement.dataset.isColor = 'true';
        optionElement.dataset.oldOptionId = oldOptionId || '';

        var oldIdHtml = oldOptionId ? `<input type="hidden" name="options[${currentIndex}][old_id]" value="${oldOptionId}">` : '';

        optionElement.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🎨</span>
                        <label class="block text-sm font-medium text-gray-700">Nama Opsi</label>
                    </div>
                    ${oldIdHtml}
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
                var oldValueId = valueIds[index] || null;
                addOptionValueWithOldId(optionElement, currentIndex, value, image, index, oldValueId);
            });
        } else {
            addOptionValue(optionElement, currentIndex);
        }

        updateOptionsEmptyState();
    }

    // ============================================
    // 🔥 FUNGSI GET OPTIONS
    // ============================================

    function getOptions() {
        var optionElements = optionsContainer.querySelectorAll('.option-item');
        var options = [];

        optionElements.forEach(function(optionElement, currentOptionIndex) {
            var nameInput = optionElement.querySelector('.option-name');
            var name = nameInput ? nameInput.value.trim() : '';
            
            var valueInputs = optionElement.querySelectorAll('.option-value-input');
            var values = [];
            
            valueInputs.forEach(function(input) {
                var value = input.value.trim();
                if (value !== '') {
                    values.push(value);
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
    // 🔥 FUNGSI GENERATE KOMBINASI & VARIAN
    // ============================================

    function generateCombinations(options) {
        var result = [[]];
        options.forEach(function(option, optionIdx) {
            var newResult = [];
            result.forEach(function(combination) {
                option.values.forEach(function(value, valueIdx) {
                    newResult.push([...combination, {
                        optionIndex: optionIdx,
                        valueIndex: valueIdx,
                        optionName: option.name,
                        value: value
                    }]);
                });
            });
            result = newResult;
        });
        return result;
    }

    function findExistingVariant(combination) {
        const selectedValueNames = combination.map(function(item) {
            return item.value;
        }).sort().join('|');

        for (var i = 0; i < existingVariants.length; i++) {
            var variant = existingVariants[i];
            var variantValueNames = variant.option_value_names || [];
            var variantKey = variantValueNames.sort().join('|');
            
            if (variantKey === selectedValueNames) {
                return variant;
            }
        }

        return null;
    }

    function findExistingVariantByName(combination) {
        if (!combination || combination.length === 0) return null;
        
        // Ambil nilai dan normalize
        var searchValues = [];
        for (var i = 0; i < combination.length; i++) {
            var val = (combination[i].value || '').trim();
            if (val !== '') {
                searchValues.push(val.toLowerCase());
            }
        }
        searchValues.sort();
        var searchKey = searchValues.join('|');
        
        // Cari di existing variants
        for (var i = 0; i < existingVariants.length; i++) {
            var variant = existingVariants[i];
            var variantValues = [];
            
            if (variant.option_value_names && Array.isArray(variant.option_value_names)) {
                for (var j = 0; j < variant.option_value_names.length; j++) {
                    var val = (variant.option_value_names[j] || '').trim();
                    if (val !== '') {
                        variantValues.push(val.toLowerCase());
                    }
                }
            }
            variantValues.sort();
            var variantKey = variantValues.join('|');
            
            if (variantKey === searchKey) {
                return variant;
            }
        }
        
        return null;
    }

    // ============================================
    // 🔥 RENDER VARIANTS
    // ============================================

    function renderVariants(combinations, isEdit = false) {
        variantsContainer.innerHTML = '';

        var validCombinations = combinations.filter(function(combination) {
            return combination.every(function(item) {
                var value = item.value;
                return value && !value.includes('/') && !value.includes('\\') && !value.includes('fakepath') && value.length < 50;
            });
        });

        if (validCombinations.length === 0) {
            variantsContainer.innerHTML = `
                <div class="p-6 text-center text-sm text-gray-500 bg-yellow-50 rounded-lg border border-yellow-200">
                    ⚠️ Tidak ada kombinasi varian yang valid.
                </div>
            `;
            updateVariantsEmptyState();
            return;
        }

        validCombinations.forEach(function(combination, index) {
            var variant = document.createElement('div');
            variant.className = 'variant-item rounded-xl border border-gray-200 bg-white p-5 shadow-sm';

            var optionValueIndexesHtml = '';
            combination.forEach(function(item) {
                optionValueIndexesHtml += `
                    <input type="hidden" 
                        name="variants[${index}][option_value_indexes][${item.optionIndex}]" 
                        value="${item.valueIndex}">
                `;
            });

            var combinationText = combination.map(function(item) {
                return item.value;
            }).join(' - ');

            variant.innerHTML = `
                ${optionValueIndexesHtml}

                <div class="mb-4 flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-900">${escapeHtml(combinationText)}</p>
                    <span class="text-xs text-blue-600">🆕 Varian baru</span>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-600">SKU</label>
                        <input type="text" name="variants[${index}][sku]" value="" required
                            class="sku-input mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Masukkan SKU">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Harga</label>
                        <input type="number" name="variants[${index}][price]" value="" min="0" required
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 price-input"
                            data-variant-index="${index}"
                            oninput="calculateDiscount(this, ${index})">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Diskon (%)</label>
                        <input type="number" name="variants[${index}][discount_percent]" value="0" min="0" max="100"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 discount-percent-input"
                            data-variant-index="${index}"
                            oninput="calculateDiscount(this, ${index})"
                            placeholder="0">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Harga Diskon</label>
                        <input type="number" name="variants[${index}][discount_price]" value="" min="0" step="0.01"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 discount-price-input"
                            data-variant-index="${index}"
                            placeholder="Otomatis"
                            readonly
                            style="background-color: #f3f4f6; cursor: not-allowed;">
                        <p class="mt-0.5 text-xs text-gray-400">* Dihitung otomatis dari persentase diskon</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Stok</label>
                        <input type="number" 
                            name="variants[${index}][stock]" 
                            value="0" 
                            min="0" 
                            required
                            class="stock-input mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Berat (gram)</label>
                        <input type="number" name="variants[${index}][weight]" value="1000" min="0" required
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            `;

            variantsContainer.appendChild(variant);
        });

        updateVariantsEmptyState();
        
        setTimeout(recalculateAllDiscounts, 100);
    }

    function renderVariantsWithExisting(combinations, isEdit = false) {
        variantsContainer.innerHTML = '';

        // Filter kombinasi valid
        var validCombinations = combinations.filter(function(combination) {
            return combination.every(function(item) {
                var value = item.value;
                return value && value.trim() !== '' && 
                    !value.includes('/') && 
                    !value.includes('\\') && 
                    !value.includes('fakepath') && 
                    value.length < 50;
            });
        });

        if (validCombinations.length === 0) {
            variantsContainer.innerHTML = `
                <div class="p-6 text-center text-sm text-gray-500 bg-yellow-50 rounded-lg border border-yellow-200">
                    ⚠️ Tidak ada kombinasi varian yang valid.
                </div>
            `;
            updateVariantsEmptyState();
            return;
        }

        validCombinations.forEach(function(combination, index) {
            // CARI VARIAN YANG COCOK
            var existingVariant = null;
            
            // Normalisasi kombinasi
            var searchValues = [];
            for (var i = 0; i < combination.length; i++) {
                var val = (combination[i].value || '').trim();
                if (val !== '') {
                    searchValues.push(val.toLowerCase());
                }
            }
            searchValues.sort();
            var searchKey = searchValues.join('|');
            
            // Cari di existing variants
            for (var i = 0; i < existingVariants.length; i++) {
                var variant = existingVariants[i];
                var variantValues = [];
                
                if (variant.option_value_names && Array.isArray(variant.option_value_names)) {
                    for (var j = 0; j < variant.option_value_names.length; j++) {
                        var val = (variant.option_value_names[j] || '').trim();
                        if (val !== '') {
                            variantValues.push(val.toLowerCase());
                        }
                    }
                }
                variantValues.sort();
                var variantKey = variantValues.join('|');
                
                if (variantKey === searchKey) {
                    existingVariant = variant;
                    break;
                }
            }

            // Build HTML variant
            var variant = document.createElement('div');
            variant.className = 'variant-item rounded-xl border border-gray-200 bg-white p-5 shadow-sm';

            var optionValueIndexesHtml = '';
            combination.forEach(function(item) {
                optionValueIndexesHtml += `
                    <input type="hidden" 
                        name="variants[${index}][option_value_indexes][${item.optionIndex}]" 
                        value="${item.valueIndex}">
                `;
            });

            var existingVariantId = existingVariant ?
                `<input type="hidden" name="variants[${index}][id]" value="${existingVariant.id}">` : '';

            var combinationText = combination.map(function(item) {
                return item.value;
            }).join(' - ');

            var sku = existingVariant?.sku ?? '';
            var price = existingVariant?.price ?? '';
            var discountPrice = existingVariant?.discount_price ?? '';
            var stock = existingVariant?.stock ?? 0;
            var weight = existingVariant?.weight ?? '';

            var discountPercent = 0;
            if (price > 0 && discountPrice > 0 && discountPrice < price) {
                discountPercent = Math.round(((price - discountPrice) / price) * 100);
            }

            variant.innerHTML = `
                ${optionValueIndexesHtml}
                ${existingVariantId}

                <div class="mb-4 flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-900">${escapeHtml(combinationText)}</p>
                    ${existingVariant ? '<span class="text-xs text-green-600">✅ Sudah ada</span>' : '<span class="text-xs text-blue-600">🆕 Varian baru</span>'}
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-600">SKU</label>
                        <input type="text" name="variants[${index}][sku]" value="${escapeHtml(sku)}" required
                            class="sku-input mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Masukkan SKU">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Harga</label>
                        <input type="number" name="variants[${index}][price]" value="${escapeHtml(price)}" min="0" required
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 price-input"
                            data-variant-index="${index}"
                            oninput="calculateDiscount(this, ${index})">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Diskon (%)</label>
                        <input type="number" name="variants[${index}][discount_percent]" value="${discountPercent}" min="0" max="100"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 discount-percent-input"
                            data-variant-index="${index}"
                            oninput="calculateDiscount(this, ${index})"
                            placeholder="0">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Harga Diskon</label>
                        <input type="number" name="variants[${index}][discount_price]" value="${escapeHtml(discountPrice)}" min="0" step="0.01"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 discount-price-input"
                            data-variant-index="${index}"
                            placeholder="Otomatis"
                            readonly
                            style="background-color: #f3f4f6; cursor: not-allowed;">
                        <p class="mt-0.5 text-xs text-gray-400">* Dihitung otomatis dari persentase diskon</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Stok</label>
                        <input type="number" 
                            name="variants[${index}][stock]" 
                            value="${stock}" 
                            min="0" 
                            required
                            class="stock-input mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
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

        setTimeout(function() {
            recalculateAllDiscounts();
            fixExistingVariants();
        }, 100);
    }

    // ============================================
    // 🔥 EVENT LISTENERS
    // ============================================

    addColorOptionButton.addEventListener('click', function() {
        addColorOption();
    });

    addSizeOptionButton.addEventListener('click', function() {
        addSizeOption();
    });

    generateVariantsButton.addEventListener('click', function() {
        const options = getOptions();

        if (options.length === 0) {
            alert('⚠️ Tambahkan minimal satu opsi produk (Warna atau Ukuran).');
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
        
        // 🔥 KIRIMKAN PARAMETER isEdit
        const isEdit = {{ $isEdit ? 'true' : 'false' }};
        renderVariants(combinations, isEdit);
    });

    optionsContainer.addEventListener('click', function(event) {
        const removeOptionButton = event.target.closest('.remove-option');
        if (removeOptionButton) {
            const optionElement = removeOptionButton.closest('.option-item');
            if (optionElement && confirm('Hapus opsi ini dan semua nilainya?')) {
                optionElement.remove();
                updateOptionsEmptyState();
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
                if (totalRows <= 1 && !confirm('Ini adalah nilai terakhir. Hapus?')) {
                    return;
                }
                valueRow.remove();
            }
        }
    });

    // Remove existing image
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
    // 🔥 LOAD EXISTING OPTIONS
    // ============================================

    if (existingOptions.length > 0) {
        console.log('✅ Loading existing options:', existingOptions.length);
        
        optionIndex = 0;
        
        existingOptions.forEach(function(option) {
            const optionValues = Array.isArray(option.values) ? option.values : [];
            const values = optionValues.map(function(item) {
                return item.value || '';
            }).filter(function(v) { return v !== ''; });
            const images = optionValues.map(function(item) { return item.image || ''; });
            const valueIds = optionValues.map(function(item) { return item.id || null; });
            const oldOptionId = option.id || null;
            
            if (values.length > 0) {
                if ((option.name || '').toLowerCase().trim() === 'ukuran' || (option.name || '').toLowerCase().trim() === 'size') {
                    addSizeOption(option.name, values, oldOptionId, valueIds);
                } else {
                    addColorOptionWithOldId(option.name, values, images, oldOptionId, valueIds);
                }
            }
        });

        setTimeout(function() {
            var options = getOptions();
            console.log('📋 Current options after load:', options);
            
            if (options.length > 0) {
                var combinations = generateCombinations(options);
                console.log('🔗 Generated combinations:', combinations.length);
                
                // 🔥 KIRIMKAN PARAMETER isEdit
                const isEdit = {{ $isEdit ? 'true' : 'false' }};
                renderVariantsWithExisting(combinations, isEdit);
            }
        }, 300);
    } else {
        console.log('ℹ️ No existing options to load');
    }

    // ============================================
    // 🔥 FEATURE - INLINE ADD
    // ============================================
    const addFeatureBtn = document.getElementById('add-feature-btn');
    const featureNameInput = document.getElementById('new-feature-name');
    const featureFeedback = document.getElementById('feature-feedback');
    const featuresList = document.getElementById('features-list');
    const noFeaturesMsg = document.getElementById('no-features-message');

    function showFeedback(message, type = 'success') {
        featureFeedback.textContent = message;
        featureFeedback.className = 'mt-2 text-sm ' + (type === 'success' ? 'text-green-600' : 'text-red-600');
        featureFeedback.classList.remove('hidden');
        
        setTimeout(function() {
            featureFeedback.classList.add('hidden');
        }, 3000);
    }

    if (addFeatureBtn) {
        addFeatureBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const name = featureNameInput.value.trim();
            
            if (!name) {
                showFeedback('⚠️ Nama fitur wajib diisi!', 'error');
                featureNameInput.focus();
                return;
            }
            
            const csrfToken = document.querySelector('input[name="_token"]')?.value || 
                            document.querySelector('meta[name="csrf-token"]')?.content || '';
            
            addFeatureBtn.disabled = true;
            addFeatureBtn.textContent = 'Menyimpan...';
            
            fetch('{{ route("admin.features.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: name
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const label = document.createElement('label');
                    label.className = 'feature-item flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 hover:bg-gray-50 cursor-pointer transition shadow-sm';
                    label.innerHTML = `
                        <input type="checkbox" 
                            name="features[]" 
                            value="${data.feature.id}" 
                            checked
                            class="feature-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">${data.feature.name}</span>
                    `;
                    
                    if (noFeaturesMsg) {
                        noFeaturesMsg.remove();
                    }
                    
                    featuresList.appendChild(label);
                    featureNameInput.value = '';
                    showFeedback('✅ Fitur "' + data.feature.name + '" berhasil ditambahkan!', 'success');
                } else {
                    showFeedback('❌ ' + (data.message || 'Gagal menambahkan fitur'), 'error');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                showFeedback('❌ Terjadi kesalahan. Silakan coba lagi.', 'error');
            })
            .finally(function() {
                addFeatureBtn.disabled = false;
                addFeatureBtn.textContent = '+ Tambah Fitur';
            });
        });
    }

    if (featureNameInput) {
        featureNameInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (addFeatureBtn) {
                    addFeatureBtn.click();
                }
            }
        });
    }

    // ============================================
    // 🔥 INITIAL STATE
    // ============================================

    updateOptionsEmptyState();
    updateVariantsEmptyState();

    // 🔥 PERBAIKI VARIAN YANG SUDAH ADA
    setTimeout(function() {
        fixExistingVariants();
        recalculateAllDiscounts();
    }, 400);

    console.log('✅ Product form initialized with integrated variant system');

});

// ============================================
// 🔥 SAVE TINYMCE CONTENT SEBELUM SUBMIT
// ============================================
document.addEventListener('submit', function(e) {
    if (e.target.id === 'product-form') {
        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }
    }
});
</script>