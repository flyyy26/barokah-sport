@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah FAQ</h1>
        <p class="mt-1 text-sm text-gray-500">Tambahkan pertanyaan baru untuk pelanggan.</p>
    </div>

    <form action="{{ route('admin.faqs.store') }}" method="POST" class="space-y-6">
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

            {{-- JAWABAN --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Jawaban</label>
                <textarea name="answer" rows="5" 
                          class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Tuliskan jawaban lengkap..." required>{{ old('answer') }}</textarea>
                @error('answer')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- KATEGORI --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Kategori</label>
                <select name="category" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach ($categories as $key => $label)
                        <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('category')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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