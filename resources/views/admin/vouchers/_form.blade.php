<div class="space-y-6">
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Mohon perbaiki kesalahan berikut:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Informasi Utama --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Informasi Voucher</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Nama Voucher <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $voucher->name ?? '') }}" required
                    class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Contoh: Diskon Gajian 100RB">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Kode Promo <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="{{ old('code', $voucher->code ?? '') }}" required
                    class="mt-1.5 block w-full uppercase rounded-lg border-gray-300 shadow-sm text-sm font-mono tracking-wider focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Contoh: PAYDAY100K">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Deskripsi Singkat</label>
            <textarea name="description" rows="2"
                class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Potongan belanja spesial gajian untuk semua kategori.">{{ old('description', $voucher->description ?? '') }}</textarea>
        </div>
    </div>

    {{-- Skema Diskon & Ketentuan Transaksi --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Skema Diskon & Batasan Belanja</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Tipe Diskon <span class="text-red-500">*</span></label>
                <select name="discount_type" id="discount_type" class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="fixed" {{ old('discount_type', $voucher->discount_type ?? '') === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                    <option value="percentage" {{ old('discount_type', $voucher->discount_type ?? '') === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Nilai Potongan <span id="discount_unit_label">(Rp)</span> <span class="text-red-500">*</span></label>
                <input type="number" name="discount_value" id="discount_value" value="{{ old('discount_value', $voucher->discount_value ?? '') }}" min="0" required
                    class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="100000">
            </div>

            <div id="max_discount_wrapper" class="hidden">
                <label class="block text-xs font-semibold text-gray-700 uppercase">Maksimal Potongan (Rp)</label>
                <input type="number" name="max_discount_amount" value="{{ old('max_discount_amount', $voucher->max_discount_amount ?? '') }}" min="0"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: 150000">
                <p class="mt-1 text-[11px] text-gray-500">Kosongkan jika tanpa batas maks potongan.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Minimal Transaksi (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="min_transaction_amount" value="{{ old('min_transaction_amount', $voucher->min_transaction_amount ?? 0) }}" min="0" required
                    class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="300000">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Total Kuota Penggunaan</label>
                <input type="number" name="usage_limit" value="{{ old('usage_limit', $voucher->usage_limit ?? '') }}" min="1"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: 100">
                <p class="mt-1 text-[11px] text-gray-500">Kosongkan jika kuota tidak terbatas.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Batas Pakai Per Akun <span class="text-red-500">*</span></label>
                <input type="number" name="limit_per_user" value="{{ old('limit_per_user', $voucher->limit_per_user ?? 1) }}" min="1" required
                    class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="1">
            </div>
        </div>
    </div>

    {{-- Periode & Syarat Ketentuan --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Periode & Syarat Ketentuan</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Tanggal Mulai <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="start_date" required
                    value="{{ old('start_date', isset($voucher->start_date) ? $voucher->start_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Tanggal Berakhir <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="end_date" required
                    value="{{ old('end_date', isset($voucher->end_date) ? $voucher->end_date->format('Y-m-d\TH:i') : now()->addDays(7)->format('Y-m-d\TH:i')) }}"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Syarat & Ketentuan (S&K)</label>
            <textarea id="terms_and_conditions" name="terms_and_conditions" rows="6"
                class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="1. Berlaku untuk seluruh produk. 2. Tidak dapat digabung dengan promo lain.">{{ old('terms_and_conditions', $voucher->terms_and_conditions ?? '') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                    {{ old('is_active', $voucher->is_active ?? true) ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="is_active" class="text-sm font-medium text-gray-900">Aktifkan Voucher Ini</label>
            </div>

            <div class="flex items-start gap-2">
                <input type="checkbox" id="is_public" name="is_public" value="1"
                    {{ old('is_public', $voucher->is_public ?? true) ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 mt-0.5">
                <div>
                    <label for="is_public" class="text-sm font-medium text-gray-900">Tampilkan di Daftar Voucher (Publik)</label>
                    <p class="text-xs text-gray-500">Customer bisa langsung klik tombol "Pakai" saat checkout tanpa harus mengetik kode promo.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tombol Aksi --}}
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.vouchers.index') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Batal
        </a>
        <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 shadow-sm">
            {{ isset($voucher) ? 'Simpan Perubahan' : 'Buat Voucher' }}
        </button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var discountType = document.getElementById('discount_type');
        var maxWrapper = document.getElementById('max_discount_wrapper');
        var unitLabel = document.getElementById('discount_unit_label');
        var valInput = document.getElementById('discount_value');

        function updateFields() {
            if (discountType.value === 'percentage') {
                maxWrapper.classList.remove('hidden');
                unitLabel.textContent = '(%)';
                valInput.placeholder = 'Contoh: 20';
            } else {
                maxWrapper.classList.add('hidden');
                unitLabel.textContent = '(Rp)';
                valInput.placeholder = 'Contoh: 100000';
            }
        }

        discountType.addEventListener('change', updateFields);
        updateFields();
    });
</script>
@endpush