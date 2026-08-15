@if (empty($cart))
    <div class="flex h-full flex-col items-center justify-center text-slate-400">
        <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
        <p class="mt-4 font-medium text-slate-600">Keranjang kosong</p>
        <p class="text-sm text-slate-400">Yuk, mulai belanja!</p>
    </div>
@else
    <div class="space-y-3">
        @foreach ($cart as $key => $item)
            <div class="cart-item flex items-center gap-3 border-b border-slate-100 pb-3 last:border-0" data-key="{{ $key }}">
                {{-- Image --}}
                <div class="h-16 w-16 rounded-xl bg-slate-100 overflow-hidden shrink-0">
                    @if ($item['image'] && Storage::disk('public')->exists($item['image']))
                        <img src="{{ Storage::url($item['image']) }}" 
                             alt="{{ $item['product_name'] }}" 
                             class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full items-center justify-center text-2xl text-slate-300">📦</div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 line-clamp-1">{{ $item['product_name'] }}</p>
                    @if ($item['variant_name'])
                        <p class="text-xs text-slate-500">{{ $item['variant_name'] }}</p>
                    @endif
                    <p class="text-sm font-bold text-slate-900">
                        Rp {{ number_format($item['price'], 0, ',', '.') }}
                    </p>
                </div>

                {{-- Quantity & Remove --}}
                <div class="flex items-center gap-2">
                    <div class="flex items-center border border-slate-200 rounded-lg">
                        <button class="qty-btn px-2 py-1 text-slate-600 hover:bg-slate-100 rounded-l-lg" data-action="decrease">−</button>
                        <input type="number" class="qty-input w-10 text-center border-0 py-1 text-xs focus:ring-0" 
                               value="{{ $item['quantity'] }}" min="1" data-key="{{ $key }}">
                        <button class="qty-btn px-2 py-1 text-slate-600 hover:bg-slate-100 rounded-r-lg" data-action="increase">+</button>
                    </div>
                    <button class="remove-item text-red-400 hover:text-red-600 p-1" data-key="{{ $key }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif