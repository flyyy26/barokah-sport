<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Alamat - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">

    {{-- NAVBAR --}}
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
            <a href="{{ route('customer.home') }}" class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 font-bold text-white">
                    {{ strtoupper(substr(config('app.name'), 0, 1)) }}
                </div>
                <span class="font-bold text-slate-900">{{ config('app.name') }}</span>
            </a>
            <a href="{{ route('customer.account') }}" class="text-sm text-slate-500 hover:text-slate-700">← Kembali</a>
        </div>
    </header>

    <main class="mx-auto max-w-2xl px-6 py-10 lg:px-8">

        <h1 class="text-2xl font-bold text-slate-900">Tambah Alamat Baru</h1>
        <p class="mt-1 text-sm text-slate-500">Simpan alamat untuk memudahkan proses checkout.</p>

        <form action="{{ route('customer.addresses.store') }}" method="POST" class="mt-6 space-y-5">
            @csrf

            {{-- Label --}}
            <div>
                <label for="label" class="mb-1 block text-sm font-medium text-slate-700">Label Alamat <span class="text-slate-400">(opsional)</span></label>
                <input type="text" name="label" id="label" value="{{ old('label') }}" placeholder="Contoh: Rumah, Kantor" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition">
            </div>

            {{-- Recipient Name --}}
            <div>
                <label for="recipient_name" class="mb-1 block text-sm font-medium text-slate-700">Nama Penerima <span class="text-red-500">*</span></label>
                <input type="text" name="recipient_name" id="recipient_name" value="{{ old('recipient_name', auth('customer')->user()->name) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition @error('recipient_name') border-red-400 @enderror">
                @error('recipient_name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Recipient Phone --}}
            <div>
                <label for="recipient_phone" class="mb-1 block text-sm font-medium text-slate-700">Nomor Telepon <span class="text-red-500">*</span></label>
                <input type="text" name="recipient_phone" id="recipient_phone" value="{{ old('recipient_phone', auth('customer')->user()->phone) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition @error('recipient_phone') border-red-400 @enderror">
                @error('recipient_phone') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Address --}}
            <div>
                <label for="address" class="mb-1 block text-sm font-medium text-slate-700">Alamat Lengkap <span class="text-red-500">*</span></label>
                <textarea name="address" id="address" rows="3" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition @error('address') border-red-400 @enderror">{{ old('address') }}</textarea>
                @error('address') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- City --}}
            <div>
                <label for="city" class="mb-1 block text-sm font-medium text-slate-700">Kota <span class="text-red-500">*</span></label>
                <input type="text" name="city" id="city" value="{{ old('city') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition @error('city') border-red-400 @enderror">
                @error('city') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Province --}}
            <div>
                <label for="province" class="mb-1 block text-sm font-medium text-slate-700">Provinsi <span class="text-red-500">*</span></label>
                <input type="text" name="province" id="province" value="{{ old('province') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition @error('province') border-red-400 @enderror">
                @error('province') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Postal Code --}}
            <div>
                <label for="postal_code" class="mb-1 block text-sm font-medium text-slate-700">Kode Pos <span class="text-red-500">*</span></label>
                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition @error('postal_code') border-red-400 @enderror">
                @error('postal_code') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Set as Default --}}
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default') ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <label for="is_default" class="text-sm text-slate-600">Jadikan alamat utama</label>
            </div>

            <button type="submit" class="w-full rounded-xl bg-slate-900 py-3.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                Simpan Alamat
            </button>
        </form>

    </main>

</body>
</html>