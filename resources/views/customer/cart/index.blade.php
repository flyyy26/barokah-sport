@extends('layouts.customer')

@section('title', 'Keranjang - Barokah Sport')

@section('content')
    
    <style>
        .cart-container {
            width:100%;
            margin: 0 auto;
            border-top:.1vw solid #076694;
        }

        .cart-header h1 {
            font-size: 2.3vw;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            font-family:heading;
            text-transform:uppercase;
        }

        .cart-header p {
            font-size: 0.85vw;
            color: #94a3b8;
            margin-top: 0.2vw;
        }

        /* ============================================
        FILTER ROW - SELECT BERJAJAR
        ============================================ */
        .katalog_top_container{
            width:100%;
            padding: 1.3vw 7.54vw;
            padding-bottom:1.8vw;
            background: #f9fafb;
        }
        /* ============================================
           ALERT
           ============================================ */
        .cart-alert {
            padding: 1vw 1.5vw;
            border-radius: 0.8vw;
            margin-bottom: 1.5vw;
            font-size: 0.85vw;
        }

        .cart-alert.success {
            background: #f0fdf4;
            border: 0.1vw solid #86efac;
            color: #166534;
        }

        .cart-alert.error {
            background: #fef2f2;
            border: 0.1vw solid #fca5a5;
            color: #991b1b;
        }

        /* ============================================
           EMPTY CART
           ============================================ */
        .cart-empty {
            background: #ffffff;
            border: 0.1vw solid #e2e8f0;
            border-radius: 1.2vw;
            padding: 4vw 2vw;
            text-align: center;
        }

        .cart-empty .empty-icon {
            width: 6vw;
            height: 6vw;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            border-radius: 50%;
            font-size: 3vw;
        }

        .cart-empty h3 {
            font-size: 1.3vw;
            font-weight: 600;
            color: #0f172a;
            margin-top: 1vw;
        }

        .cart-empty p {
            font-size: 0.85vw;
            color: #94a3b8;
            margin-top: 0.4vw;
        }

        .cart-empty .btn-shop {
            display: inline-block;
            margin-top: 1.5vw;
            padding: 0.7vw 2vw;
            background: #0f172a;
            color: #ffffff;
            font-size: 0.85vw;
            font-weight: 600;
            border-radius: 0.7vw;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .cart-empty .btn-shop:hover {
            background: #1e293b;
            transform: translateY(-0.1vw);
        }

        /* ============================================
           CART GRID
           ============================================ */
        .cart-grid {
            display: grid;
            grid-template-columns: 70% 30%;
            padding:2.8vw 7.3vw;
        }

        /* ============================================
           CART ITEMS
           ============================================ */
        .cart-items-wrapper {
            background: #ffffff;
            border: 0.1vw solid #e2e8f0;
            border-radius: 1.2vw;
            padding: 1.5vw;
            margin-right:2vw;
        }

        .cart-items {
            display: flex;
            flex-direction: column;
            gap: 1vw;
        }

        /* ============================================
           CART ITEM
           ============================================ */
        .cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 1vw;
            border-bottom: 0.1vw solid #f1f5f9;
        }

        .cart-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .cart-item-left {
            display: flex;
            align-items: center;
            gap: 1vw;
            flex: 1;
            min-width: 0;
        }

        .cart-item-image {
            width: 5vw;
            height: 5vw;
            min-width: 5vw;
            background: #f1f5f9;
            border-radius: 0.7vw;
            overflow: hidden;
            flex-shrink: 0;
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-image .placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            font-size: 2vw;
            color: #cbd5e1;
        }

        .cart-item-info {
            flex: 1;
            min-width: 0;
        }

        .cart-item-info .item-name {
            display: block;
            font-size: 0.9vw;
            font-weight: 600;
            color: #0f172a;
            text-decoration: none;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: color 0.2s;
        }

        .cart-item-info .item-name:hover {
            color: #076694;
        }

        .cart-item-info .item-variant {
            font-size: 0.7vw;
            color: #94a3b8;
            margin-top: 0.1vw;
        }

        .cart-item-info .item-price {
            font-size: 0.85vw;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0.1vw;
        }

        /* ============================================
           CART ITEM ACTIONS
           ============================================ */
        .cart-item-actions {
            display: flex;
            align-items: center;
            gap: 0.8vw;
            flex-shrink: 0;
        }

        .cart-item-actions .qty-wrapper {
            display: flex;
            align-items: center;
            border: 0.1vw solid #e2e8f0;
            border-radius: 0.5vw;
            overflow: hidden;
        }

        .cart-item-actions .qty-btn {
            width: 2vw;
            height: 2vw;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1vw;
            font-weight: 500;
            color: #475569;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: background 0.15s;
            padding: 0;
        }

        .cart-item-actions .qty-btn:hover {
            background: #f1f5f9;
        }

        .cart-item-actions .qty-input {
            width: 2.8vw;
            height: 2vw;
            text-align: center;
            font-size: 0.8vw;
            font-weight: 600;
            color: #0f172a;
            background: transparent;
            border: none;
            border-left: 0.1vw solid #e2e8f0;
            border-right: 0.1vw solid #e2e8f0;
            padding: 0;
            outline: none;
            -moz-appearance: textfield;
        }

        .cart-item-actions .qty-input::-webkit-outer-spin-button,
        .cart-item-actions .qty-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .cart-item-actions .btn-remove {
            width: 2vw;
            height: 2vw;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            background: transparent;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s;
            padding: 0;
        }

        .cart-item-actions .btn-remove:hover {
            background: #fef2f2;
            color: #ef4444;
            transform: scale(1.1);
        }

        .cart-item-actions .btn-remove svg {
            width: 1.2vw;
            height: 1.2vw;
        }

        .cart-item-actions .item-subtotal {
            font-size: 0.85vw;
            font-weight: 700;
            color: #0f172a;
            min-width: 5vw;
            text-align: right;
        }

        /* ============================================
           CART BOTTOM ACTIONS
           ============================================ */
        .cart-bottom-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 1vw;
        }

        .cart-bottom-actions .btn-continue {
            font-size: 0.8vw;
            color: #076694;
            text-decoration: none;
            transition: color 0.2s;
        }

        .cart-bottom-actions .btn-continue:hover {
            color: #076694;
        }

        .cart-bottom-actions .btn-clear {
            font-size: 0.8vw;
            color: #ef4444;
            background: none;
            border: none;
            cursor: pointer;
            transition: color 0.2s;
        }

        .cart-bottom-actions .btn-clear:hover {
            color: #dc2626;
        }

        /* ============================================
           SUMMARY
           ============================================ */
        .cart-summary {
            background: #ffffff;
            border: 0.1vw solid #e2e8f0;
            border-radius: 1.2vw;
            padding: 1.5vw;
            position: sticky;
            top: 2vw;
            height: fit-content;
        }

        .cart-summary h2 {
            font-size: 1.2vw;
            font-weight: 700;
            color: #0f172a;
        }

        .cart-summary .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.8vw;
            padding: 0.4vw 0;
        }

        .cart-summary .summary-row .label {
            color: #94a3b8;
        }

        .cart-summary .summary-row .value {
            font-weight: 500;
            color: #0f172a;
        }

        .cart-summary .summary-divider {
            border-top: 0.1vw solid #e2e8f0;
            margin: 0.5vw 0;
            padding-top: 0.5vw;
        }

        .cart-summary .summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 1vw;
            font-weight: 700;
            color: #0f172a;
            padding-top: 0.5vw;
        }

        .cart-summary .btn-checkout {
            display: block;
            width: 100%;
            margin-top: 1.2vw;
            padding: 0.8vw 1.5vw;
            background: #0f172a;
            color: #ffffff;
            font-size: 0.9vw;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            border-radius: 0.7vw;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .cart-summary .btn-checkout:hover {
            background: #1e293b;
            transform: translateY(-0.1vw);
            box-shadow: 0 0.2vw 0.8vw rgba(15, 23, 42, 0.15);
        }

        .cart-summary .btn-checkout:active {
            transform: scale(0.97);
        }

        /* ============================================
           RESPONSIVE - TABLET
           ============================================ */
        @media (max-width: 1024px) {
            .cart-grid {
                grid-template-columns: 1fr;
                gap: 2vw;
            }

            .cart-summary {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .cart-container {
                padding: 2vw 3vw;
            }

            .cart-header h1 {
                font-size: 3vw;
            }

            .cart-header p {
                font-size: 1.2vw;
            }

            .cart-item-image {
                width: 8vw;
                height: 8vw;
                min-width: 8vw;
            }

            .cart-item-info .item-name {
                font-size: 1.4vw;
            }

            .cart-item-info .item-variant {
                font-size: 1vw;
            }

            .cart-item-info .item-price {
                font-size: 1.3vw;
            }

            .cart-item-actions .qty-btn {
                width: 3vw;
                height: 3vw;
                font-size: 1.6vw;
            }

            .cart-item-actions .qty-input {
                width: 4vw;
                height: 3vw;
                font-size: 1.2vw;
            }

            .cart-item-actions .btn-remove svg {
                width: 1.8vw;
                height: 1.8vw;
            }

            .cart-item-actions .item-subtotal {
                font-size: 1.3vw;
                min-width: 8vw;
            }

            .cart-summary h2 {
                font-size: 1.8vw;
            }

            .cart-summary .summary-row {
                font-size: 1.2vw;
            }

            .cart-summary .summary-total {
                font-size: 1.6vw;
            }

            .cart-summary .btn-checkout {
                font-size: 1.4vw;
                padding: 1vw 2vw;
            }

            .cart-empty .empty-icon {
                width: 10vw;
                height: 10vw;
                font-size: 5vw;
            }

            .cart-empty h3 {
                font-size: 2vw;
            }

            .cart-empty p {
                font-size: 1.2vw;
            }

            .cart-empty .btn-shop {
                font-size: 1.2vw;
                padding: 1vw 3vw;
            }

            .cart-bottom-actions .btn-continue,
            .cart-bottom-actions .btn-clear {
                font-size: 1.2vw;
            }
        }

        /* ============================================
           RESPONSIVE - MOBILE
           ============================================ */
        @media (max-width: 480px) {
            .cart-container {
                padding: 2vw 2vw;
            }

            .cart-header h1 {
                font-size: 4.5vw;
            }

            .cart-header p {
                font-size: 1.8vw;
            }

            .cart-items-wrapper {
                padding: 2.5vw;
                border-radius: 1.8vw;
            }

            .cart-item {
                flex-direction: column;
                align-items: stretch;
                gap: 1.5vw;
                padding-bottom: 2vw;
            }

            .cart-item-left {
                gap: 2vw;
            }

            .cart-item-image {
                width: 14vw;
                height: 14vw;
                min-width: 14vw;
                border-radius: 1.2vw;
            }

            .cart-item-image .placeholder {
                font-size: 4vw;
            }

            .cart-item-info .item-name {
                font-size: 2.2vw;
            }

            .cart-item-info .item-variant {
                font-size: 1.6vw;
            }

            .cart-item-info .item-price {
                font-size: 2vw;
            }

            .cart-item-actions {
                justify-content: flex-end;
                gap: 1.5vw;
            }

            .cart-item-actions .qty-wrapper {
                border-radius: 0.8vw;
            }

            .cart-item-actions .qty-btn {
                width: 5vw;
                height: 5vw;
                font-size: 2.8vw;
            }

            .cart-item-actions .qty-input {
                width: 7vw;
                height: 5vw;
                font-size: 2vw;
            }

            .cart-item-actions .btn-remove {
                width: 4vw;
                height: 4vw;
            }

            .cart-item-actions .btn-remove svg {
                width: 3vw;
                height: 3vw;
            }

            .cart-item-actions .item-subtotal {
                font-size: 2vw;
                min-width: 14vw;
            }

            .cart-bottom-actions {
                flex-direction: column;
                gap: 1vw;
                align-items: center;
            }

            .cart-bottom-actions .btn-continue,
            .cart-bottom-actions .btn-clear {
                font-size: 1.8vw;
            }

            .cart-summary {
                padding: 2.5vw;
                border-radius: 1.8vw;
            }

            .cart-summary h2 {
                font-size: 2.8vw;
            }

            .cart-summary .summary-row {
                font-size: 1.8vw;
                padding: 0.6vw 0;
            }

            .cart-summary .summary-total {
                font-size: 2.4vw;
            }

            .cart-summary .btn-checkout {
                font-size: 2.2vw;
                padding: 1.5vw 3vw;
                border-radius: 1.2vw;
            }

            .cart-empty {
                padding: 6vw 3vw;
                border-radius: 1.8vw;
            }

            .cart-empty .empty-icon {
                width: 16vw;
                height: 16vw;
                font-size: 8vw;
            }

            .cart-empty h3 {
                font-size: 3vw;
            }

            .cart-empty p {
                font-size: 1.8vw;
            }

            .cart-empty .btn-shop {
                font-size: 1.8vw;
                padding: 1.5vw 4vw;
                border-radius: 1.2vw;
            }

            .cart-alert {
                font-size: 1.6vw;
                padding: 1.5vw 2.5vw;
                border-radius: 1.2vw;
            }
        }

        /* ============================================
           RESPONSIVE - EXTRA SMALL
           ============================================ */
        @media (max-width: 360px) {
            .cart-item-left {
                gap: 3vw;
            }

            .cart-item-image {
                width: 18vw;
                height: 18vw;
                min-width: 18vw;
            }

            .cart-item-info .item-name {
                font-size: 2.8vw;
            }

            .cart-item-info .item-price {
                font-size: 2.6vw;
            }

            .cart-item-actions .qty-btn {
                width: 6.5vw;
                height: 6.5vw;
                font-size: 3.6vw;
            }

            .cart-item-actions .qty-input {
                width: 9vw;
                height: 6.5vw;
                font-size: 2.6vw;
            }

            .cart-item-actions .item-subtotal {
                font-size: 2.6vw;
                min-width: 18vw;
            }

            .cart-summary h2 {
                font-size: 3.6vw;
            }

            .cart-summary .summary-row {
                font-size: 2.2vw;
            }

            .cart-summary .summary-total {
                font-size: 3vw;
            }

            .cart-summary .btn-checkout {
                font-size: 2.8vw;
                padding: 2vw 4vw;
            }
        }

        /* ============================================
           UTILITY
           ============================================ */
        .hidden {
            display: none !important;
        }
    </style>

    <main class="cart-container">
    <div class="katalog_top_container">
        <div class="cart-header">
            <h1>Keranjang Belanja</h1>
            <p>Tinjau dan kelola item di keranjang belanja Anda.</p>
        </div>

        @if (session('success'))
            <div class="cart-alert success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="cart-alert error">
                {{ session('error') }}
            </div>
        @endif
    </div>

    @if (empty($cart) || count($cart) == 0)
        {{-- Empty Cart --}}
        <div class="cart-empty">
            <div class="empty-icon">🛒</div>
            <h3>Keranjang Kosong</h3>
            <p>Belum ada produk di keranjang. Yuk, mulai belanja!</p>
            <a href="{{ route('customer.products.index') }}" class="btn-shop">
                Mulai Belanja
            </a>
        </div>
    @else
        <div class="cart-grid">

            {{-- Cart Items --}}
            <div class="cart-items-wrapper">
                <div class="cart-items">
                    @php $subtotal = 0; @endphp
                    @foreach ($cart as $key => $item)
                        @php 
                            $itemPrice = isset($item['price']) ? $item['price'] : 0;
                            $itemQuantity = isset($item['quantity']) ? $item['quantity'] : 1;
                            $subtotal += $itemPrice * $itemQuantity; 
                        @endphp
                        <div class="cart-item" data-key="{{ $key }}">
                            <div class="cart-item-left">
                                {{-- Image --}}
                                <div class="cart-item-image">
                                    @if (!empty($item['image']) && Storage::disk('public')->exists($item['image']))
                                        <img src="{{ Storage::url($item['image']) }}" 
                                             alt="{{ $item['product_name'] ?? 'Produk' }}">
                                    @else
                                        <div class="placeholder">📦</div>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="cart-item-info">
                                    <a href="{{ route('customer.products.show', $item['slug'] ?? '#') }}" 
                                       class="item-name">
                                        {{ $item['product_name'] ?? 'Produk' }}
                                    </a>
                                    @if (!empty($item['variant_name']))
                                        <p class="item-variant">Varian: {{ $item['variant_name'] }}</p>
                                    @endif
                                    <p class="item-price">
                                        Rp {{ number_format($itemPrice, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="cart-item-actions">
                                {{-- Quantity --}}
                                <div class="qty-wrapper">
                                    <button class="qty-btn" data-action="decrease">−</button>
                                    <input type="number" class="qty-input" 
                                           value="{{ $itemQuantity }}" min="1" data-key="{{ $key }}">
                                    <button class="qty-btn" data-action="increase">+</button>
                                </div>

                                {{-- Remove --}}
                                <button class="btn-remove" data-key="{{ $key }}">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>

                                {{-- Subtotal item --}}
                                <p class="item-subtotal">
                                    Rp {{ number_format($itemPrice * $itemQuantity, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Action Buttons --}}
                <div class="cart-bottom-actions">
                    <a href="{{ route('customer.products.index') }}" class="btn-continue">
                        ← Lanjut Belanja
                    </a>
                    <form action="{{ route('customer.cart.clear') }}" method="POST" 
                          onsubmit="return confirm('Kosongkan keranjang?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-clear">
                            Kosongkan Keranjang
                        </button>
                    </form>
                </div>
            </div>

            {{-- Summary --}}
            <div class="cart-summary">
                <h2>Ringkasan Belanja</h2>

                <div class="summary-row">
                    <span class="label">Subtotal</span>
                    <span class="value">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span class="label">Ongkir</span>
                    <span class="value">Dihitung di checkout</span>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-total">
                    <span>Total</span>
                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>

                <a href="{{ route('customer.checkout.index') }}" class="btn-checkout">
                    Checkout →
                </a>
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
            const input = this.closest('.qty-wrapper').querySelector('.qty-input');
            let value = parseInt(input.value) || 1;
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
            let value = parseInt(this.value) || 1;
            if (value < 1) {
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
                alert(data.message || 'Gagal memperbarui keranjang');
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

    document.querySelectorAll('.btn-remove').forEach(button => {
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
                } else {
                    alert(data.message || 'Gagal menghapus item');
                }
            })
            .catch(() => {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
        });
    });
});
</script>

@endsection