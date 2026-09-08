@extends('layouts.admin')

@section('content')

    <div class="ml-64 p-8">
        <div class="mx-auto max-w-7xl">

            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900">📦 Pesanan</h1>
                <p class="mt-1 text-sm text-slate-500">Kelola semua pesanan user.</p>
            </div>

            {{-- Status Cards --}}
            <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-7">
                <div class="rounded-xl border border-slate-200 bg-white p-4 text-center">
                    <p class="text-2xl font-bold text-slate-900">{{ $statusCounts['total'] }}</p>
                    <p class="text-xs text-slate-500">Total</p>
                </div>
                <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-700">{{ $statusCounts['pending'] }}</p>
                    <p class="text-xs text-yellow-600">Menunggu</p>
                </div>
                <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-center">
                    <p class="text-2xl font-bold text-blue-700">{{ $statusCounts['processing'] }}</p>
                    <p class="text-xs text-blue-600">Diproses</p>
                </div>
                <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 text-center">
                    <p class="text-2xl font-bold text-indigo-700">{{ $statusCounts['shipped'] }}</p>
                    <p class="text-xs text-indigo-600">Dikirim</p>
                </div>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-center">
                    <p class="text-2xl font-bold text-emerald-700">{{ $statusCounts['delivered'] }}</p>
                    <p class="text-xs text-emerald-600">Selesai</p>
                </div>
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-center">
                    <p class="text-2xl font-bold text-red-700">{{ $statusCounts['cancelled'] }}</p>
                    <p class="text-xs text-red-600">Dibatalkan</p>
                </div>
                <div class="rounded-xl border border-orange-200 bg-orange-50 p-4 text-center">
                    <p class="text-2xl font-bold text-orange-700">{{ $statusCounts['unpaid'] }}</p>
                    <p class="text-xs text-orange-600">Belum Bayar</p>
                </div>
            </div>

            {{-- Filter & Search --}}
            <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.orders.index') }}" 
                       class="rounded-lg px-3 py-1.5 text-sm font-medium {{ !request('shipping_status') ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Semua
                    </a>
                    <a href="{{ route('admin.orders.index', ['shipping_status' => 'pending']) }}" 
                       class="rounded-lg px-3 py-1.5 text-sm font-medium {{ request('shipping_status') == 'pending' ? 'bg-yellow-500 text-white' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">
                        Menunggu
                    </a>
                    <a href="{{ route('admin.orders.index', ['shipping_status' => 'processing']) }}" 
                       class="rounded-lg px-3 py-1.5 text-sm font-medium {{ request('shipping_status') == 'processing' ? 'bg-blue-500 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                        Diproses
                    </a>
                    <a href="{{ route('admin.orders.index', ['shipping_status' => 'shipped']) }}" 
                       class="rounded-lg px-3 py-1.5 text-sm font-medium {{ request('shipping_status') == 'shipped' ? 'bg-indigo-500 text-white' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' }}">
                        Dikirim
                    </a>
                    <a href="{{ route('admin.orders.index', ['shipping_status' => 'delivered']) }}" 
                       class="rounded-lg px-3 py-1.5 text-sm font-medium {{ request('shipping_status') == 'delivered' ? 'bg-emerald-500 text-white' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}">
                        Selesai
                    </a>
                <a href="{{ route('admin.orders.index', ['shipping_status' => 'cancelled']) }}" 
                   class="rounded-lg px-3 py-1.5 text-sm font-medium {{ request('shipping_status') == 'cancelled' ? 'bg-red-500 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                    Dibatalkan
                </a>
            </div>

            {{-- Bulk Actions (only visible in processing tab) --}}
            @if(request('shipping_status') === 'processing')
            <div id="bulk-actions-bar" class="hidden mb-4 rounded-xl border border-slate-200 bg-white p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="select-all" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-slate-600">
                            <span id="selected-count">0</span> pesanan dipilih
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" id="btn-bulk-ship"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50" disabled>
                            Kirim Produk
                        </button>
                        <button type="button" id="btn-bulk-print"
                            class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50" disabled>
                            Print Label
                        </button>
                    </div>
                </div>
            </div>
            @endif

                <form method="GET" class="flex gap-2">
                    <input type="text" name="search" placeholder="Cari order atau user..." 
                           value="{{ request('search') }}"
                           class="rounded-lg border border-slate-200 px-4 py-2 text-sm outline-none focus:border-blue-500">
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        Cari
                    </button>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">
                                @if(request('shipping_status') === 'processing')
                                <input type="checkbox" id="select-all-header" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                @endif
                            </th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">No. Order</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">User</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Total</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Pembayaran</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Tanggal</th>
                            <th class="px-4 py-3 text-center font-medium text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($orders as $order)
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                @if(request('shipping_status') === 'processing')
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="selected_orders[]" value="{{ $order->id }}"
                                           class="order-checkbox h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                @else
                                <td class="px-4 py-3"></td>
                                @endif
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $order->order_number }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-900">{{ $order->user->name ?? 'Guest' }}</p>
                                    <p class="text-xs text-slate-400">{{ $order->user->email ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-900">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        @if($order->shipping_status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->shipping_status == 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->shipping_status == 'shipped') bg-indigo-100 text-indigo-800
                                        @elseif($order->shipping_status == 'delivered') bg-emerald-100 text-emerald-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $order->shipping_status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        @if($order->payment_status == 'paid') bg-emerald-100 text-emerald-800
                                        @elseif($order->payment_status == 'unpaid') bg-orange-100 text-orange-800
                                        @elseif($order->payment_status == 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $order->payment_status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500">
                                    {{ $order->created_at->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('admin.orders.show', $order) }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                    Belum ada pesanan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $orders->links() }}
            </div>

        </div>
    </div>

    @if(request('shipping_status') === 'processing')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const isProcessingTab = {{ request('shipping_status') === 'processing' ? 'true' : 'false' }};
                    if (!isProcessingTab) return;

                    const selectAllHeader = document.getElementById('select-all-header');
                    const selectAll = document.getElementById('select-all');
                    const bulkActionsBar = document.getElementById('bulk-actions-bar');
            const btnBulkShip = document.getElementById('btn-bulk-ship');
            const btnBulkPrint = document.getElementById('btn-bulk-print');
            const selectedCountEl = document.getElementById('selected-count');
            const checkboxes = document.querySelectorAll('.order-checkbox');

            function updateSelectedCount() {
                const count = document.querySelectorAll('.order-checkbox:checked').length;
                selectedCountEl.textContent = count;
                bulkActionsBar.classList.toggle('hidden', count === 0);
                btnBulkShip.disabled = count === 0;
                btnBulkPrint.disabled = count === 0;
            }

            if (selectAllHeader) {
                selectAllHeader.addEventListener('change', function () {
                    if (selectAll) selectAll.checked = this.checked;
                    checkboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                    updateSelectedCount();
                });
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(cb => {
                        if (!cb.disabled) {
                            cb.checked = this.checked;
                        }
                    });
                    updateSelectedCount();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateSelectedCount);
            });

            function getSelectedIds() {
                return Array.from(document.querySelectorAll('.order-checkbox:checked'))
                    .map(cb => cb.value);
            }

            btnBulkShip.addEventListener('click', function () {
                const ids = getSelectedIds();
                if (ids.length === 0) return;
                if (!confirm('Kirim ' + ids.length + ' pesanan ini? Pastikan data pengiriman (kurir, layanan) sudah diisi di masing-masing pesanan.')) return;

                const formData = new FormData();
                ids.forEach(id => formData.append('order_ids[]', id));

                fetch('{{ route('admin.orders.bulk-ship') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Gagal: ' + data.message);
                    }
                })
                .catch(err => {
                    alert('Error: ' + err.message);
                });
            });

            btnBulkPrint.addEventListener('click', function () {
                const ids = getSelectedIds();
                if (ids.length === 0) return;

                const formData = new FormData();
                ids.forEach(id => formData.append('order_ids[]', id));

                fetch('{{ route('admin.orders.bulk-print-label') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        if (data.labels && data.labels.length === 1) {
                            window.open(data.labels[0].pdf_url, '_blank');
                        } else if (data.labels && data.labels.length > 1) {
                            let html = '<div style="padding:20px;"><h3>Labels untuk ' + data.labels.length + ' pesanan</h3><div style="display:flex;flex-wrap:gap:10px;">';
                            data.labels.forEach(function(label) {
                                html += '<div style="margin-bottom:15px;"><strong>' + label.order_number + '</strong><br><a href="' + label.pdf_url + '" target="_blank">Buka Label</a></div>';
                            });
                            if (data.errors && data.errors.length > 0) {
                                html += '<p style="color:red;">' + data.errors.join('<br>') + '</p>';
                            }
                            html += '</div></div>';
                            const printWindow = window.open('', '_blank');
                            printWindow.document.write(html);
                            printWindow.document.close();
                        }
                        // Uncheck all after printing
                        checkboxes.forEach(cb => cb.checked = false);
                        selectAll.checked = false;
                        updateSelectedCount();
                    } else {
                        alert('Gagal: ' + (data.errors ? data.errors.join(', ') : data.message));
                    }
                })
                .catch(err => {
                    alert('Error: ' + err.message);
                });
            });

            // Initial state
            updateSelectedCount();
        });
    </script>
    @endif

@endsection