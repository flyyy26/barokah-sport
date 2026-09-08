@extends('layouts.admin')

@section('title', 'Retur #' . $order->order_number . ' - Admin E-Commerce')
@section('page-title', '🔍 Detail Retur')

@section('content')
<div class="ml-64 p-8">
    <div class="mx-auto max-w-5xl">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                <div class="flex items-center">
                    <svg class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3-8a3 3 0 11-6 0 3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-2 text-sm font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                <div class="flex items-center">
                    <svg class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 9.293a1 1 0 000 1.414l3 3 3-3a1 1 0 00-1.414-1.414L10 11.586l-2.293-2.293a1 1 0 00-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-2 text-sm font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('admin.returns.index') }}" class="text-sm text-blue-600 hover:text-blue-800">← Kembali ke Retur</a>
                <a href="{{ route('admin.orders.show', $order) }}" class="text-sm text-blue-600 hover:text-blue-800 ml-4">Pesanan #{{ $order->order_number }}</a>
                <h1 class="mt-1 text-2xl font-bold text-slate-900">Detail Retur</h1>
                <p class="text-sm text-slate-500">Status:
                    <span class="font-medium
                        @if($order->return_status == 'pending') text-yellow-800
                        @elseif($order->return_status == 'approved') text-blue-800
                        @elseif($order->return_status == 'completed') text-emerald-800
                        @elseif($order->return_status == 'rejected') text-red-800 @endif">
                        {{ $order->return_status_label }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Return Info --}}
        <div class="mb-6 rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="font-semibold text-slate-900">📋 Informasi Retur</h2>
            <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-500">Pelanggan</p>
                    <p class="font-medium text-slate-900">{{ $order->user->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Email</p>
                    <p class="font-medium text-slate-900">{{ $order->user->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Alasan Retur</p>
                    <p class="font-medium text-slate-900">{{ $order->return_reason ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Tanggal Permintaan</p>
                    <p class="font-medium text-slate-900">{{ $order->return_requested_at?->format('d M Y H:i') ?? '-' }}</p>
                </div>
                @if($order->return_processed_at)
                    <div>
                        <p class="text-slate-500">Tanggal Diproses</p>
                        <p class="font-medium text-slate-900">{{ $order->return_processed_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Diproses Oleh</p>
                        <p class="font-medium text-slate-900">{{ $order->returnedByAdmin->name ?? '-' }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Return Items --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="font-semibold text-slate-900">📦 Produk Retur</h2>
            <p class="mt-1 text-xs text-slate-500">
                Pilih jumlah barang yang ingin dikembalikan ke stok. Jika tidak memilih, semua barang akan dikembalikan.
            </p>

            @if($order->return_status === 'approved')
                <form action="{{ route('admin.returns.restore', $order) }}" method="POST">
                    @csrf
                    <div class="mt-4 space-y-3">
                        @foreach ($order->items as $item)
                            <div class="flex items-center gap-4 border-b border-slate-100 pb-3 last:border-0">
                                <div class="h-14 w-14 rounded-lg bg-slate-100 overflow-hidden shrink-0">
                                    @if ($item->product && $item->product->images->first())
                                        <img src="{{ Storage::url($item->product->images->first()->image) }}"
                                             alt="{{ $item->product_name }}"
                                             class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full items-center justify-center text-2xl text-slate-300">📦</div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-slate-900">{{ $item->product_name }}</p>
                                    @if ($item->variant_name)
                                        <p class="text-xs text-slate-500">Varian: {{ $item->variant_name }}</p>
                                    @endif
                                    <p class="text-xs text-slate-400">Qty: {{ $item->quantity }}</p>
                                </div>
                                <div class="w-32">
                                    <label class="block text-xs text-slate-500 mb-1">Kembalikan Qty</label>
                                    <input type="number" name="items[{{ $item->id }}]"
                                           value="{{ $item->quantity }}"
                                           min="0"
                                           max="{{ $item->quantity }}"
                                           class="w-full rounded-lg border border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div class="w-24 text-right">
                                    <p class="font-semibold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('admin.returns.index') }}"
                           class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Batal
                        </a>
                        <button type="submit"
                                onclick="return confirm('Kembalikan stok untuk pesanan #{{ $order->order_number }}?\n\nBarang akan dikembalikan ke gudang dan status retur akan diselesaikan.')"
                                class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                            📦 Kembalikan ke Stok
                        </button>
                    </div>
                </form>
            @else
                <div class="mt-4 space-y-3">
                    @foreach ($order->items as $item)
                        <div class="flex items-center gap-4 border-b border-slate-100 pb-3 last:border-0">
                            <div class="h-14 w-14 rounded-lg bg-slate-100 overflow-hidden shrink-0">
                                @if ($item->product && $item->product->images->first())
                                    <img src="{{ Storage::url($item->product->images->first()->image) }}"
                                         alt="{{ $item->product_name }}"
                                         class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center text-2xl text-slate-300">📦</div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-slate-900">{{ $item->product_name }}</p>
                                @if ($item->variant_name)
                                    <p class="text-xs text-slate-500">Varian: {{ $item->variant_name }}</p>
                                @endif
                                <p class="text-xs text-slate-400">Qty: {{ $item->quantity }}</p>
                            </div>
                            <div class="w-24 text-right">
                                <p class="font-semibold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">
                    <a href="{{ route('admin.returns.index') }}"
                       class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        ← Kembali ke Daftar Retur
                    </a>
                </div>
            @endif
        </div>

        {{-- Return Action (for pending status) --}}
        @if($order->return_status === 'pending')
            <div class="mt-6 flex justify-end gap-3">
                <form action="{{ route('admin.orders.reject-return', $order) }}" method="POST">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Tolak permintaan retur pesanan #{{ $order->order_number }}?')"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                        ❌ Tolak Retur
                    </button>
                </form>
                <form action="{{ route('admin.orders.approve-return', $order) }}" method="POST">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Setujui permintaan retur pesanan #{{ $order->order_number }}?\n\nStok tidak akan dikembalikan otomatis. Gunakan halaman ini untuk mengembalikan stok setelah barang diterima.')"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        ✅ Setujui Retur
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
