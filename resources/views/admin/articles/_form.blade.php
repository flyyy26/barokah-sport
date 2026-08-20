@php
    $isEdit = $isEdit ?? false;
    $article = $article ?? null;
@endphp

<div class="space-y-6">

    {{-- Title --}}
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
            Judul Artikel <span class="text-red-500">*</span>
        </label>
        <input type="text" name="title" id="title" 
               value="{{ old('title', $article->title ?? '') }}" required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('title') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
    </div>

    {{-- 🔥 KATEGORI --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Kategori <span class="text-red-500">*</span>
        </label>
        <div class="flex gap-2">
            <select name="article_category_id" id="article_category_id" required
                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Kategori --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" 
                            {{ (old('article_category_id', $article->article_category_id ?? '') == $cat->id) ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            <button type="button" id="btn-add-category" 
                    class="px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition flex items-center gap-1 whitespace-nowrap">
                <span class="text-lg">+</span>
                <span class="hidden sm:inline">Tambah</span>
            </button>
            <button type="button" id="btn-delete-category" 
                    class="px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition flex items-center gap-1 whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                <span class="text-lg">×</span>
                <span class="hidden sm:inline">Hapus</span>
            </button>
        </div>
        @error('article_category_id') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
    </div>

    {{-- Author --}}
    <div>
        <label for="author" class="block text-sm font-medium text-gray-700 mb-2">
            Penulis
        </label>
        <input type="text" name="author" id="author" 
               value="{{ old('author', $article->author ?? 'Admin') }}"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    {{-- Tags --}}
    <div>
        <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
            Tags (pisahkan dengan koma)
        </label>
        <input type="text" name="tags" id="tags" 
               value="{{ old('tags', isset($article) ? $article->tags_string : '') }}"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="Contoh: olahraga, jaket, running">
        <p class="text-xs text-gray-500 mt-1">Pisahkan tag dengan koma (,)</p>
    </div>

    {{-- Excerpt --}}
    <div>
        <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
            Deskripsi Pendek (Excerpt)
        </label>
        <textarea name="excerpt" id="excerpt" rows="3"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Ringkasan singkat artikel...">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
        <p class="text-xs text-gray-500 mt-1">Jika kosong, akan diambil dari konten secara otomatis (max 150 karakter).</p>
    </div>

    {{-- Content - CKEditor --}}
    <div>
        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
            Konten <span class="text-red-500">*</span>
        </label>
        <textarea name="content" id="editor" rows="10"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Isi artikel...">{{ old('content', $article->content ?? '') }}</textarea>
        @error('content') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
    </div>

    {{-- Image --}}
    <div>
        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
            Gambar Artikel
        </label>
        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg">
        <p class="text-xs text-gray-500 mt-1">Maksimal 2MB. Format JPG, PNG, atau WebP.</p>
        @error('image') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror

        @if(isset($article) && $article->image)
            <div class="mt-4">
                <img src="{{ Storage::url($article->image) }}" 
                     class="w-32 h-32 object-cover rounded-lg border">
            </div>
        @endif
    </div>

    {{-- Published Date --}}
    <div>
        <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
            Tanggal Publikasi
        </label>
        <input type="datetime-local" name="published_at" id="published_at" 
               value="{{ old('published_at', isset($article) && $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    {{-- Status --}}
    <div class="border-t pt-6">
        <div class="flex flex-wrap items-center gap-8">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1"
                    @checked(old('is_active', $article->is_active ?? true))
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-700">Aktif</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_featured" value="1"
                    @checked(old('is_featured', $article->is_featured ?? false))
                    class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                <span class="text-sm text-gray-700">🌟 Artikel Unggulan</span>
            </label>
        </div>
    </div>

</div>

{{-- 🔥 MODAL TAMBAH KATEGORI --}}
<div id="modal-add-category" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Tambah Kategori</h3>
            <button type="button" onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-600">
                ✕
            </button>
        </div>
        <form id="form-add-category" onsubmit="return false;">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori</label>
                <input type="text" id="new-category-name" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Masukkan nama kategori..." required>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeCategoryModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button type="button" id="btn-save-category"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

{{-- 🔥 JAVASCRIPT SIMPLIFIED --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectCategory = document.getElementById('article_category_id');
    const btnAdd = document.getElementById('btn-add-category');
    const btnDelete = document.getElementById('btn-delete-category');
    const modal = document.getElementById('modal-add-category');
    const newCategoryName = document.getElementById('new-category-name');
    const btnSaveCategory = document.getElementById('btn-save-category');
    const submitBtn = document.getElementById('submit-article-btn');
    const articleForm = document.getElementById('article-form');

    // ============================================
    // 🔥 TAMBAH KATEGORI
    // ============================================

    btnAdd.addEventListener('click', function(e) {
        e.preventDefault();
        modal.classList.remove('hidden');
        newCategoryName.focus();
    });

    window.closeCategoryModal = function() {
        modal.classList.add('hidden');
        newCategoryName.value = '';
    };

    btnSaveCategory.addEventListener('click', function(e) {
        e.preventDefault();
        saveCategory();
    });

    newCategoryName.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveCategory();
        }
    });

    function saveCategory() {
        const name = newCategoryName.value.trim();
        if (!name) {
            alert('Nama kategori wajib diisi!');
            newCategoryName.focus();
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        btnSaveCategory.disabled = true;
        btnSaveCategory.textContent = 'Menyimpan...';

        fetch('{{ route("admin.article-categories.ajax.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: name })
        })
        .then(response => response.json())
        .then(data => {
            btnSaveCategory.disabled = false;
            btnSaveCategory.textContent = 'Simpan Kategori';

            if (data.success) {
                const option = document.createElement('option');
                option.value = data.category.id;
                option.textContent = data.category.name;
                selectCategory.appendChild(option);
                selectCategory.value = data.category.id;
                btnDelete.disabled = false;
                closeCategoryModal();
                alert(data.message);
            } else {
                alert(data.message || 'Gagal menambahkan kategori');
            }
        })
        .catch(() => {
            btnSaveCategory.disabled = false;
            btnSaveCategory.textContent = 'Simpan Kategori';
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    }

    // ============================================
    // 🔥 HAPUS KATEGORI
    // ============================================

    function updateDeleteButton() {
        btnDelete.disabled = !selectCategory.value;
    }

    selectCategory.addEventListener('change', updateDeleteButton);
    updateDeleteButton();

    btnDelete.addEventListener('click', function(e) {
        e.preventDefault();

        const categoryId = selectCategory.value;
        const categoryName = selectCategory.options[selectCategory.selectedIndex]?.text;

        if (!categoryId) {
            alert('Pilih kategori yang akan dihapus.');
            return;
        }

        if (!confirm(`Hapus kategori "${categoryName}"?`)) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        btnDelete.disabled = true;
        btnDelete.innerHTML = '⏳';

        fetch(`{{ url('admin/article-categories/ajax') }}/${categoryId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            btnDelete.innerHTML = '× <span class="hidden sm:inline">Hapus</span>';
            btnDelete.disabled = false;

            if (data.success) {
                const option = selectCategory.querySelector(`option[value="${categoryId}"]`);
                if (option) {
                    option.remove();
                }
                selectCategory.value = '';
                btnDelete.disabled = true;
                alert(data.message);
            } else {
                alert(data.message || 'Gagal menghapus kategori');
                updateDeleteButton();
            }
        })
        .catch(() => {
            btnDelete.innerHTML = '× <span class="hidden sm:inline">Hapus</span>';
            btnDelete.disabled = false;
            updateDeleteButton();
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    });

    // ============================================
    // 🔥 TUTUP MODAL
    // ============================================

    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeCategoryModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeCategoryModal();
        }
    });

    // ============================================
    // 🔥 SUBMIT FORM - PAKAI TOMBOL
    // ============================================

    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            console.log('🔄 Submit button clicked');
            
            // 🔥 AMBIL DATA CKEDITOR
            if (window.ckEditorInstance) {
                const editorData = window.ckEditorInstance.getData();
                document.querySelector('#editor').value = editorData;
                console.log('📝 CKEditor content saved:', editorData.length);
            }
            
            // 🔥 VALIDASI
            const title = document.getElementById('title').value.trim();
            const category = document.getElementById('article_category_id').value;
            const content = document.querySelector('#editor').value.trim();
            
            if (!title) {
                alert('Judul artikel wajib diisi!');
                document.getElementById('title').focus();
                return;
            }
            
            if (!category) {
                alert('Kategori wajib dipilih!');
                document.getElementById('article_category_id').focus();
                return;
            }
            
            if (!content || content === '<p>&nbsp;</p>' || content === '<p></p>' || content === '') {
                alert('Konten artikel wajib diisi!');
                document.getElementById('editor').focus();
                return;
            }
            
            // 🔥 SUBMIT FORM
            console.log('✅ Submitting form...');
            articleForm.submit();
        });
    }

    // ============================================
    // 🔥 CKEDITOR
    // ============================================

    if (typeof ClassicEditor !== 'undefined') {
        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'bulletedList', 'numberedList', '|',
                        'outdent', 'indent', '|',
                        'link', 'blockQuote', 'insertTable', '|',
                        'undo', 'redo'
                    ]
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraf', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                    ]
                },
                language: 'id',
                placeholder: 'Tulis konten artikel di sini...',
                licenseKey: 'GPL',
                version: 'GPL'
            })
            .then(editor => {
                window.ckEditorInstance = editor;
                console.log('✅ CKEditor initialized');
            })
            .catch(error => {
                console.error('CKEditor error:', error);
            });
    }

    console.log('✅ Article form initialized');
});
</script>

<style>
    .ck-editor__editable {
        min-height: 300px !important;
        max-height: 500px !important;
    }
    #modal-add-category.hidden {
        display: none !important;
    }
</style>