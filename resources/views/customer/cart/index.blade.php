<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Keranjang Belanja - {{ config('app.name') }}</title>
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

<body class="bg-slate-50 text-slate-900">

    @include('customer.partials.navbar')

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">🛒 Keranjang Belanja</h1>
            <p class="mt-1 text-sm text-slate-500">Tinjau dan kelola item di keranjang belanja Anda.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if (empty($cart))
            {{-- Empty Cart --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-slate-100 text-5xl">
                    🛒
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Keranjang Kosong</h3>
                <p class="mt-2 text-sm text-slate-500">Belum ada produk di keranjang. Yuk, mulai belanja!</p>
                <a href="{{ route('customer.products.index') }}" 
                   class="mt-6 inline-block rounded-xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Mulai Belanja
                </a>
            </div>
        @else
            <div class="grid gap-6 lg:grid-cols-3">

                {{-- Cart Items --}}
                <div class="lg:col-span-2">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6">
                        <div class="space-y-4">
                            @php $subtotal = 0; @endphp
                            @foreach ($cart as $key => $item)
                                @php $subtotal += $item['price'] * $item['quantity']; @endphp
                                <div class="cart-item flex items-center justify-between border-b border-slate-100 pb-4 last:border-0" data-key="{{ $key }}">
                                    <div class="flex items-center gap-4">
                                        {{-- Image --}}
                                        <div class="h-20 w-20 rounded-xl bg-slate-100 overflow-hidden shrink-0">
                                            @if ($item['image'])
                                                <img src="{{ Storage::url($item['image']) }}" 
                                                     alt="{{ $item['product_name'] }}" 
                                                     class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full items-center justify-center text-3xl text-slate-300">📦</div>
                                            @endif
                                        </div>

                                        {{-- Info --}}
                                        <div>
                                            <a href="{{ route('customer.products.show', $item['slug']) }}" 
                                               class="font-semibold text-slate-900 hover:text-blue-600 line-clamp-1">
                                                {{ $item['product_name'] }}
                                            </a>
                                            @if ($item['variant_name'])
                                                <p class="text-xs text-slate-500">Varian: {{ $item['variant_name'] }}</p>
                                            @endif
                                            <p class="text-sm font-bold text-slate-900">
                                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex items-center gap-3">
                                        {{-- Quantity --}}
                                        <div class="flex items-center border border-slate-200 rounded-lg">
                                            <button class="qty-btn px-3 py-1.5 text-slate-600 hover:bg-slate-100" data-action="decrease">−</button>
                                            <input type="number" class="qty-input w-12 text-center border-0 py-1.5 text-sm focus:ring-0" 
                                                   value="{{ $item['quantity'] }}" min="1" data-key="{{ $key }}">
                                            <button class="qty-btn px-3 py-1.5 text-slate-600 hover:bg-slate-100" data-action="increase">+</button>
                                        </div>

                                        {{-- Remove --}}
                                        <button class="remove-item text-red-500 hover:text-red-700 p-1" data-key="{{ $key }}">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>

                                        {{-- Subtotal item --}}
                                        <p class="text-sm font-semibold text-slate-900 min-w-[80px] text-right">
                                            Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-4 flex justify-between">
                        <a href="{{ route('customer.products.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                            ← Lanjut Belanja
                        </a>
                        <form action="{{ route('customer.cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan keranjang?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-500 hover:text-red-700">
                                Kosongkan Keranjang
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="lg:col-span-1">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 sticky top-24">
                        <h2 class="text-lg font-bold text-slate-900">Ringkasan Belanja</h2>

                        <div class="mt-4 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Subtotal</span>
                                <span class="font-medium text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Ongkir</span>
                                <span class="font-medium text-slate-900">Dihitung di checkout</span>
                            </div>
                            <div class="border-t border-slate-200 pt-2 flex justify-between font-bold text-base">
                                <span>Total</span>
                                <span class="text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <a href="{{ route('customer.checkout.index') }}" 
                        class="mt-6 block w-full rounded-xl bg-slate-900 py-3.5 text-center font-semibold text-white transition hover:bg-slate-800">
                            Checkout →
                        </a>
                    </div>
                </div>

            </div>
        @endif

    </main>

    @include('customer.partials.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            // ============================================
            // UPDATE QUANTITY
            // ============================================

            document.querySelectorAll('.qty-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.closest('.flex').querySelector('.qty-input');
                    let value = parseInt(input.value);
                    const action = this.dataset.action;

                    if (action === 'increase') {
                        value += 1;
                    } else if (action === 'decrease' && value > 1) {
                        value -= 1;
                    }

                    if (value < 1) return;

                    input.value = value;
                    updateCart(input.dataset.key, value);
                });
            });

            document.querySelectorAll('.qty-input').forEach(input => {
                input.addEventListener('change', function() {
                    let value = parseInt(this.value);
                    if (isNaN(value) || value < 1) {
                        value = 1;
                        this.value = 1;
                    }
                    updateCart(this.dataset.key, value);
                });
            });

            function updateCart(key, quantity) {
                fetch('{{ route("customer.cart.update") }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ key, quantity })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message);
                        window.location.reload();
                    }
                })
                .catch(() => {
                    window.location.reload();
                });
            }

            // ============================================
            // REMOVE ITEM
            // ============================================

            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    if (!confirm('Hapus item ini dari keranjang?')) return;

                    const key = this.dataset.key;

                    fetch('{{ route("customer.cart.remove") }}', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ key })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    });
                });
            });
        });
    </script>

</body>
</html>