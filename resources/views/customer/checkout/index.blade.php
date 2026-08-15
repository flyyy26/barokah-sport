<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        select:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

    @include('customer.partials.navbar')

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">📦 Checkout</h1>
            <p class="mt-1 text-sm text-slate-500">Lengkapi data untuk menyelesaikan pesanan.</p>
        </div>

        @if (session('error'))
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-medium">Terjadi kesalahan:</p>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Form --}}
            <div class="lg:col-span-2">
                <form action="{{ route('customer.checkout.process') }}" method="POST" id="checkout-form">
                    @csrf

                    {{-- Informasi Pengiriman --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 mb-6">
                        <h2 class="font-semibold text-slate-900">📍 Informasi Pengiriman</h2>
                        <p class="text-sm text-slate-500 mb-4">Isi data penerima paket.</p>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                            {{-- Nama Penerima --}}
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-slate-700">Nama Penerima <span class="text-red-500">*</span></label>
                                <input type="text" name="shipping_name" id="shipping_name" 
                                    value="{{ old('shipping_name', $defaultAddress->recipient_name ?? $customer->name ?? '') }}" 
                                    required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                @error('shipping_name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            {{-- Nomor WhatsApp --}}
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-slate-700">Nomor WhatsApp <span class="text-red-500">*</span></label>
                                <input type="tel" name="shipping_phone" id="shipping_phone" 
                                    value="{{ old('shipping_phone', $defaultAddress->recipient_phone ?? $customer->phone ?? '') }}" 
                                    placeholder="08xxxxxxxxxx" required
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                @error('shipping_phone') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            {{-- Alamat Lengkap --}}
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-slate-700">Alamat Lengkap <span class="text-red-500">*</span></label>
                                <textarea name="shipping_address" id="shipping_address" rows="3" required
                                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">{{ old('shipping_address', $defaultAddress->address ?? '') }}</textarea>
                                @error('shipping_address') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                                <p class="mt-1 text-xs text-slate-400">Contoh: Jalan Mawar No. 10, RT 01 RW 02</p>
                            </div>

                            {{-- Provinsi --}}
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Provinsi <span class="text-red-500">*</span></label>
                                <select name="shipping_province" id="province" required
                                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    <option value="">-- Pilih Provinsi --</option>
                                </select>
                                <input type="hidden" name="shipping_province_id" id="province_id">
                                @error('shipping_province') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            {{-- Kota/Kabupaten --}}
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Kota/Kabupaten <span class="text-red-500">*</span></label>
                                <select name="shipping_city" id="city" required
                                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    <option value="">-- Pilih Kota --</option>
                                </select>
                                <input type="hidden" name="shipping_city_id" id="city_id">
                                @error('shipping_city') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            {{-- Kecamatan --}}
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Kecamatan <span class="text-red-500">*</span></label>
                                <select name="shipping_district" id="district" required
                                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                                <input type="hidden" name="shipping_district_id" id="district_id">
                                @error('shipping_district') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            {{-- Kelurahan --}}
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Kelurahan <span class="text-red-500">*</span></label>
                                <select name="shipping_subdistrict" id="subdistrict" required
                                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    <option value="">-- Pilih Kelurahan --</option>
                                </select>
                                <input type="hidden" name="shipping_subdistrict_id" id="subdistrict_id">
                                @error('shipping_subdistrict') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            {{-- 🔥 HIDDEN FIELD untuk shipping_postal_code --}}
                            <input type="hidden" name="shipping_postal_code" id="shipping_postal_code" value="0">

                        </div>
                    </div>

                    {{-- Ekspedisi & Ongkir --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 mb-6">
                        <h2 class="font-semibold text-slate-900">🚚 Ekspedisi & Ongkir</h2>
                        <p class="text-sm text-slate-500 mb-4">Pilih kurir dan lihat estimasi ongkir.</p>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            {{-- Kurir --}}
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Kurir <span class="text-red-500">*</span></label>
                                <select name="courier" id="courier" required disabled>
                                    <option value="">-- Pilih Kurir --</option>
                                </select>
                            </div>

                            {{-- Layanan --}}
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Layanan</label>
                                <select name="shipping_service" id="service" 
                                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                                    <option value="">-- Pilih Layanan --</option>
                                </select>
                            </div>

                            {{-- Estimasi Ongkir --}}
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Estimasi Ongkir</label>
                                <div id="shipping-cost-display" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                                    Pilih kurir dan kota tujuan
                                </div>
                                <input type="hidden" name="shipping_cost" id="shipping_cost" value="0">
                            </div>
                        </div>

                        {{-- Berat Total --}}
                        <div class="mt-3 text-sm text-slate-500">
                            <span>Berat total: </span>
                            <span id="total-weight" class="font-medium text-slate-700">0</span>
                            <span> gram</span>
                            <span class="text-xs text-slate-400 ml-2">
                                ({{ count($cart) }} produk)
                            </span>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 mb-6">
                        <h2 class="font-semibold text-slate-900">📝 Catatan</h2>
                        <textarea name="notes" rows="3" class="mt-4 block w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500" 
                                  placeholder="Tambahkan catatan untuk pesanan (opsional)">{{ old('notes') }}</textarea>
                    </div>

                    {{-- Payment Method --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6">
                        <h2 class="font-semibold text-slate-900">💳 Metode Pembayaran</h2>
                        <div class="mt-4 space-y-3">
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-4 hover:border-blue-300 transition">
                                <input type="radio" name="payment_method" value="transfer" checked class="h-4 w-4 text-blue-600">
                                <div>
                                    <span class="font-medium text-slate-700">Transfer Bank (Manual)</span>
                                    <p class="text-xs text-slate-400">Pembayaran akan dikonfirmasi oleh admin.</p>
                                </div>
                            </label>
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-4 hover:border-blue-300 transition opacity-50">
                                <input type="radio" name="payment_method" value="qris" disabled class="h-4 w-4 text-blue-600">
                                <div>
                                    <span class="font-medium text-slate-700">QRIS <span class="text-xs text-slate-400">(Segera)</span></span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Info Guest --}}
                    @guest('customer')
                        <div class="mt-4 rounded-xl bg-blue-50 p-4 text-sm text-blue-700">
                            <p class="font-medium">💡 Akun akan dibuat otomatis</p>
                            <p class="mt-1">Kamu akan login otomatis dengan nomor WhatsApp. Password default: <strong>6 digit terakhir nomor WhatsApp</strong>.</p>
                            <p class="mt-1 text-xs text-blue-600">Kamu bisa mengganti password nanti di halaman profil.</p>
                        </div>
                    @endguest

                </form>
            </div>

            {{-- Summary --}}
            <div class="lg:col-span-1">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 sticky top-24">

                    <h2 class="text-lg font-bold text-slate-900">Ringkasan Pesanan</h2>

                    {{-- Items Preview --}}
                    <div class="mt-4 space-y-2 max-h-48 overflow-y-auto">
                        @foreach ($cart as $item)
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex-1">
                                    <p class="font-medium text-slate-700 line-clamp-1">{{ $item['product_name'] }}</p>
                                    @if ($item['variant_name'])
                                        <p class="text-xs text-slate-400">{{ $item['variant_name'] }}</p>
                                    @endif
                                    <p class="text-xs text-slate-400">{{ $item['quantity'] }}x</p>
                                </div>
                                <span class="font-medium text-slate-900 ml-2">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 border-t border-slate-200 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Subtotal</span>
                            <span class="font-medium text-slate-900" id="subtotal-display">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between" id="shipping-summary">
                            <span class="text-slate-500">Ongkir</span>
                            <span class="font-medium text-slate-900" id="shipping-cost-text">Rp 0</span>
                        </div>
                        <div class="border-t border-slate-200 pt-2 flex justify-between font-bold text-base">
                            <span>Total</span>
                            <span class="text-slate-900" id="total-display">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit" form="checkout-form" 
                            class="mt-6 block w-full rounded-xl bg-slate-900 py-3.5 text-center font-semibold text-white transition hover:bg-slate-800">
                        Buat Pesanan
                    </button>

                    <a href="{{ route('customer.cart.index') }}" class="mt-3 block text-center text-sm text-slate-500 hover:text-slate-700">
                        ← Kembali ke Keranjang
                    </a>
                </div>
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
        // CHECK SHIPPING COST DENGAN RAJAONGKIR
        // ============================================

        function checkShippingCost(destinationPostalCode) {
            $('#shipping-cost-display')
                .html('<span class="text-blue-500">⏳ Mencari ongkir...</span>')
                .addClass('loading');

            if (!destinationPostalCode || destinationPostalCode === '0' || destinationPostalCode === '') {
                $('#shipping-cost-display')
                    .removeClass('loading')
                    .html('<span class="text-red-500">❌ Kode pos tujuan tidak tersedia</span>');
                return;
            }

            // Ambil items dari cart
            const items = [];
            @foreach ($cart as $item)
                items.push({
                    name: '{{ $item['product_name'] }}',
                    weight: {{ $item['weight'] ?? 1000 }},
                    quantity: {{ $item['quantity'] }},
                    price: {{ $item['price'] }}
                });
            @endforeach

            console.log('=== CHECKING BITESHIP RATES (POSTAL CODE) ===');
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
                    $('#shipping-cost-display').removeClass('loading');

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
                            '<span class="text-red-500">❌ ' + errorMsg + '</span>'
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
                            '<span class="text-red-500">❌ Tidak ada layanan pengiriman untuk rute ini</span>'
                        );
                        return;
                    }

                    // Format data kurir
                    const couriers = {};
                    let hasValidServices = false;
                    
                    $.each(response.data, function(index, courier) {
                        if (courier.services && courier.services.length > 0) {
                            // Filter services dengan cost > 0
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
                            '<span class="text-red-500">❌ Tidak ada layanan pengiriman dengan harga valid</span>'
                        );
                        return;
                    }

                    console.log('Available couriers:', couriers);

                    // Render kurir options
                    let courierOptions = '<option value="">-- Pilih Kurir --</option>';
                    $.each(couriers, function(code, courier) {
                        courierOptions += `<option value="${code}">${courier.name}</option>`;
                    });

                    $('#courier')
                        .html(courierOptions)
                        .prop('disabled', false)
                        .data('shipping-rates', couriers);

                    // Reset service
                    $('#service')
                        .prop('disabled', true)
                        .html('<option value="">-- Pilih Layanan --</option>');

                    $('#shipping-cost-display').html(
                        `<span class="text-emerald-600">✅ ${Object.keys(couriers).length} kurir tersedia</span>`
                    );

                    // Auto-select first courier if only one available
                    if (Object.keys(couriers).length === 1) {
                        const firstCourier = Object.keys(couriers)[0];
                        $('#courier').val(firstCourier).trigger('change');
                    }
                },
                error: function(xhr, status, error) {
                    $('#shipping-cost-display').removeClass('loading');
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

                    // Tambahkan informasi status code
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
                    $('#shipping-cost-display').html(`<span class="text-red-500">${message}</span>`);
                }
            });
        }

        function updateShippingCost(cost, serviceName, etd) {

            cost = parseInt(cost) || 0;

            $('#shipping_cost').val(cost);

            $('#shipping-cost-text').text(
                'Rp ' + formatNumber(cost)
            );

            $('#shipping-cost-display').html(`
                <div class="text-emerald-600">
                    <div class="font-semibold">
                        ${serviceName}
                    </div>
                    <div class="text-xs text-slate-500">
                        Estimasi: ${etd}
                    </div>
                </div>
            `);

            updateTotal();
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

        // ============================================
        // SUBDISTRICT CHANGE
        // ============================================

        $('#subdistrict').on('change', function() {

            const selectedOption = $(this).find(':selected');

            // 🔥 Ambil kode wilayah (contoh: '32.02.31.2003')
            const villageCode = selectedOption.attr('data-code') || '';
            const zipCode = selectedOption.attr('data-zip') || '';

            console.log('=== SUBDISTRICT SELECTED ===');
            console.log('Village Code:', villageCode);
            console.log('Postal Code:', zipCode);

            // Simpan ID kelurahan
            $('#subdistrict_id').val(villageCode);
            $('#shipping_postal_code').val(zipCode);

            // Reset ongkir
            $('#shipping_cost').val(0);
            $('#shipping-cost-text').text('Rp 0');
            updateTotal();

            // Reset service
            $('#service')
                .prop('disabled', true)
                .html('<option value="">-- Pilih Layanan --</option>');

            // ============================================
            // VALIDASI KODE POS
            // ============================================

            if (!zipCode || zipCode === '0' || zipCode === '' || zipCode.length < 4) {
                $('#courier')
                    .prop('disabled', true)
                    .html('<option value="">-- Kode pos tidak valid --</option>');

                $('#shipping-cost-display').html(
                    '<span class="text-red-500">❌ Kode pos tujuan tidak valid</span>'
                );

                return;
            }

            // ============================================
            // RESET COURIER
            // ============================================

            $('#courier')
                .prop('disabled', true)
                .html('<option value="">⏳ Memuat Kurir...</option>');

            $('#shipping-cost-display').html(
                '<span class="text-blue-500">⏳ Mencari layanan pengiriman...</span>'
            );

            // ============================================
            // BERAT TOTAL
            // ============================================

            const weight = calculateTotalWeight();

            if (!weight || weight <= 0) {
                $('#courier')
                    .prop('disabled', true)
                    .html('<option value="">-- Kurir Tidak Tersedia --</option>');

                $('#shipping-cost-display').html(
                    '<span class="text-red-500">❌ Berat produk belum tersedia</span>'
                );

                return;
            }

            // ============================================
            // PANGGIL BITESHIP DENGAN POSTAL CODE
            // ============================================

            console.log('=== REQUEST ONGKIR BITESHIP ===');
            console.log('Origin Postal Code:', '{{ config('services.biteship.origin.postal_code', '46196') }}');
            console.log('Destination Postal Code:', zipCode);
            console.log('Weight:', weight);

            checkShippingCost(zipCode);

        });

        // ============================================
        // COURIER CHANGE
        // ============================================

        $('#courier').on('change', function() {
            const selectedCourier = $(this).val();

            console.log('=== COURIER SELECTED ===');
            console.log('Courier:', selectedCourier);

            // Reset service
            $('#service')
                .prop('disabled', true)
                .html('<option value="">-- Memuat Layanan --</option>');

            // Reset ongkir
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

            // Ambil data ongkir yang sudah didapat
            const couriers = $('#courier').data('shipping-rates') || {};
            const selectedCourierData = couriers[selectedCourier];

            if (!selectedCourierData || !selectedCourierData.services || !selectedCourierData.services.length) {
                $('#service')
                    .html('<option value="">-- Layanan Tidak Tersedia --</option>')
                    .prop('disabled', true);
                $('#shipping-cost-display').html(
                    '<span class="text-red-500">❌ Layanan kurir tidak tersedia</span>'
                );
                return;
            }

            // Tampilkan service
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
                `<span class="text-emerald-600">✅ ${selectedCourierData.services.length} layanan tersedia</span>`
            );
        });

        // ============================================
        // SERVICE CHANGE
        // ============================================

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

            // Update shipping cost
            $('#shipping_cost').val(cost);
            $('#shipping-cost-text').text('Rp ' + formatNumber(cost));

            $('#shipping-cost-display').html(`
                <div class="text-emerald-600">
                    <div class="font-semibold">${serviceName}</div>
                    <div class="text-xs text-slate-500">Estimasi: ${etd} hari</div>
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
        // SAVE TO LOCALSTORAGE (untuk guest)
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

</body>
</html>