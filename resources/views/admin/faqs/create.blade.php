@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah FAQ</h1>
        <p class="mt-1 text-sm text-gray-500">Tambahkan pertanyaan baru untuk pelanggan.</p>
    </div>

    <form action="{{ route('admin.faqs.store') }}" method="POST" class="space-y-6" id="faq-form">
        @csrf

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            {{-- PERTANYAAN --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Pertanyaan</label>
                <input type="text" name="question" value="{{ old('question') }}"
                       class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Contoh: Bagaimana cara memesan produk?" required>
                @error('question')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- JAWABAN DENGAN TINYMCE --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Jawaban</label>
                <textarea name="answer" id="faq_answer" rows="8" 
                          class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Tuliskan jawaban lengkap..." required>{{ old('answer') }}</textarea>
                @error('answer')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- KATEGORI --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Kategori</label>
                <select name="category_id" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="">Pilih Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">
                    <a href="{{ route('admin.faqs.categories') }}" class="text-blue-600 hover:underline">Kelola Kategori</a>
                </p>
            </div>

            {{-- URUTAN --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Urutan</label>
                <input type="number" name="order" value="{{ old('order', $faqs->count() + 1 ?? 1) }}"
                       class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       min="0">
                <p class="mt-1 text-xs text-gray-500">Semakin kecil angka, semakin atas tampilannya.</p>
                @error('order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- STATUS --}}
            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" checked
                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label class="ml-2 block text-sm text-gray-700">Aktif</label>
            </div>
        </div>

        {{-- ACTION --}}
        <div class="flex justify-end gap-3 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <a href="{{ route('admin.faqs.index') }}" 
               class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" 
                    class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                Simpan FAQ
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/f0qff2j87jgv24lrb8m0hd4yuglweewk56pa79tykafgtc6g/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init({
            selector: '#faq_answer',
            height: 250,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });

        const form = document.getElementById('faq-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                tinymce.triggerSave();
            });
        }
    });
</script>
@endpush