<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - {{ config('app.name') }}</title>
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

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="mb-8">
            <p class="text-sm font-medium text-blue-600">Akun Saya</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                Riwayat Pesanan
            </h1>
            <p class="mt-2 text-sm text-slate-500">Lihat semua pesanan yang pernah kamu buat.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-4">

            {{-- SIDEBAR NAV --}}
            <div class="lg:col-span-1 space-y-2">
                <a href="{{ route('customer.account') }}" class="block rounded-xl px-4 py-3 text-sm text-slate-600 transition hover:bg-slate-100">
                    📋 Profil Saya
                </a>
                <a href="{{ route('customer.orders') }}" class="block rounded-xl bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-600">
                    📦 Riwayat Pesanan
                </a>
                <a href="{{ route('customer.addresses.create') }}" class="block rounded-xl px-4 py-3 text-sm text-slate-600 transition hover:bg-slate-100">
                    ➕ Tambah Alamat
                </a>
            </div>

            {{-- MAIN CONTENT --}}
            <div class="lg:col-span-3">

                @if (session('success'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($orders->isEmpty())
                    {{-- EMPTY STATE --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center">
                        <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-slate-100 text-4xl">
                            📦
                        </div>
                        <h3 class="mt-4 text-lg font-semibold text-slate-900">Belum Ada Pesanan</h3>
                        <p class="mt-2 text-sm text-slate-500">
                            Kamu belum melakukan pemesanan apapun. Yuk, mulai belanja sekarang!
                        </p>
                        <a href="{{ route('customer.products.index') }}" 
                           class="mt-6 inline-block rounded-xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Mulai Belanja
                        </a>
                    </div>
                @else
                    {{-- LIST ORDERS --}}
                    <div class="space-y-4">
                        @foreach ($orders as $order)
                            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div>
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm font-semibold text-slate-900">
                                                #{{ $order->order_number }}
                                            </span>
                                            {{-- 🔥 PERBAIKAN: Status yang benar --}}
                                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium
                                                @if($order->status == 'delivered' || $order->status == 'completed') bg-emerald-100 text-emerald-700
                                                @elseif($order->status == 'cancelled') bg-red-100 text-red-700
                                                @elseif($order->status == 'shipped') bg-blue-100 text-blue-700
                                                @elseif($order->status == 'processing') bg-indigo-100 text-indigo-700
                                                @else bg-yellow-100 text-yellow-700 @endif">
                                                {{ ucfirst($order->status ?? 'Pending') }}
                                            </span>
                                            {{-- 🔥 TAMBAHKAN: Status Pembayaran --}}
                                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium
                                                @if($order->payment_status == 'paid') bg-emerald-100 text-emerald-700
                                                @elseif($order->payment_status == 'unpaid') bg-orange-100 text-orange-700
                                                @else bg-red-100 text-red-700 @endif">
                                                {{ $order->payment_status == 'paid' ? '✅ Lunas' : '⏳ Belum Bayar' }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $order->created_at->format('d M Y, H:i') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-slate-900">
                                            Rp {{ number_format($order->total ?? 0, 0, ',', '.') }}
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            {{ $order->items->count() ?? 0 }} produk
                                        </p>
                                    </div>
                                </div>

                                {{-- ORDER ITEMS PREVIEW --}}
                                <div class="mt-4 border-t border-slate-100 pt-4">
                                    <div class="flex items-center gap-4 overflow-x-auto pb-2">
                                        @foreach ($order->items->take(3) as $item)
                                            <div class="flex shrink-0 items-center gap-3">
                                                <div class="h-12 w-12 rounded-lg bg-slate-100 overflow-hidden">
                                                    @if ($item->product && $item->product->images->first())
                                                        <img src="{{ Storage::url($item->product->images->first()->image) }}" 
                                                             alt="{{ $item->product_name }}" 
                                                             class="h-full w-full object-cover">
                                                    @else
                                                        <div class="flex h-full items-center justify-center text-xl text-slate-300">📦</div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-slate-700 line-clamp-1">{{ $item->product_name }}</p>
                                                    <p class="text-xs text-slate-400">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if ($order->items->count() > 3)
                                            <span class="shrink-0 text-sm font-medium text-slate-400">
                                                +{{ $order->items->count() - 3 }} lainnya
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- ACTION --}}
                                <div class="mt-4 flex gap-3">
                                    <a href="{{ route('customer.orders.show', $order) }}" 
                                       class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                                        Lihat Detail
                                    </a>
                                    @if (($order->status ?? 'pending') == 'pending')
                                        <button class="rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50">
                                            Batalkan
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- PAGINATION --}}
                    <div class="mt-6">
                        {{ $orders->links() }}
                    </div>
                @endif

            </div>

        </div>

    </main>

    @include('customer.partials.footer')

</body>

</html>