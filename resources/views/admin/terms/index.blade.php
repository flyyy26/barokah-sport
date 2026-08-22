@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Syarat & Ketentuan</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola syarat dan ketentuan yang berlaku di toko.</p>
    </div>

    {{-- SUCCESS & ERROR --}}
    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- FORM --}}
    <form action="{{ route('admin.terms.update') }}" method="POST" class="space-y-6" id="terms-form">
        @csrf
        @method('PUT')

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            {{-- STATUS BADGE --}}
            <div class="mb-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-gray-700">Status:</span>
                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $term && $term->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $term && $term->is_active ? '✅ Aktif' : '❌ Nonaktif' }}
                    </span>
                </div>
                @if($term)
                    <form action="{{ route('admin.terms.toggle') }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="rounded-lg px-4 py-2 text-sm font-medium {{ $term->is_active ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                            {{ $term->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                @endif
            </div>

            {{-- JUDUL --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Judul</label>
                <input type="text" name="title" 
                       value="{{ old('title', $term?->title ?? 'Syarat & Ketentuan Barokah Sport') }}"
                       class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- KONTEN DENGAN TINYMCE --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Konten</label>
                <textarea name="content" id="terms_content" rows="15" 
                          class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Tuliskan syarat dan ketentuan...">{{ old('content', $term?->content ?? '') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- VERSI --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Versi</label>
                    <input type="text" name="version" 
                           value="{{ old('version', $term?->version ?? '1.0') }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Contoh: 1.0">
                    @error('version')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- TANGGAL EFEKTIF --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Efektif</label>
                    <input type="date" name="effective_date" 
                           value="{{ old('effective_date', $term?->effective_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('effective_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- STATUS --}}
            <div class="mt-5 flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       {{ old('is_active', $term?->is_active ?? true) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="is_active" class="ml-2 block text-sm text-gray-700">Aktif</label>
            </div>
        </div>

        {{-- ACTION --}}
        <div class="flex justify-end rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <button type="submit" 
                    class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Syarat & Ketentuan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init({
            selector: '#terms_content',
            height: 400,
            menubar: false,
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
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });
        
        const form = document.getElementById('terms-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                tinymce.triggerSave();
            });
        }
    });
</script>
@endpush