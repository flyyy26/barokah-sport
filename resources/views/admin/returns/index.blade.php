@extends('layouts.admin')

@section('title', 'Manajemen Retur - Admin E-Commerce')
@section('page-title', 'Manajemen Retur')

@section('content')
<div class="ml-64 p-8">
    <div class="mx-auto max-w-7xl">

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
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 9.293a1 1 1 0 000 1.414l3 3 3-3a1 1 0 00-1.414-1.414L10 11.586l-2.293-2.293a1 1 0 00-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-2 text-sm font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Manajemen Retur</h1>
                <p class="text-sm text-slate-500">Daftar permintaan retur dari pelanggan</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4">
                <p class="text-xs text-slate-400">Menunggu Persetujuan</p>
                <p class="mt-1 text-2xl font-bold text-yellow-800">{{ $pendingCount }}</p>
            </div>
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                <p class="text-xs text-slate-400">Disetujui (perlu kembalikan stok)</p>
                <p class="mt-1 text-2xl font-bold text-blue-800">{{ $approvedCount }}</p>
            </div>
        </div>

        {{-- Filter --}}
        <form method="GET" class="mb-4 flex gap-3">
            <select name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
            </select>
        </form>

        {{-- Returns Table --}}
        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
            @if ($orders->count() > 0)
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-medium text-slate-500 uppercase">No. Pesanan</th>
                            <th class="px-4 py-3 text-xs font-medium text-slate-500 uppercase">Pelanggan</th>
                            <th class="px-4 py-3 text-xs font-medium text-slate-500 uppercase">Produk Retur</th>
                            <th class="px-4 py-3 text-xs font-medium text-slate-500 uppercase">Qty Total</th>
                            <th class="px-4 py-3 text-xs font-medium text-slate-500 uppercase">Status Retur</th>
                            <th class="px-4 py-3 text-xs font-medium text-slate-500 uppercase">Alasan</th>
                            <th class="px-4 py-3 text-xs font-medium text-slate-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            @php
                                $totalReturnQty = $order->items->sum('quantity');
                            @endphp
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-900">#{{ $order->order_number }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $order->user->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        @foreach ($order->items as $item)
                                            <div class="flex items-center gap-2">
                                                <span class="text-slate-900">{{ $item->product_name }}</span>
                                                @if ($item->variant_name)
                                                    <span class="text-xs text-slate-500">({{ $item->variant_name }})</span>
                                                @endif
                                                <span class="text-xs text-slate-400">×{{ $item->quantity }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-slate-900">{{ $totalReturnQty }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        @if($order->return_status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->return_status == 'approved') bg-blue-100 text-blue-800
                                        @elseif($order->return_status == 'completed') bg-emerald-100 text-emerald-800 @endif">
                                        {{ $order->return_status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $order->return_reason ? \Illuminate\Support\Str::limit($order->return_reason, 50) : '-' }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $order->return_requested_at?->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.returns.show', $order) }}"
                                       class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-700">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-8 text-center text-slate-500">
                    Tidak ada permintaan retur.
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if ($orders->count() > 0)
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
