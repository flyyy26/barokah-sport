<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">

    @include('customer.partials.navbar')

    <main class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('customer.orders') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    ← Kembali ke Riwayat Pesanan
                </a>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">
                    Detail Pesanan
                </h1>
                <p class="text-sm text-slate-500">#{{ $order->order_number }}</p>
            </div>
            <div>
                <span class="rounded-full px-3 py-1 text-sm font-medium
                    @if($order->status == 'delivered' || $order->status == 'completed') bg-emerald-100 text-emerald-700
                    @elseif($order->status == 'cancelled') bg-red-100 text-red-700
                    @elseif($order->status == 'shipped') bg-blue-100 text-blue-700
                    @elseif($order->status == 'processing') bg-indigo-100 text-indigo-700
                    @else bg-yellow-100 text-yellow-700 @endif">
                    {{ ucfirst($order->status ?? 'Pending') }}
                </span>
                <span class="ml-2 rounded-full px-3 py-1 text-sm font-medium
                    @if($order->payment_status == 'paid') bg-emerald-100 text-emerald-700
                    @elseif($order->payment_status == 'unpaid') bg-orange-100 text-orange-700
                    @else bg-red-100 text-red-700 @endif">
                    {{ $order->payment_status == 'paid' ? '✅ Lunas' : '⏳ Belum Bayar' }}
                </span>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- MAIN CONTENT --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- ORDER ITEMS --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="font-semibold text-slate-900">🛍️ Item Pesanan</h2>
                    <div class="mt-4 space-y-4">
                        @foreach ($order->items as $item)
                            <div class="flex items-center justify-between border-b border-slate-100 pb-4 last:border-0">
                                <div class="flex items-center gap-4">
                                    <div class="h-16 w-16 rounded-lg bg-slate-100 overflow-hidden shrink-0">
                                        @if ($item->product && $item->product->images->first())
                                            <img src="{{ Storage::url($item->product->images->first()->image) }}" 
                                                 alt="{{ $item->product_name }}" 
                                                 class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full items-center justify-center text-2xl text-slate-300">📦</div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $item->product_name }}</p>
                                        @if ($item->variant_name)
                                            <p class="text-xs text-slate-500">Varian: {{ $item->variant_name }}</p>
                                        @endif
                                        <p class="text-xs text-slate-400">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <p class="font-semibold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- TOTAL --}}
                    <div class="mt-4 border-t border-slate-200 pt-4 space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Subtotal</span>
                            <span class="text-slate-700">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Ongkir</span>
                            <span class="text-slate-700">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        @if ($order->discount > 0)
                            <div class="flex justify-between">
                                <span class="text-slate-500">Diskon</span>
                                <span class="text-red-500">-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between border-t border-slate-200 pt-2 text-base font-bold">
                            <span>Total</span>
                            <span class="text-slate-900">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- CATATAN --}}
                @if ($order->notes)
                    <div class="rounded-2xl border border-slate-200 bg-white p-6">
                        <h2 class="font-semibold text-slate-900">📝 Catatan</h2>
                        <p class="mt-2 text-sm text-slate-600">{{ $order->notes }}</p>
                    </div>
                @endif

                {{-- STATUS TIMELINE --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="font-semibold text-slate-900">📊 Timeline Pesanan</h2>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="h-3 w-3 rounded-full bg-emerald-500"></div>
                            <div>
                                <p class="text-sm font-medium text-slate-900">Pesanan Dibuat</p>
                                <p class="text-xs text-slate-400">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @if ($order->paid_at)
                            <div class="flex items-center gap-3">
                                <div class="h-3 w-3 rounded-full bg-emerald-500"></div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">Pembayaran Dikonfirmasi</p>
                                    <p class="text-xs text-slate-400">{{ $order->paid_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        @endif
                        @if ($order->shipped_at)
                            <div class="flex items-center gap-3">
                                <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">Pesanan Dikirim</p>
                                    <p class="text-xs text-slate-400">{{ $order->shipped_at->format('d M Y, H:i') }}</p>
                                    @if ($order->courier && $order->tracking_number)
                                        <p class="text-xs text-slate-500">Kurir: {{ strtoupper($order->courier) }} - Resi: {{ $order->tracking_number }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if ($order->delivered_at)
                            <div class="flex items-center gap-3">
                                <div class="h-3 w-3 rounded-full bg-emerald-500"></div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">Pesanan Selesai</p>
                                    <p class="text-xs text-slate-400">{{ $order->delivered_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        @endif
                        @if ($order->cancelled_at)
                            <div class="flex items-center gap-3">
                                <div class="h-3 w-3 rounded-full bg-red-500"></div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">Pesanan Dibatalkan</p>
                                    <p class="text-xs text-slate-400">{{ $order->cancelled_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- SIDEBAR --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- ALAMAT PENGIRIMAN --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="font-semibold text-slate-900">📍 Alamat Pengiriman</h2>
                    <div class="mt-3 text-sm text-slate-600">
                        <p class="font-medium text-slate-900">{{ $order->shipping_name }}</p>
                        <p>{{ $order->shipping_phone }}</p>
                        <p class="mt-2">{{ $order->shipping_address }}</p>
                        <p>{{ $order->shipping_district ?? '' }}, {{ $order->shipping_city }}</p>
                        <p>{{ $order->shipping_province }}</p>
                        @if ($order->shipping_postal_code)
                            <p>Kode Pos: {{ $order->shipping_postal_code }}</p>
                        @endif
                    </div>
                </div>

                {{-- INFORMASI PENGIRIMAN --}}
                @if ($order->courier)
                    <div class="rounded-2xl border border-slate-200 bg-white p-6">
                        <h2 class="font-semibold text-slate-900">🚚 Informasi Pengiriman</h2>
                        <div class="mt-3 text-sm text-slate-600">
                            <p><span class="text-slate-500">Kurir:</span> {{ strtoupper($order->courier) }}</p>
                            @if ($order->service)
                                <p><span class="text-slate-500">Layanan:</span> {{ $order->service }}</p>
                            @endif
                            @if ($order->tracking_number)
                                <p><span class="text-slate-500">No. Resi:</span> <strong class="text-slate-900">{{ $order->tracking_number }}</strong></p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- INFORMASI PEMBAYARAN --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="font-semibold text-slate-900">💳 Informasi Pembayaran</h2>
                    <div class="mt-3 text-sm text-slate-600">
                        <p><span class="text-slate-500">Status:</span> 
                            <span class="font-medium
                                @if($order->payment_status == 'paid') text-emerald-600
                                @elseif($order->payment_status == 'unpaid') text-orange-600
                                @else text-red-600 @endif">
                                {{ $order->payment_status == 'paid' ? '✅ Lunas' : '⏳ Belum Bayar' }}
                            </span>
                        </p>
                        @if ($order->paid_at)
                            <p><span class="text-slate-500">Dibayar:</span> {{ $order->paid_at->format('d M Y, H:i') }}</p>
                        @endif
                        <p class="mt-2 text-xs text-slate-400">
                            @if ($order->payment_status == 'unpaid')
                                Silakan lakukan pembayaran dan konfirmasi ke admin.
                            @elseif ($order->payment_status == 'paid')
                                Pembayaran sudah dikonfirmasi.
                            @endif
                        </p>
                    </div>
                </div>

            </div>

        </div>

    </main>

    @include('customer.partials.footer')

</body>

</html>