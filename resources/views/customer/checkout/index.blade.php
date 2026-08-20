@extends('layouts.customer')

@section('title', 'Checkout - Barokah Sport')

@section('content')
    
    <style>

        /* ============================================
           CHECKOUT CONTAINER
           ============================================ */
        .checkout-container {
            max-width: 90vw;
            margin: 0 auto;
            padding: 1.5vw 3vw;
        }

        /* ============================================
           HEADER
           ============================================ */
        .checkout-header {
            margin-bottom: 2vw;
        }

        .checkout-header h1 {
            font-size: 2.7vw;
            font-weight: 700;
            color: #0f172a;
            font-family:heading;
            text-transform:uppercase;
        }

        .checkout-header p {
            font-size: 0.85vw;
            color: #94a3b8;
            margin-top: 0.2vw;
        }

        /* ============================================
           ALERT
           ============================================ */
        .checkout-alert {
            padding: 1vw 1.5vw;
            border-radius: 0.8vw;
            margin-bottom: 1.5vw;
            font-size: 0.85vw;
        }

        .checkout-alert.error {
            background: #fef2f2;
            border: 0.1vw solid #fca5a5;
            color: #991b1b;
        }

        .checkout-alert .alert-title {
            font-weight: 600;
        }

        .checkout-alert .alert-list {
            margin-top: 0.5vw;
            padding-left: 1.5vw;
        }

        .checkout-alert .alert-list li {
            list-style: disc;
        }

        /* ============================================
           CHECKOUT GRID
           ============================================ */
        .checkout-grid {
            display: grid;
            grid-template-columns: 70% 30%;
        }

        /* ============================================
           FORM SECTION
           ============================================ */
        .checkout-section {
            background: #ffffff;
            border: 0.1vw solid #e2e8f0;
            border-radius: 1.2vw;
            padding: 1.5vw;
            margin-bottom: 1.5vw;
            margin-right:1.5vw;
        }

        .checkout-section:last-child {
            margin-bottom: 0;
        }

        .checkout-section .section-title {
            font-size: 1.1vw;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.2vw;
        }

        .checkout-section .section-subtitle {
            font-size: 0.8vw;
            color: #94a3b8;
            margin-bottom: 1vw;
        }

        /* ============================================
           FORM ELEMENTS
           ============================================ */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1vw;
        }

        .form-grid .full-width {
            grid-column: 1 / -1;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.3vw;
        }

        .form-group label {
            font-size: 0.8vw;
            font-weight: 500;
            color: #0f172a;
        }

        .form-group label .required {
            color: #ef4444;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.7vw 1vw;
            border: 0.1vw solid #e2e8f0;
            border-radius: 0.7vw;
            font-size: 0.8vw;
            color: #0f172a;
            background: #ffffff;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2vw rgba(59, 130, 246, 0.1);
        }

        .form-group input:disabled,
        .form-group select:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            background: #f1f5f9;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 5vw;
        }

        .form-group .helper-text {
            font-size: 0.65vw;
            color: #94a3b8;
            margin-top: 0.2vw;
        }

        .form-group .error-text {
            font-size: 0.7vw;
            color: #ef4444;
            margin-top: 0.2vw;
        }

        /* ============================================
           SHIPPING COST DISPLAY
           ============================================ */
        .shipping-cost-display {
            width: 100%;
            padding: 0.7vw 1vw;
            border: 0.1vw solid #e2e8f0;
            border-radius: 0.7vw;
            font-size: 0.8vw;
            color: #94a3b8;
            background: #f8fafc;
            min-height: 3vw;
            display: flex;
            align-items: center;
        }

        .shipping-cost-display .loading {
            color: #3b82f6;
        }

        .shipping-cost-display .success {
            color: #22c55e;
        }

        .shipping-cost-display .error {
            color: #ef4444;
        }

        /* ============================================
           PAYMENT METHOD
           ============================================ */
        .payment-options {
            display: flex;
            flex-direction: column;
            gap: 0.6vw;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 0.8vw;
            padding: 0.8vw 1.2vw;
            border: 0.1vw solid #e2e8f0;
            border-radius: 0.7vw;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .payment-option:hover {
            border-color: #93c5fd;
        }

        .payment-option input[type="radio"] {
            width: 1.2vw;
            height: 1.2vw;
            accent-color: #3b82f6;
            flex-shrink: 0;
            cursor: pointer;
        }

        .payment-option .payment-info {
            display: flex;
            flex-direction: column;
        }

        .payment-option .payment-info .payment-name {
            font-size: 0.85vw;
            font-weight: 500;
            color: #0f172a;
        }

        .payment-option .payment-info .payment-desc {
            font-size: 0.7vw;
            color: #94a3b8;
        }

        .payment-option.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .payment-option.disabled input[type="radio"] {
            cursor: not-allowed;
        }

        /* ============================================
           GUEST INFO
           ============================================ */
        .guest-info {
            padding: 0.8vw 1.2vw;
            border-radius: 0.7vw;
            background: #eff6ff;
            border: 0.1vw solid #93c5fd;
            font-size: 0.8vw;
            color: #1d4ed8;
            margin-top: 1vw;
        }

        .guest-info .guest-title {
            font-weight: 600;
        }

        .guest-info .guest-text {
            margin-top: 0.2vw;
        }

        .guest-info .guest-hint {
            font-size: 0.7vw;
            color: #60a5fa;
            margin-top: 0.2vw;
        }

        /* ============================================
           WEIGHT DISPLAY
           ============================================ */
        .weight-display {
            font-size: 0.8vw;
            color: #94a3b8;
            margin-top: 0.5vw;
        }

        .weight-display .weight-value {
            font-weight: 600;
            color: #0f172a;
        }

        .weight-display .weight-unit {
            color: #94a3b8;
        }

        .weight-display .weight-items {
            font-size: 0.65vw;
            color: #94a3b8;
            margin-left: 0.5vw;
        }

        /* ============================================
           SUMMARY
           ============================================ */
        .checkout-summary {
            background: #ffffff;
            border: 0.1vw solid #e2e8f0;
            border-radius: 1.2vw;
            padding: 1.5vw;
            position: sticky;
            top: 8vw;
            height: fit-content;
        }

        .checkout-summary .summary-title {
            font-size: 1.1vw;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1vw;
        }

        .checkout-summary .summary-items {
            max-height: 16vw;
            overflow-y: auto;
            margin-bottom: 1vw;
        }

        .checkout-summary .summary-items::-webkit-scrollbar {
            width: 0.2vw;
        }

        .checkout-summary .summary-items::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 0.2vw;
        }

        .checkout-summary .summary-items::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 0.2vw;
        }

        .checkout-summary .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 0.4vw 0;
            border-bottom: 0.05vw solid #f1f5f9;
            font-size: 0.8vw;
        }

        .checkout-summary .summary-item:last-child {
            border-bottom: none;
        }

        .checkout-summary .summary-item .item-info {
            flex: 1;
            min-width: 0;
        }

        .checkout-summary .summary-item .item-name {
            font-weight: 500;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .checkout-summary .summary-item .item-variant {
            font-size: 0.65vw;
            color: #94a3b8;
        }

        .checkout-summary .summary-item .item-qty {
            font-size: 0.65vw;
            color: #94a3b8;
        }

        .checkout-summary .summary-item .item-price {
            font-weight: 600;
            color: #0f172a;
            margin-left: 0.5vw;
            white-space: nowrap;
        }

        .checkout-summary .summary-divider {
            border-top: 0.1vw solid #e2e8f0;
            margin: 0.5vw 0;
        }

        .checkout-summary .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.8vw;
            padding: 0.3vw 0;
        }

        .checkout-summary .summary-row .row-label {
            color: #94a3b8;
        }

        .checkout-summary .summary-row .row-value {
            font-weight: 500;
            color: #0f172a;
        }

        .checkout-summary .summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 1vw;
            font-weight: 700;
            color: #0f172a;
            padding-top: 0.5vw;
            border-top: 0.15vw solid #0f172a;
            margin-top: 0.3vw;
        }

        .checkout-summary .btn-submit {
            display: block;
            width: 100%;
            padding: 0.8vw 1.5vw;
            background: #0f172a;
            color: #ffffff;
            font-size: 0.9vw;
            font-weight: 600;
            text-align: center;
            border: none;
            border-radius: 0.7vw;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 1vw;
            font-family: inherit;
        }

        .checkout-summary .btn-submit:hover {
            background: #1e293b;
            transform: translateY(-0.1vw);
            box-shadow: 0 0.2vw 0.8vw rgba(15, 23, 42, 0.15);
        }

        .checkout-summary .btn-submit:active {
            transform: scale(0.97);
        }

        .checkout-summary .btn-back {
            display: block;
            text-align: center;
            font-size: 0.8vw;
            color: #94a3b8;
            text-decoration: none;
            margin-top: 0.8vw;
            transition: color 0.2s;
        }

        .checkout-summary .btn-back:hover {
            color: #0f172a;
        }

        /* ============================================
           RESPONSIVE - TABLET
           ============================================ */
        @media (max-width: 1024px) {
            .checkout-grid {
                grid-template-columns: 1fr;
                gap: 2vw;
            }

            .checkout-summary {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .checkout-container {
                padding: 2vw 3vw;
            }

            .checkout-header h1 {
                font-size: 3vw;
            }

            .checkout-header p {
                font-size: 1.2vw;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 1.2vw;
            }

            .form-grid .full-width {
                grid-column: 1;
            }

            .form-group label {
                font-size: 1.2vw;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                font-size: 1.2vw;
                padding: 0.8vw 1.2vw;
                border-radius: 0.8vw;
            }

            .form-group .helper-text {
                font-size: 1vw;
            }

            .form-group .error-text {
                font-size: 1vw;
            }

            .checkout-section {
                padding: 2vw;
                border-radius: 1.5vw;
            }

            .checkout-section .section-title {
                font-size: 1.6vw;
            }

            .checkout-section .section-subtitle {
                font-size: 1.2vw;
            }

            .payment-option {
                padding: 1vw 1.5vw;
                border-radius: 1vw;
            }

            .payment-option input[type="radio"] {
                width: 1.8vw;
                height: 1.8vw;
            }

            .payment-option .payment-info .payment-name {
                font-size: 1.3vw;
            }

            .payment-option .payment-info .payment-desc {
                font-size: 1vw;
            }

            .guest-info {
                font-size: 1.2vw;
                padding: 1.2vw 1.8vw;
            }

            .guest-info .guest-hint {
                font-size: 1vw;
            }

            .shipping-cost-display {
                font-size: 1.2vw;
                padding: 0.8vw 1.2vw;
                min-height: 4vw;
            }

            .weight-display {
                font-size: 1.2vw;
            }

            .weight-display .weight-items {
                font-size: 1vw;
            }

            .checkout-summary {
                padding: 2vw;
                border-radius: 1.5vw;
            }

            .checkout-summary .summary-title {
                font-size: 1.6vw;
            }

            .checkout-summary .summary-item {
                font-size: 1.2vw;
                padding: 0.6vw 0;
            }

            .checkout-summary .summary-item .item-variant {
                font-size: 1vw;
            }

            .checkout-summary .summary-item .item-qty {
                font-size: 1vw;
            }

            .checkout-summary .summary-row {
                font-size: 1.2vw;
            }

            .checkout-summary .summary-total {
                font-size: 1.6vw;
            }

            .checkout-summary .btn-submit {
                font-size: 1.4vw;
                padding: 1vw 2vw;
                border-radius: 1vw;
            }

            .checkout-summary .btn-back {
                font-size: 1.2vw;
            }

            .checkout-alert {
                font-size: 1.2vw;
                padding: 1.2vw 1.8vw;
                border-radius: 1vw;
            }
        }

        @media (max-width: 480px) {
            .checkout-container {
                padding: 2vw 2vw;
            }

            .checkout-header h1 {
                font-size: 4.5vw;
            }

            .checkout-header p {
                font-size: 1.8vw;
            }

            .form-group label {
                font-size: 1.8vw;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                font-size: 1.8vw;
                padding: 1.2vw 1.8vw;
                border-radius: 1.2vw;
            }

            .form-group .helper-text {
                font-size: 1.4vw;
            }

            .form-group .error-text {
                font-size: 1.4vw;
            }

            .checkout-section {
                padding: 3vw;
                border-radius: 2vw;
            }

            .checkout-section .section-title {
                font-size: 2.4vw;
            }

            .checkout-section .section-subtitle {
                font-size: 1.8vw;
            }

            .payment-option {
                padding: 1.5vw 2vw;
                border-radius: 1.5vw;
            }

            .payment-option input[type="radio"] {
                width: 2.8vw;
                height: 2.8vw;
            }

            .payment-option .payment-info .payment-name {
                font-size: 2vw;
            }

            .payment-option .payment-info .payment-desc {
                font-size: 1.6vw;
            }

            .guest-info {
                font-size: 1.8vw;
                padding: 1.8vw 2.5vw;
                border-radius: 1.2vw;
            }

            .guest-info .guest-hint {
                font-size: 1.6vw;
            }

            .shipping-cost-display {
                font-size: 1.8vw;
                padding: 1.2vw 1.8vw;
                min-height: 6vw;
            }

            .weight-display {
                font-size: 1.8vw;
            }

            .weight-display .weight-items {
                font-size: 1.4vw;
            }

            .checkout-summary {
                padding: 3vw;
                border-radius: 2vw;
            }

            .checkout-summary .summary-title {
                font-size: 2.4vw;
            }

            .checkout-summary .summary-item {
                font-size: 1.8vw;
                padding: 0.8vw 0;
            }

            .checkout-summary .summary-item .item-variant {
                font-size: 1.4vw;
            }

            .checkout-summary .summary-item .item-qty {
                font-size: 1.4vw;
            }

            .checkout-summary .summary-row {
                font-size: 1.8vw;
            }

            .checkout-summary .summary-total {
                font-size: 2.4vw;
            }

            .checkout-summary .btn-submit {
                font-size: 2.2vw;
                padding: 1.5vw 3vw;
                border-radius: 1.5vw;
            }

            .checkout-summary .btn-back {
                font-size: 1.8vw;
            }

            .checkout-alert {
                font-size: 1.8vw;
                padding: 1.8vw 2.5vw;
                border-radius: 1.5vw;
            }
        }

        @media (max-width: 360px) {
            .checkout-summary .summary-item {
                flex-direction: column;
                gap: 0.3vw;
            }

            .checkout-summary .summary-item .item-price {
                margin-left: 0;
            }

            .payment-option {
                flex-wrap: wrap;
            }
        }

        /* ============================================
           UTILITY
           ============================================ */
        .hidden {
            display: none !important;
        }

        .loading-opacity {
            opacity: 0.6;
            pointer-events: none;
        }

        .text-center {
            text-align: center;
        }

        .mt-1 { margin-top: 0.5vw; }
        .mt-2 { margin-top: 1vw; }
        .mb-1 { margin-bottom: 0.5vw; }
        .mb-2 { margin-bottom: 1vw; }
    </style>

    <main class="checkout-container">

        {{-- Header --}}
        <div class="checkout-header">
            <h1>Checkout</h1>
            <p>Lengkapi data untuk menyelesaikan pesanan.</p>
        </div>

        @if (session('error'))
            <div class="checkout-alert error">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="checkout-alert error">
                <p class="alert-title">Terjadi kesalahan:</p>
                <ul class="alert-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="checkout-grid">

            {{-- Form --}}
            <div>
                <form action="{{ route('customer.checkout.process') }}" method="POST" id="checkout-form">
                    @csrf

                    {{-- Informasi Pengiriman --}}
                    <div class="checkout-section">
                        <h2 class="section-title">📍 Informasi Pengiriman</h2>
                        <p class="section-subtitle">Isi data penerima paket.</p>

                        <div class="form-grid">
                            {{-- Nama Penerima --}}
                            <div class="form-group full-width">
                                <label>Nama Penerima <span class="required">*</span></label>
                                <input type="text" name="shipping_name" id="shipping_name" 
                                    value="{{ old('shipping_name', $defaultAddress->recipient_name ?? $customer->name ?? '') }}" 
                                    required>
                                @error('shipping_name') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            {{-- Nomor WhatsApp --}}
                            <div class="form-group full-width">
                                <label>Nomor WhatsApp <span class="required">*</span></label>
                                <input type="tel" name="shipping_phone" id="shipping_phone" 
                                    value="{{ old('shipping_phone', $defaultAddress->recipient_phone ?? $customer->phone ?? '') }}" 
                                    placeholder="08xxxxxxxxxx" required>
                                @error('shipping_phone') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            {{-- Alamat Lengkap --}}
                            <div class="form-group full-width">
                                <label>Alamat Lengkap <span class="required">*</span></label>
                                <textarea name="shipping_address" id="shipping_address" rows="3" required>{{ old('shipping_address', $defaultAddress->address ?? '') }}</textarea>
                                <span class="helper-text">Contoh: Jalan Mawar No. 10, RT 01 RW 02</span>
                                @error('shipping_address') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            {{-- Provinsi --}}
                            <div class="form-group">
                                <label>Provinsi <span class="required">*</span></label>
                                <select name="shipping_province" id="province" required>
                                    <option value="">-- Pilih Provinsi --</option>
                                </select>
                                <input type="hidden" name="shipping_province_id" id="province_id">
                                @error('shipping_province') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            {{-- Kota/Kabupaten --}}
                            <div class="form-group">
                                <label>Kota/Kabupaten <span class="required">*</span></label>
                                <select name="shipping_city" id="city" required>
                                    <option value="">-- Pilih Kota --</option>
                                </select>
                                <input type="hidden" name="shipping_city_id" id="city_id">
                                @error('shipping_city') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            {{-- Kecamatan --}}
                            <div class="form-group">
                                <label>Kecamatan <span class="required">*</span></label>
                                <select name="shipping_district" id="district" required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                                <input type="hidden" name="shipping_district_id" id="district_id">
                                @error('shipping_district') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            {{-- Kelurahan --}}
                            <div class="form-group">
                                <label>Kelurahan <span class="required">*</span></label>
                                <select name="shipping_subdistrict" id="subdistrict" required>
                                    <option value="">-- Pilih Kelurahan --</option>
                                </select>
                                <input type="hidden" name="shipping_subdistrict_id" id="subdistrict_id">
                                @error('shipping_subdistrict') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            {{-- Hidden Postal Code --}}
                            <input type="hidden" name="shipping_postal_code" id="shipping_postal_code" value="0">
                        </div>
                    </div>

                    {{-- Ekspedisi & Ongkir --}}
                    <div class="checkout-section">
                        <h2 class="section-title">🚚 Ekspedisi & Ongkir</h2>
                        <p class="section-subtitle">Pilih kurir dan lihat estimasi ongkir.</p>

                        <div class="form-grid">
                            {{-- Kurir --}}
                            <div class="form-group">
                                <label>Kurir <span class="required">*</span></label>
                                <select name="courier" id="courier" required disabled>
                                    <option value="">-- Pilih Kurir --</option>
                                </select>
                            </div>

                            {{-- Layanan --}}
                            <div class="form-group">
                                <label>Layanan</label>
                                <select name="shipping_service" id="service" disabled>
                                    <option value="">-- Pilih Layanan --</option>
                                </select>
                            </div>

                            {{-- Estimasi Ongkir --}}
                            <div class="form-group full-width">
                                <label>Estimasi Ongkir</label>
                                <div class="shipping-cost-display" id="shipping-cost-display">
                                    Pilih kurir dan kota tujuan
                                </div>
                                <input type="hidden" name="shipping_cost" id="shipping_cost" value="0">
                            </div>
                        </div>

                        {{-- Berat Total --}}
                        <div class="weight-display">
                            <span>Berat total: </span>
                            <span class="weight-value" id="total-weight">0</span>
                            <span class="weight-unit"> gram</span>
                            <span class="weight-items">({{ count($cart) }} produk)</span>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div class="checkout-section">
                        <h2 class="section-title">📝 Catatan</h2>
                        <textarea name="notes" rows="3" class="full-width" style="width:100%;padding:0.7vw 1vw;border:0.1vw solid #e2e8f0;border-radius:0.7vw;font-size:0.8vw;font-family:inherit;" placeholder="Tambahkan catatan untuk pesanan (opsional)">{{ old('notes') }}</textarea>
                    </div>

                    {{-- Payment Method --}}
                    <div class="checkout-section">
                        <h2 class="section-title">💳 Metode Pembayaran</h2>
                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="transfer" checked>
                                <div class="payment-info">
                                    <span class="payment-name">Transfer Bank (Manual)</span>
                                    <span class="payment-desc">Pembayaran akan dikonfirmasi oleh admin.</span>
                                </div>
                            </label>
                            <label class="payment-option disabled">
                                <input type="radio" name="payment_method" value="qris" disabled>
                                <div class="payment-info">
                                    <span class="payment-name">QRIS <span style="font-size:0.6vw;color:#94a3b8;">(Segera)</span></span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Info Guest --}}
                    @guest('customer')
                        <div class="guest-info">
                            <p class="guest-title">💡 Akun akan dibuat otomatis</p>
                            <p class="guest-text">Kamu akan login otomatis dengan nomor WhatsApp. Password default: <strong>6 digit terakhir nomor WhatsApp</strong>.</p>
                            <p class="guest-hint">Kamu bisa mengganti password nanti di halaman profil.</p>
                        </div>
                    @endguest

                </form>
            </div>

            {{-- Summary --}}
            <div class="checkout-summary">
                <h2 class="summary-title">Ringkasan Pesanan</h2>

                {{-- Items --}}
                <div class="summary-items">
                    @foreach ($cart as $item)
                        <div class="summary-item">
                            <div class="item-info">
                                <div class="item-name">{{ $item['product_name'] }}</div>
                                @if ($item['variant_name'])
                                    <div class="item-variant">{{ $item['variant_name'] }}</div>
                                @endif
                                <div class="item-qty">{{ $item['quantity'] }}x</div>
                            </div>
                            <span class="item-price">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Subtotal --}}
                <div class="summary-divider"></div>
                <div class="summary-row">
                    <span class="row-label">Subtotal</span>
                    <span class="row-value" id="subtotal-display">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>

                {{-- Ongkir --}}
                <div class="summary-row" id="shipping-summary">
                    <span class="row-label">Ongkir</span>
                    <span class="row-value" id="shipping-cost-text">Rp 0</span>
                </div>

                {{-- Total --}}
                <div class="summary-total">
                    <span>Total</span>
                    <span id="total-display">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>

                {{-- Submit Button --}}
                <button type="submit" form="checkout-form" class="btn-submit">
                    Buat Pesanan
                </button>

                <a href="{{ route('customer.cart.index') }}" class="btn-back">
                    ← Kembali ke Keranjang
                </a>
            </div>

        </div>

    </main>

    @include('customer.partials.footer')

    <script>
        $(document).ready(function() {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            let subtotal = {{ $subtotal }};

            // 🔥 DATA DEFAULT ADDRESS (dari server)
            const defaultAddress = @json($defaultAddress);
            const isLoggedIn = @json(Auth::guard('customer')->check());

            // ============================================
            // CACHE MANAGEMENT
            // ============================================

            const CACHE_KEY_PROVINCES = 'checkout_provinces';
            const CACHE_KEY_CITIES = 'checkout_cities_';
            const CACHE_KEY_DISTRICTS = 'checkout_districts_';
            const CACHE_KEY_VILLAGES = 'checkout_villages_';

            function getCache(key) {
                try {
                    const data = localStorage.getItem(key);
                    if (data) {
                        const parsed = JSON.parse(data);
                        if (parsed.expiry && parsed.expiry > Date.now()) {
                            return parsed.data;
                        }
                        localStorage.removeItem(key);
                    }
                } catch(e) {}
                return null;
            }

            function setCache(key, data, ttl = 3600000) {
                try {
                    localStorage.setItem(key, JSON.stringify({
                        data: data,
                        expiry: Date.now() + ttl
                    }));
                } catch(e) {}
            }

            // ============================================
            // LOAD PROVINCES
            // ============================================

            function loadProvinces() {
                let cached = getCache(CACHE_KEY_PROVINCES);

                if (cached) {
                    console.log('Provinces loaded from cache:', cached);
                    renderProvinces(cached);
                    return;
                }

                $('#province').html('<option value="">-- Memuat Provinsi --</option>');

                $.ajax({
                    url: '/api/indonesia-regions/cascade?country_name=ID',
                    method: 'GET',
                    timeout: 30000,
                    success: function(response) {
                        console.log('API Response - Cascade:', response);
                        const provinces = response?.options?.provinces || [];
                        console.log('Provinces:', provinces);
                        setCache(CACHE_KEY_PROVINCES, provinces);
                        renderProvinces(provinces);
                    },
                    error: function(xhr) {
                        console.error('Error loading provinces:', xhr.responseText);
                        $('#province').html('<option value="">-- Gagal memuat data --</option>');
                        setTimeout(loadProvinces, 5000);
                    }
                });
            }

            function renderProvinces(data) {
                let options = '<option value="">-- Pilih Provinsi --</option>';
                if (data && Array.isArray(data) && data.length > 0) {
                    $.each(data, function(index, province) {
                        const code = province.value;
                        const name = province.label;
                        if (code && name) {
                            options += `<option value="${name}" data-code="${code}">${name}</option>`;
                        }
                    });
                }
                $('#province').html(options);

                if (defaultAddress && defaultAddress.province) {
                    $('#province').val(defaultAddress.province).trigger('change');
                }
            }

            // ============================================
            // LOAD CITIES
            // ============================================

            function loadCities(provinceCode, selectedCity = null) {
                const cacheKey = CACHE_KEY_CITIES + provinceCode;
                let cached = getCache(cacheKey);

                if (cached) {
                    renderCities(cached, selectedCity);
                    return;
                }

                $('#city').html('<option value="">-- Memuat Kota --</option>').prop('disabled', true);

                $.ajax({
                    url: '/api/indonesia-regions/cascade?region_code=' + provinceCode + '&country_name=ID',
                    method: 'GET',
                    success: function(response) {
                        console.log('Cities API response:', response);
                        const cities = response?.options?.cities || [];
                        setCache(cacheKey, cities);
                        renderCities(cities, selectedCity);
                    },
                    error: function() {
                        $('#city').html('<option value="">-- Gagal memuat --</option>').prop('disabled', true);
                    }
                });
            }

            function renderCities(data, selectedCity) {
                let options = '<option value="">-- Pilih Kota --</option>';
                if (data && data.length > 0) {
                    $.each(data, function(index, city) {
                        const code = city.value;
                        const name = city.label;
                        if (code && name) {
                            options += `<option value="${name}" data-code="${code}">${name}</option>`;
                        }
                    });
                }
                $('#city').html(options).prop('disabled', false);

                if (selectedCity) {
                    setTimeout(function() {
                        $('#city').val(selectedCity).trigger('change');
                    }, 100);
                }
            }

            // ============================================
            // LOAD DISTRICTS
            // ============================================

            function loadDistricts(cityCode, selectedDistrict = null) {
                const cacheKey = CACHE_KEY_DISTRICTS + cityCode;
                let cached = getCache(cacheKey);

                if (cached) {
                    renderDistricts(cached, selectedDistrict);
                    return;
                }

                $('#district').html('<option value="">-- Memuat Kecamatan --</option>').prop('disabled', true);

                $.ajax({
                    url: '/api/indonesia-regions/cascade?region_code=' + cityCode + '&country_name=ID',
                    method: 'GET',
                    success: function(response) {
                        console.log('Districts API response:', response);
                        const districts = response?.options?.districts || [];
                        setCache(cacheKey, districts);
                        renderDistricts(districts, selectedDistrict);
                    },
                    error: function() {
                        $('#district').html('<option value="">-- Gagal memuat --</option>').prop('disabled', true);
                    }
                });
            }

            function renderDistricts(data, selectedDistrict) {
                let options = '<option value="">-- Pilih Kecamatan --</option>';
                if (data && data.length > 0) {
                    $.each(data, function(index, district) {
                        const code = district.value;
                        const name = district.label;
                        if (code && name) {
                            options += `<option value="${name}" data-code="${code}">${name}</option>`;
                        }
                    });
                }
                $('#district').html(options).prop('disabled', false);

                if (selectedDistrict) {
                    setTimeout(function() {
                        $('#district').val(selectedDistrict).trigger('change');
                    }, 100);
                }
            }

            // ============================================
            // LOAD VILLAGES (Kelurahan)
            // ============================================

            function loadVillages(districtCode, selectedVillage = null) {
                const cacheKey = CACHE_KEY_VILLAGES + districtCode;
                let cached = getCache(cacheKey);

                if (cached) {
                    console.log('Villages loaded from cache:', cached);
                    renderVillages(cached, selectedVillage);
                    return;
                }

                $('#subdistrict').html('<option value="">-- Memuat Kelurahan --</option>').prop('disabled', true);

                $.ajax({
                    url: '{{ route("api.villages") }}?district_code=' + districtCode,
                    method: 'GET',
                    timeout: 30000,
                    success: function(response) {
                        console.log('Villages API response:', response);

                        let villages = response;
                        if (response && response.data && Array.isArray(response.data)) {
                            villages = response.data;
                        }

                        if (!villages || villages.length === 0) {
                            console.warn('No villages found for district:', districtCode);
                            $('#subdistrict').html('<option value="">-- Data tidak tersedia --</option>').prop('disabled', false);
                            return;
                        }

                        console.log('Villages found:', villages.length);
                        setCache(cacheKey, villages);
                        renderVillages(villages, selectedVillage);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading villages:', {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
                        $('#subdistrict').html('<option value="">-- Gagal memuat --</option>').prop('disabled', false);
                    }
                });
            }

            function renderVillages(data, selectedVillage) {
                let options = '<option value="">-- Pilih Kelurahan --</option>';

                if (data && data.length > 0) {
                    $.each(data, function(index, village) {
                        const code = village.value;
                        const name = village.label;
                        const postalCode = village.postal_code || '';

                        if (code && name) {
                            options += `<option value="${name}" data-code="${code}" data-zip="${postalCode}">${name}${postalCode ? ' (' + postalCode + ')' : ''}</option>`;
                        }
                    });
                } else {
                    options += '<option value="">-- Data tidak tersedia --</option>';
                }

                $('#subdistrict').html(options).prop('disabled', false);

                if (selectedVillage) {
                    setTimeout(function() {
                        $('#subdistrict').val(selectedVillage).trigger('change');
                    }, 100);
                }
            }

            // ============================================
            // CHECK SHIPPING COST
            // ============================================

            function checkShippingCost(destinationPostalCode) {
                $('#shipping-cost-display')
                    .html('<span class="loading">⏳ Mencari ongkir...</span>');

                if (!destinationPostalCode || destinationPostalCode === '0' || destinationPostalCode === '') {
                    $('#shipping-cost-display')
                        .html('<span class="error">❌ Kode pos tujuan tidak tersedia</span>');
                    return;
                }

                const items = [];
                @foreach ($cart as $item)
                    items.push({
                        name: '{{ $item['product_name'] }}',
                        weight: {{ $item['weight'] ?? 1000 }},
                        quantity: {{ $item['quantity'] }},
                        price: {{ $item['price'] }}
                    });
                @endforeach

                console.log('=== CHECKING BITESHIP RATES ===');
                console.log('Destination Postal Code:', destinationPostalCode);
                console.log('Items:', items);

                $.ajax({
                    url: '{{ route("api.biteship.rates") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({
                        destination_postal_code: destinationPostalCode,
                        items: items,
                        couriers: ['jne', 'jnt', 'sicepat', 'pos', 'anteraja']
                    }),
                    success: function(response) {
                        console.log('Biteship Response:', response);

                        if (!response.success) {
                            let errorMsg = response.message || 'Gagal mendapatkan ongkir';
                            if (response.detail) {
                                console.error('Detail error:', response.detail);
                                errorMsg += ' - ' + JSON.stringify(response.detail);
                            }
                            
                            $('#courier')
                                .prop('disabled', true)
                                .html('<option value="">-- Kurir tidak tersedia --</option>');
                            $('#service')
                                .prop('disabled', true)
                                .html('<option value="">-- Pilih Layanan --</option>');
                            $('#shipping-cost-display').html(
                                '<span class="error">❌ ' + errorMsg + '</span>'
                            );
                            return;
                        }

                        if (!response.data || response.data.length === 0) {
                            $('#courier')
                                .prop('disabled', true)
                                .html('<option value="">-- Kurir tidak tersedia --</option>');
                            $('#service')
                                .prop('disabled', true)
                                .html('<option value="">-- Pilih Layanan --</option>');
                            $('#shipping-cost-display').html(
                                '<span class="error">❌ Tidak ada layanan pengiriman untuk rute ini</span>'
                            );
                            return;
                        }

                        const couriers = {};
                        let hasValidServices = false;
                        
                        $.each(response.data, function(index, courier) {
                            if (courier.services && courier.services.length > 0) {
                                const validServices = courier.services.filter(s => s.cost > 0);
                                if (validServices.length > 0) {
                                    couriers[courier.code] = {
                                        code: courier.code,
                                        name: courier.name,
                                        services: validServices
                                    };
                                    hasValidServices = true;
                                }
                            }
                        });

                        if (!hasValidServices) {
                            $('#courier')
                                .prop('disabled', true)
                                .html('<option value="">-- Kurir tidak tersedia --</option>');
                            $('#service')
                                .prop('disabled', true)
                                .html('<option value="">-- Pilih Layanan --</option>');
                            $('#shipping-cost-display').html(
                                '<span class="error">❌ Tidak ada layanan pengiriman dengan harga valid</span>'
                            );
                            return;
                        }

                        console.log('Available couriers:', couriers);

                        let courierOptions = '<option value="">-- Pilih Kurir --</option>';
                        $.each(couriers, function(code, courier) {
                            courierOptions += `<option value="${code}">${courier.name}</option>`;
                        });

                        $('#courier')
                            .html(courierOptions)
                            .prop('disabled', false)
                            .data('shipping-rates', couriers);

                        $('#service')
                            .prop('disabled', true)
                            .html('<option value="">-- Pilih Layanan --</option>');

                        $('#shipping-cost-display').html(
                            `<span class="success">✅ ${Object.keys(couriers).length} kurir tersedia</span>`
                        );

                        if (Object.keys(couriers).length === 1) {
                            const firstCourier = Object.keys(couriers)[0];
                            $('#courier').val(firstCourier).trigger('change');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Biteship AJAX Error:', {
                            status: status,
                            error: error,
                            response: xhr.responseText,
                            statusCode: xhr.status
                        });

                        let message = '❌ Gagal mendapatkan ongkir';
                        
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                message += ': ' + response.message;
                            }
                            if (response.detail) {
                                console.error('Detail error:', response.detail);
                            }
                        } catch(e) {
                            console.error('Error parsing response:', e);
                        }

                        if (xhr.status === 400) {
                            message += ' (Bad Request - periksa kode pos)';
                        } else if (xhr.status === 401) {
                            message += ' (API Key tidak valid)';
                        } else if (xhr.status === 404) {
                            message += ' (Rute tidak ditemukan)';
                        } else if (xhr.status === 500) {
                            message += ' (Server error)';
                        }

                        $('#courier')
                            .prop('disabled', true)
                            .html('<option value="">-- Gagal memuat kurir --</option>');
                        $('#service')
                            .prop('disabled', true)
                            .html('<option value="">-- Pilih Layanan --</option>');
                        $('#shipping-cost-display').html(`<span class="error">${message}</span>`);
                    }
                });
            }

            // ============================================
            // EVENT HANDLERS
            // ============================================

            // Province Change
            $('#province').on('change', function() {
                const provinceCode = $(this).find(':selected').data('code');

                $('#province_id').val(provinceCode);
                $('#city_id').val('');
                $('#district_id').val('');
                $('#subdistrict_id').val('');
                $('#shipping_cost').val(0);
                $('#shipping-cost-text').text('Rp 0');
                updateTotal();

                if (provinceCode) {
                    const selectedCity = defaultAddress?.city || null;
                    loadCities(provinceCode, selectedCity);
                } else {
                    $('#city').html('<option value="">-- Pilih Kota --</option>').prop('disabled', true);
                    $('#district').html('<option value="">-- Pilih Kecamatan --</option>').prop('disabled', true);
                    $('#subdistrict').html('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);
                }

                $('#courier').prop('disabled', true);
                $('#service').html('<option value="">-- Pilih Layanan --</option>');
                $('#shipping-cost-display').text('Pilih kurir dan kelurahan tujuan');
            });

            // City Change
            $('#city').on('change', function() {
                const cityCode = $(this).find(':selected').data('code');

                $('#city_id').val(cityCode);
                $('#district_id').val('');
                $('#subdistrict_id').val('');
                $('#shipping_cost').val(0);
                $('#shipping-cost-text').text('Rp 0');
                updateTotal();

                $('#district').html('<option value="">-- Pilih Kecamatan --</option>').prop('disabled', true);
                $('#subdistrict').html('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);

                if (cityCode) {
                    loadDistricts(cityCode);
                }

                $('#courier').prop('disabled', true);
                $('#service').html('<option value="">-- Pilih Layanan --</option>');
                $('#shipping-cost-display').text('Pilih kurir dan kelurahan tujuan');
            });

            // District Change
            $('#district').on('change', function() {
                const districtCode = $(this).find(':selected').data('code');

                $('#district_id').val(districtCode);
                $('#subdistrict_id').val('');
                $('#shipping_cost').val(0);
                $('#shipping-cost-text').text('Rp 0');
                updateTotal();

                $('#subdistrict').html('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);

                if (districtCode) {
                    loadVillages(districtCode);
                }

                $('#courier').prop('disabled', true);
                $('#service').html('<option value="">-- Pilih Layanan --</option>');
                $('#shipping-cost-display').text('Pilih kurir dan kelurahan tujuan');
            });

            // Subdistrict Change
            $('#subdistrict').on('change', function() {
                const selectedOption = $(this).find(':selected');
                const villageCode = selectedOption.attr('data-code') || '';
                const zipCode = selectedOption.attr('data-zip') || '';

                console.log('=== SUBDISTRICT SELECTED ===');
                console.log('Village Code:', villageCode);
                console.log('Postal Code:', zipCode);

                $('#subdistrict_id').val(villageCode);
                $('#shipping_postal_code').val(zipCode);

                $('#shipping_cost').val(0);
                $('#shipping-cost-text').text('Rp 0');
                updateTotal();

                $('#service')
                    .prop('disabled', true)
                    .html('<option value="">-- Pilih Layanan --</option>');

                if (!zipCode || zipCode === '0' || zipCode === '' || zipCode.length < 4) {
                    $('#courier')
                        .prop('disabled', true)
                        .html('<option value="">-- Kode pos tidak valid --</option>');
                    $('#shipping-cost-display').html(
                        '<span class="error">❌ Kode pos tujuan tidak valid</span>'
                    );
                    return;
                }

                $('#courier')
                    .prop('disabled', true)
                    .html('<option value="">⏳ Memuat Kurir...</option>');

                $('#shipping-cost-display').html(
                    '<span class="loading">⏳ Mencari layanan pengiriman...</span>'
                );

                const weight = calculateTotalWeight();

                if (!weight || weight <= 0) {
                    $('#courier')
                        .prop('disabled', true)
                        .html('<option value="">-- Kurir Tidak Tersedia --</option>');
                    $('#shipping-cost-display').html(
                        '<span class="error">❌ Berat produk belum tersedia</span>'
                    );
                    return;
                }

                console.log('=== REQUEST ONGKIR BITESHIP ===');
                console.log('Origin Postal Code:', '{{ config('services.biteship.origin.postal_code', '46196') }}');
                console.log('Destination Postal Code:', zipCode);
                console.log('Weight:', weight);

                checkShippingCost(zipCode);
            });

            // Courier Change
            $('#courier').on('change', function() {
                const selectedCourier = $(this).val();

                console.log('=== COURIER SELECTED ===');
                console.log('Courier:', selectedCourier);

                $('#service')
                    .prop('disabled', true)
                    .html('<option value="">-- Memuat Layanan --</option>');

                $('#shipping_cost').val(0);
                $('#shipping-cost-text').text('Rp 0');
                updateTotal();

                if (!selectedCourier) {
                    $('#service')
                        .html('<option value="">-- Pilih Layanan --</option>')
                        .prop('disabled', true);
                    $('#shipping-cost-display').html('Pilih kurir');
                    return;
                }

                const couriers = $('#courier').data('shipping-rates') || {};
                const selectedCourierData = couriers[selectedCourier];

                if (!selectedCourierData || !selectedCourierData.services || !selectedCourierData.services.length) {
                    $('#service')
                        .html('<option value="">-- Layanan Tidak Tersedia --</option>')
                        .prop('disabled', true);
                    $('#shipping-cost-display').html(
                        '<span class="error">❌ Layanan kurir tidak tersedia</span>'
                    );
                    return;
                }

                let serviceOptions = '<option value="">-- Pilih Layanan --</option>';
                $.each(selectedCourierData.services, function(index, service) {
                    const serviceName = service.service || '';
                    const description = service.description || '';
                    const cost = parseInt(service.cost, 10) || 0;
                    const etd = service.etd || '-';

                    serviceOptions += `
                        <option value="${serviceName}" data-cost="${cost}" data-etd="${etd}">
                            ${serviceName} ${description ? '- ' + description : ''} - Rp ${formatNumber(cost)} (${etd} hari)
                        </option>
                    `;
                });

                $('#service')
                    .html(serviceOptions)
                    .prop('disabled', false);

                $('#shipping-cost-display').html(
                    `<span class="success">✅ ${selectedCourierData.services.length} layanan tersedia</span>`
                );
            });

            // Service Change
            $('#service').on('change', function() {
                const selected = $(this).find(':selected');
                const cost = parseInt(selected.data('cost')) || 0;
                const etd = selected.data('etd') || '-';
                const serviceName = selected.val();

                console.log('=== SERVICE SELECTED ===');
                console.log('Service:', serviceName);
                console.log('Cost:', cost);
                console.log('ETD:', etd);

                if (!serviceName || cost <= 0) {
                    $('#shipping_cost').val(0);
                    $('#shipping-cost-text').text('Rp 0');
                    updateTotal();
                    return;
                }

                $('#shipping_cost').val(cost);
                $('#shipping-cost-text').text('Rp ' + formatNumber(cost));

                $('#shipping-cost-display').html(`
                    <div class="success">
                        <div style="font-weight:600;">${serviceName}</div>
                        <div style="font-size:0.65vw;color:#94a3b8;">Estimasi: ${etd} hari</div>
                    </div>
                `);

                updateTotal();
            });

            // ============================================
            // INIT - Load Provinces & Auto Fill
            // ============================================

            loadProvinces();

            if (defaultAddress) {
                console.log('Auto-fill address for logged-in user:', defaultAddress);

                function autoFillAddress() {
                    if (defaultAddress.province) {
                        $('#province option').each(function() {
                            if ($(this).text().trim() === defaultAddress.province) {
                                $(this).prop('selected', true);
                                $('#province').trigger('change');
                                return false;
                            }
                        });

                        setTimeout(function() {
                            if (defaultAddress.city) {
                                $('#city option').each(function() {
                                    if ($(this).text().trim() === defaultAddress.city) {
                                        $(this).prop('selected', true);
                                        $('#city').trigger('change');
                                        return false;
                                    }
                                });
                            }

                            setTimeout(function() {
                                if (defaultAddress.district) {
                                    $('#district option').each(function() {
                                        if ($(this).text().trim() === defaultAddress.district) {
                                            $(this).prop('selected', true);
                                            $('#district').trigger('change');
                                            return false;
                                        }
                                    });
                                }

                                setTimeout(function() {
                                    if (defaultAddress.subdistrict) {
                                        $('#subdistrict option').each(function() {
                                            if ($(this).text().trim() === defaultAddress.subdistrict) {
                                                $(this).prop('selected', true);
                                                $('#subdistrict').trigger('change');
                                                return false;
                                            }
                                        });
                                    }

                                    if (defaultAddress.postal_code) {
                                        $('#shipping_postal_code').val(defaultAddress.postal_code);
                                    }

                                    console.log('Auto-fill completed');
                                }, 800);
                            }, 800);
                        }, 800);
                    }
                }

                let attempts = 0;
                const maxAttempts = 10;
                const autoFillInterval = setInterval(function() {
                    attempts++;
                    if ($('#province option').length > 1) {
                        clearInterval(autoFillInterval);
                        autoFillAddress();
                    } else if (attempts >= maxAttempts) {
                        clearInterval(autoFillInterval);
                        console.log('Auto-fill timeout, trying once more');
                        autoFillAddress();
                    }
                }, 500);
            }

            // ============================================
            // SAVE TO LOCALSTORAGE (for guest)
            // ============================================

            function saveCheckoutData() {
                const data = {
                    shipping_name: $('#shipping_name').val(),
                    shipping_phone: $('#shipping_phone').val(),
                    shipping_address: $('#shipping_address').val(),
                    shipping_province: $('#province').val(),
                    shipping_city: $('#city').val(),
                    shipping_district: $('#district').val(),
                    shipping_subdistrict: $('#subdistrict').val(),
                    shipping_postal_code: $('#shipping_postal_code').val(),
                    saved_at: new Date().toISOString()
                };

                try {
                    localStorage.setItem('checkout_data', JSON.stringify(data));
                } catch(e) {}
            }

            $(document).on('change', '#shipping_name, #shipping_phone, #shipping_address, #province, #city, #district, #subdistrict', function() {
                if (!isLoggedIn) {
                    saveCheckoutData();
                }
            });

            function restoreGuestData() {
                if (isLoggedIn) return;

                try {
                    const saved = localStorage.getItem('checkout_data');
                    if (saved) {
                        const data = JSON.parse(saved);
                        const savedDate = new Date(data.saved_at);
                        const now = new Date();
                        const diffDays = (now - savedDate) / (1000 * 60 * 60 * 24);

                        if (diffDays < 7) {
                            $('#shipping_name').val(data.shipping_name || '');
                            $('#shipping_phone').val(data.shipping_phone || '');
                            $('#shipping_address').val(data.shipping_address || '');

                            if (data.shipping_province) {
                                setTimeout(() => {
                                    $('#province').val(data.shipping_province).trigger('change');
                                }, 1500);
                            }
                        }
                    }
                } catch(e) {}
            }

            if (!isLoggedIn) {
                setTimeout(restoreGuestData, 2000);
            }

            // ============================================
            // UTILITY FUNCTIONS
            // ============================================

            function calculateTotalWeight() {
                let totalWeight = 0;
                @foreach ($cart as $item)
                    totalWeight += {{ $item['weight'] ?? 1000 }} * {{ $item['quantity'] }};
                @endforeach
                $('#total-weight').text(totalWeight);
                return totalWeight;
            }

            function updateTotal() {
                const shippingCost = parseInt($('#shipping_cost').val()) || 0;
                const total = subtotal + shippingCost;
                $('#total-display').text('Rp ' + formatNumber(total));
            }

            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // ============================================
            // INIT
            // ============================================

            calculateTotalWeight();
            $('#courier').prop('disabled', true);

        });
    </script>

@endsection