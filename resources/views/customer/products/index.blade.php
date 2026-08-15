@extends('layouts.customer')

@section('title', 'Barokah Sport')

@section('content')

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Katalog Produk</h1>
            <p class="mt-1 text-sm text-slate-500">Temukan produk terbaik dari {{ config('app.name') }}</p>
        </div>

        <div class="grid gap-8 lg:grid-cols-4">

            {{-- SIDEBAR FILTER --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24 rounded-2xl border border-slate-200 bg-white p-4">
                    <h3 class="font-semibold text-slate-900">Filter</h3>

                    <form method="GET" action="{{ route('customer.products.index') }}" class="mt-4 space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Kategori</label>
                            <select name="category" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Rentang Harga</label>
                            <div class="flex gap-2">
                                <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}" 
                                       class="w-1/2 rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}" 
                                       class="w-1/2 rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                            </div>
                        </div>

                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                        <button type="submit" class="w-full rounded-lg bg-slate-900 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Terapkan Filter
                        </button>

                        <a href="{{ route('customer.products.index') }}" class="block text-center text-sm text-blue-600 hover:text-blue-700">
                            Reset Filter
                        </a>
                    </form>
                </div>
            </div>

            {{-- PRODUCT LIST --}}
            <div class="lg:col-span-3">

                <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
                    <p class="text-sm text-slate-500">Menampilkan {{ $products->total() }} produk</p>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-slate-500">Urutkan:</span>
                        <select onchange="window.location.href=this.value" class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm outline-none focus:border-blue-500">
                            <option value="{{ route('customer.products.index', array_merge(request()->query(), ['sort' => 'newest'])) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>
                                Terbaru
                            </option>
                            <option value="{{ route('customer.products.index', array_merge(request()->query(), ['sort' => 'price_asc'])) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                Harga: Rendah → Tinggi
                            </option>
                            <option value="{{ route('customer.products.index', array_merge(request()->query(), ['sort' => 'price_desc'])) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                                Harga: Tinggi → Rendah
                            </option>
                            <option value="{{ route('customer.products.index', array_merge(request()->query(), ['sort' => 'name'])) }}" {{ request('sort') == 'name' ? 'selected' : '' }}>
                                Nama (A-Z)
                            </option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($products as $product)
                        <div class="group rounded-2xl border border-slate-200 bg-white p-3 transition hover:shadow-lg">
                            <a href="{{ route('customer.products.show', $product) }}" class="block">
                                <div class="aspect-square overflow-hidden rounded-xl bg-slate-100">
                                    @if ($product->images->first())
                                        <img src="{{ Storage::url($product->images->first()->image) }}" 
                                             alt="{{ $product->name }}" 
                                             class="h-full w-full object-cover transition group-hover:scale-105">
                                    @else
                                        <div class="flex h-full items-center justify-center text-4xl text-slate-300">📦</div>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <p class="text-xs text-slate-400">{{ $product->category->name ?? 'Tanpa Kategori' }}</p>
                                    <h3 class="text-sm font-semibold text-slate-900 line-clamp-1">{{ $product->name }}</h3>
                                    <div class="mt-1 flex items-center justify-between">
                                        <span class="font-bold text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                        @if ($product->stock > 0)
                                            <span class="text-xs text-emerald-600">Tersedia</span>
                                        @else
                                            <span class="text-xs text-red-500">Habis</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <p class="col-span-full py-12 text-center text-slate-500">Belum ada produk tersedia.</p>
                    @endforelse
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>

            </div>

        </div>

    </div>

    @endsection