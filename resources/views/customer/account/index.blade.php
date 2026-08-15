<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">

    {{-- NAVBAR SAMA SEPERTI SEBELUMNYA --}}
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
            <a href="{{ route('customer.home') }}" class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 font-bold text-white">
                    {{ strtoupper(substr(config('app.name'), 0, 1)) }}
                </div>
                <span class="font-bold text-slate-900">{{ config('app.name') }}</span>
            </a>

            <div class="flex items-center gap-4">
                <span class="hidden text-sm font-medium text-slate-600 sm:block">
                    {{ auth('customer')->user()->name }}
                </span>

                <form action="{{ route('customer.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-6 py-10 lg:px-8">

        {{-- HEADER --}}
        <div class="mb-8">
            <p class="text-sm font-medium text-blue-600">Akun Saya</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                Halo, {{ auth('customer')->user()->name }} 👋
            </h1>
            <p class="mt-2 text-sm text-slate-500">Kelola informasi akun dan aktivitas belanja Anda.</p>
        </div>

        {{-- FLASH MESSAGES --}}
        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- SIDEBAR NAV --}}
            <div class="space-y-2">
                <a href="{{ route('customer.account') }}" class="block rounded-xl bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-600">
                    📋 Profil Saya
                </a>
                <a href="{{ route('customer.orders') }}" class="block rounded-xl px-4 py-3 text-sm text-slate-600 transition hover:bg-slate-100">
                    📦 Riwayat Pesanan
                </a>
                <a href="{{ route('customer.addresses.create') }}" class="block rounded-xl px-4 py-3 text-sm text-slate-600 transition hover:bg-slate-100">
                    ➕ Tambah Alamat
                </a>
            </div>

            {{-- MAIN CONTENT --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- PROFILE CARD --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="font-bold text-slate-900">Informasi Profil</h2>

                    <div class="mt-4 space-y-4">
                        <div class="border-b border-slate-100 pb-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Nama Lengkap</p>
                            <p class="mt-1 font-medium text-slate-800">{{ auth('customer')->user()->name }}</p>
                        </div>

                        <div class="border-b border-slate-100 pb-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Email</p>
                            <p class="mt-1 font-medium text-slate-800">{{ auth('customer')->user()->email }}</p>
                        </div>

                        <div class="pb-2">
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Nomor Telepon</p>
                            <p class="mt-1 font-medium text-slate-800">{{ auth('customer')->user()->phone ?: '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-xl bg-emerald-50 p-4">
                        <div class="flex items-center gap-3">
                            <div class="h-3 w-3 rounded-full bg-emerald-500"></div>
                            <span class="font-semibold text-emerald-700">Akun Aktif</span>
                        </div>
                        <p class="mt-2 text-sm text-emerald-600">Akun Anda aktif dan dapat digunakan untuk melakukan pembelian.</p>
                    </div>
                </div>

                {{-- ADDRESS LIST --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-bold text-slate-900">📍 Daftar Alamat</h2>
                        <a href="{{ route('customer.addresses.create') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                            + Tambah
                        </a>
                    </div>

                    @if ($addresses->isEmpty())
                        <p class="text-sm text-slate-500">Kamu belum memiliki alamat tersimpan.</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($addresses as $address)
                                <div class="rounded-xl border border-slate-100 p-4 {{ $address->is_default ? 'border-blue-200 bg-blue-50' : '' }}">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-slate-800">
                                                    {{ $address->label ?: 'Alamat' }}
                                                </span>
                                                @if ($address->is_default)
                                                    <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">Utama</span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-sm text-slate-600">{{ $address->recipient_name }}</p>
                                            <p class="text-sm text-slate-600">{{ $address->recipient_phone }}</p>
                                            <p class="mt-1 text-sm text-slate-500">
                                                {{ $address->address }}, {{ $address->city }}, {{ $address->province }} - {{ $address->postal_code }}
                                            </p>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('customer.addresses.edit', $address) }}" class="text-sm text-blue-600 hover:text-blue-700">Edit</a>
                                            <form action="{{ route('customer.addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm text-red-500 hover:text-red-600">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

        </div>

    </main>

</body>

</html>