<footer class="bg-white border-t border-slate-200 mt-12">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 gap-8 md:grid-cols-4">

            {{-- Brand --}}
            <div class="col-span-1 md:col-span-1">
                <h3 class="text-lg font-bold text-slate-900">{{ config('app.name') }}</h3>
                <p class="mt-2 text-sm text-slate-500 max-w-xs">
                    Toko online terpercaya untuk kebutuhan fashion muslimah modern, elegan, dan syar'i.
                </p>
                <div class="mt-4 flex gap-3">
                    <a href="#" class="text-slate-400 hover:text-blue-600 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.989C18.343 21.129 22 16.99 22 12z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-blue-600 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.104c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 0021.617-11.831c0-.213-.005-.425-.015-.637A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-blue-600 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-blue-600 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21.428 4.048a2.5 2.5 0 00-1.788-1.682C18.308 2.003 15.346 2 12 2s-6.308.003-7.64.366A2.5 2.5 0 002.572 4.05C2.207 5.381 2 8.335 2 12c0 3.665.207 6.619.572 7.95.217.754.865 1.367 1.788 1.682 1.332.363 4.294.366 7.64.366s6.308-.003 7.64-.366a2.5 2.5 0 001.788-1.682c.365-1.331.572-4.285.572-7.95 0-3.665-.207-6.619-.572-7.95zM10 15.5v-7l6 3.5-6 3.5z"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h4 class="text-sm font-semibold text-slate-900 uppercase tracking-wider">Toko</h4>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('customer.products.index') }}" class="text-slate-500 hover:text-blue-600 transition">Semua Produk</a></li>
                    <li><a href="#" class="text-slate-500 hover:text-blue-600 transition">Promo</a></li>
                    <li><a href="#" class="text-slate-500 hover:text-blue-600 transition">Produk Terbaru</a></li>
                    <li><a href="#" class="text-slate-500 hover:text-blue-600 transition">Produk Unggulan</a></li>
                </ul>
            </div>

            {{-- Info --}}
            <div>
                <h4 class="text-sm font-semibold text-slate-900 uppercase tracking-wider">Informasi</h4>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="#" class="text-slate-500 hover:text-blue-600 transition">Tentang Kami</a></li>
                    <li><a href="#" class="text-slate-500 hover:text-blue-600 transition">Cara Pemesanan</a></li>
                    <li><a href="#" class="text-slate-500 hover:text-blue-600 transition">Syarat & Ketentuan</a></li>
                    <li><a href="#" class="text-slate-500 hover:text-blue-600 transition">Kebijakan Privasi</a></li>
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <h4 class="text-sm font-semibold text-slate-900 uppercase tracking-wider">Hubungi Kami</h4>
                <ul class="mt-4 space-y-2 text-sm">
                    <li class="flex items-center gap-2 text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span>+62 812 2288 425</span>
                    </li>
                    <li class="flex items-center gap-2 text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>barokahsport0@gmail.com</span>
                    </li>
                    <li class="flex items-center gap-2 text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Tasikmalaya, Jawa Barat</span>
                    </li>
                </ul>
            </div>

        </div>

        {{-- Bottom Bar --}}
        <div class="mt-8 border-t border-slate-200 pt-6 flex flex-col sm:flex-row items-center justify-between text-sm text-slate-400">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <div class="flex items-center gap-4 mt-2 sm:mt-0">
                <span>Dibuat dengan ❤️</span>
                <span class="text-xs">v1.0.0</span>
            </div>
        </div>

    </div>
</footer>