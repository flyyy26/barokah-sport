@if (empty($products) || $products->isEmpty())
    <div class="flex h-full flex-col items-center justify-center text-slate-400">
        <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        <p class="mt-4 font-medium text-slate-600">Wishlist kosong</p>
        <p class="text-sm text-slate-400">Simpan produk favoritmu di sini!</p>
        <a href="{{ route('customer.products.index') }}" class="mt-4 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
            Mulai Belanja
        </a>
    </div>
@else
    <div class="space-y-4">
        @foreach ($products as $product)
            <div class="wishlist-item flex items-center gap-3 border-b border-slate-100 pb-3 last:border-0" data-product-id="{{ $product->id }}">
                {{-- Image --}}
                <div class="h-16 w-16 rounded-xl bg-slate-100 overflow-hidden shrink-0">
                    @if ($product->images->first())
                        <img src="{{ Storage::url($product->images->first()->image) }}" 
                             alt="{{ $product->name }}" 
                             class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full items-center justify-center text-2xl text-slate-300">📦</div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <a href="{{ route('customer.products.show', $product) }}" class="text-sm font-semibold text-slate-900 hover:text-blue-600 line-clamp-1" target="_blank">
                        {{ $product->name }}
                    </a>
                    @if ($product->category)
                        <p class="text-xs text-slate-400">{{ $product->category->name }}</p>
                    @endif
                    <p class="text-sm font-bold text-slate-900">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col gap-1">
                    @if ($product->stock > 0)
                        <button class="add-to-cart-wishlist px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition"
                                data-product-id="{{ $product->id }}"
                                data-variant-id="{{ $product->variants->first()?->id }}">
                            🛒
                        </button>
                    @endif
                    <button class="remove-wishlist px-2 py-1 text-xs font-medium text-red-500 hover:text-red-700 transition"
                            data-product-id="{{ $product->id }}">
                        ✕
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif