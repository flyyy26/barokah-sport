@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tentang Kami</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola halaman tentang kami yang akan ditampilkan di toko.</p>
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
    <form action="{{ route('admin.about.update') }}" method="POST" class="space-y-6" id="about-form">
        @csrf
        @method('PUT')

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            {{-- STATUS BADGE --}}
            <div class="mb-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-gray-700">Status:</span>
                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $about && $about->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $about && $about->is_active ? '✅ Aktif' : '❌ Nonaktif' }}
                    </span>
                </div>
                @if($about)
                    <button type="button" 
                            onclick="toggleAbout()"
                            class="rounded-lg px-4 py-2 text-sm font-medium {{ $about->is_active ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                        {{ $about->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                @endif
            </div>

            {{-- JUDUL --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Judul</label>
                <input type="text" name="title" 
                       value="{{ old('title', $about?->title ?? 'Tentang Kami Barokah Sport') }}"
                       class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- KONTEN UTAMA --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Konten Utama</label>
                <textarea name="content" id="about_content" rows="15" 
                          class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Tuliskan tentang kami...">{{ old('content', $about?->content ?? '') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- VISI --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Visi</label>
                <textarea name="vision" id="about_vision" rows="5" 
                          class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Tuliskan visi perusahaan...">{{ old('vision', $about?->vision ?? '') }}</textarea>
                @error('vision')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- MISI --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Misi</label>
                <textarea name="mission" id="about_mission" rows="5" 
                          class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Tuliskan misi perusahaan...">{{ old('mission', $about?->mission ?? '') }}</textarea>
                @error('mission')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- VERSI --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Versi</label>
                    <input type="text" name="version" 
                           value="{{ old('version', $about?->version ?? '1.0') }}"
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
                           value="{{ old('effective_date', $about?->effective_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('effective_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- STATUS --}}
            <div class="mt-5 flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       {{ old('is_active', $about?->is_active ?? true) ? 'checked' : '' }}
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
                Simpan Tentang Kami
            </button>
        </div>
    </form>

    {{-- FORM TOGGLE TERPISAH --}}
    @if($about)
        <form action="{{ route('admin.about.toggle') }}" method="POST" id="toggle-form" style="display: none;">
            @csrf
            @method('PATCH')
        </form>
    @endif

    {{-- PREVIEW --}}
    @if($about && $about->content)
        <div class="mt-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">📄 Preview</h2>
            
            {{-- PREVIEW KONTEN UTAMA --}}
            <div class="prose prose-blue max-w-none border-t border-gray-200 pt-4">
                <h3 class="text-sm font-semibold text-gray-500 mb-2">Konten Utama</h3>
                {!! $about->content !!}
            </div>
            
            {{-- PREVIEW VISI --}}
            @if($about->vision)
                <div class="mt-4 border-t border-gray-200 pt-4">
                    <h3 class="text-sm font-semibold text-gray-500 mb-2">Visi</h3>
                    <div class="prose prose-blue max-w-none">
                        {!! $about->vision !!}
                    </div>
                </div>
            @endif
            
            {{-- PREVIEW MISI --}}
            @if($about->mission)
                <div class="mt-4 border-t border-gray-200 pt-4">
                    <h3 class="text-sm font-semibold text-gray-500 mb-2">Misi</h3>
                    <div class="prose prose-blue max-w-none">
                        {!! $about->mission !!}
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/f0qff2j87jgv24lrb8m0hd4yuglweewk56pa79tykafgtc6g/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi TinyMCE untuk Konten Utama
        tinymce.init({
            selector: '#about_content',
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

        // Inisialisasi TinyMCE untuk Visi
        tinymce.init({
            selector: '#about_vision',
            height: 200,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });

        // Inisialisasi TinyMCE untuk Misi
        tinymce.init({
            selector: '#about_mission',
            height: 200,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });

        // Pastikan semua konten tersimpan sebelum submit
        const form = document.getElementById('about-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                tinymce.triggerSave();
            });
        }
    });

    function toggleAbout() {
        if (confirm('Apakah Anda yakin ingin mengubah status Tentang Kami?')) {
            document.getElementById('toggle-form').submit();
        }
    }
</script>
@endpush