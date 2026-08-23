<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Admin E-Commerce')
    </title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>

    <script src="https://cdn.tiny.cloud/1/f0qff2j87jgv24lrb8m0hd4yuglweewk56pa79tykafgtc6g/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>


<body class="bg-gray-100 text-gray-900">


    <div class="min-h-screen">


        {{-- Mobile Overlay --}}
        <div
            id="sidebarOverlay"
            class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"
        ></div>


        {{-- Sidebar --}}
        <aside
            id="sidebar"
            class="fixed inset-y-0 left-0 z-50
                   w-64 bg-white border-r border-gray-200
                   transform -translate-x-full
                   lg:translate-x-0
                   transition-transform duration-300"
        >

            {{-- Logo --}}
            <div
                class="h-16 flex items-center px-6
                       border-b border-gray-200"
            >

                <a
                    href="{{ route('admin.dashboard') }}"
                    class="text-xl font-bold text-blue-600"
                >
                    E-Commerce
                </a>

            </div>


            {{-- Navigation --}}
            <nav class="p-4 space-y-1">


                {{-- Dashboard --}}
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3
                           rounded-lg text-sm font-medium
                           text-gray-700 hover:bg-blue-50
                           hover:text-blue-600"
                >

                    <span>📊</span>

                    <span>
                        Dashboard
                    </span>

                </a>


                {{-- Kategori --}}
                <a
                    href="{{ route('admin.categories.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >

                    <span>📁</span>

                    <span>
                        Kategori
                    </span>

                </a>


                {{-- Produk --}}
                <a
                    href="{{ route('admin.products.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >
                    <span>📦</span>

                    <span>
                        Produk
                    </span>
                </a>

                <a href="{{ route('admin.stock.index') }}"
                    class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.stock.*') ? 'bg-gray-200 text-gray-900' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Manajemen Stok
                    <span class="ml-auto">
                        <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">
                            {{ \App\Models\Product::where('is_active', true)->criticalStock()->count() }}
                        </span>
                    </span>
                </a>

                <a
                    href="{{ route('admin.articles.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >

                    <span>
                        Artikel
                    </span>
                </a>

                <a
                    href="{{ route('admin.faqs.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >

                    <span>
                        FAQ
                    </span>
                </a>
                <a
                    href="{{ route('admin.terms.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >

                    <span>
                        Syarat & Ketentuan
                    </span>
                </a>
                <a
                    href="{{ route('admin.privacy.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >

                    <span>
                        Kebijakan Privasi
                    </span>
                </a>

                <a href="{{ route('admin.about.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600">
                    <span>🏢</span>
                    <span>Tentang Kami</span>
                </a>

                <a
                    href="{{ route('admin.banners.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >

                    <span>
                        Banner
                    </span>
                </a>

                <a
                    href="{{ route('admin.settings.edit') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >

                    <span>
                        Pengaturan Toko
                    </span>
                </a>

                <a
                    href="{{ route('admin.marketplaces.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >

                    <span>
                        Marketplace
                    </span>
                </a>


                {{-- Pesanan --}}
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                           rounded-lg text-sm font-medium
                           text-gray-700 hover:bg-blue-50
                           hover:text-blue-600"
                >

                    <span>🛒</span>

                    <span>
                        Pesanan
                    </span>

                </a>


                {{-- Pelanggan --}}
                <a
                    href="#"
                    class="flex items-center gap-3 px-4 py-3
                           rounded-lg text-sm font-medium
                           text-gray-700 hover:bg-blue-50
                           hover:text-blue-600"
                >

                    <span>👤</span>

                    <span>
                        Pelanggan
                    </span>

                </a>


            </nav>


            {{-- Bottom Sidebar --}}
            <div
                class="absolute bottom-0 left-0 right-0
                       p-4 border-t border-gray-200"
            >

                <div class="mb-3 px-4">

                    <p class="text-xs text-gray-500">
                        Login sebagai
                    </p>

                    <p class="text-sm font-semibold">
                        {{ auth()->user()->name }}
                    </p>

                </div>


                <form
                    action="{{ route('logout') }}"
                    method="POST"
                >

                    @csrf

                    <button
                        type="submit"
                        class="w-full flex items-center gap-3
                               px-4 py-3 rounded-lg
                               text-sm font-medium
                               text-red-600 hover:bg-red-50"
                    >

                        <span>🚪</span>

                        <span>
                            Logout
                        </span>

                    </button>

                </form>

            </div>

        </aside>


        {{-- Main Content --}}
        <div class="lg:ml-64">


            {{-- Header --}}
            <header
                class="h-16 bg-white border-b border-gray-200
                       flex items-center justify-between
                       px-4 lg:px-8"
            >

                {{-- Mobile Menu --}}
                <button
                    id="menuButton"
                    type="button"
                    class="lg:hidden p-2 rounded-lg
                           hover:bg-gray-100"
                >

                    ☰

                </button>


                {{-- Page Title --}}
                <h1 class="text-lg font-semibold">

                    @yield('page-title', 'Dashboard')

                </h1>


                {{-- User --}}
                <div class="flex items-center gap-3">

                    <div
                        class="w-9 h-9 rounded-full
                               bg-blue-100 text-blue-600
                               flex items-center justify-center
                               font-bold"
                    >

                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                    </div>

                </div>

            </header>


            {{-- Page Content --}}
            <main class="p-4 lg:p-8">

                @yield('content')

            </main>


        </div>


    </div>

    @stack('scripts')


    {{-- Vanilla JavaScript --}}
    <script>

        const sidebar = document.getElementById('sidebar');

        const overlay = document.getElementById('sidebarOverlay');

        const menuButton = document.getElementById('menuButton');


        function openSidebar() {

            sidebar.classList.remove('-translate-x-full');

            overlay.classList.remove('hidden');

        }


        function closeSidebar() {

            sidebar.classList.add('-translate-x-full');

            overlay.classList.add('hidden');

        }


        menuButton.addEventListener(
            'click',
            openSidebar
        );


        overlay.addEventListener(
            'click',
            closeSidebar
        );

    </script>


</body>

</html>