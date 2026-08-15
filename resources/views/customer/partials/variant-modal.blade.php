{{-- Modal Pilih Varian --}}
<div id="variant-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden" 
     onclick="closeVariantModal(event)">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
            <h3 class="text-lg font-bold text-slate-900">Pilih Varian</h3>
            <button type="button" onclick="closeVariantModal()" 
                    class="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Product Info --}}
        <div class="mt-4 flex items-center gap-4">
            <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-lg bg-slate-100">
                <img id="modal-product-image" src="" alt="Product" class="h-full w-full object-cover">
            </div>
            <div class="flex-1">
                <h4 id="modal-product-name" class="font-semibold text-slate-900"></h4>
                <p id="modal-product-price" class="text-sm font-bold text-blue-600"></p>
                {{-- 🔥 TAMBAHKAN STOK --}}
                <p id="modal-product-stock" class="text-xs text-slate-500"></p>
            </div>
        </div>

        {{-- Hidden Inputs --}}
        <input type="hidden" id="modal-product-id" value="">
        <input type="hidden" id="modal-selected-variant" value="">
        <input type="hidden" id="modal-variant-values" value="">

        {{-- Varian Options --}}
        <div id="modal-variant-options" class="mt-4 max-h-60 overflow-y-auto">
            {{-- Akan diisi oleh JavaScript --}}
        </div>

        {{-- Quantity --}}
        <div class="mt-4 flex items-center justify-between border-t border-slate-200 pt-4">
            <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden">
                <button type="button" class="modal-qty-btn px-4 py-2 text-slate-600 hover:bg-slate-100" data-action="decrease">−</button>
                <input type="number" id="modal-qty-input" value="1" min="1" max="999"
                       class="w-14 text-center border-0 py-2 text-sm focus:ring-0">
                <button type="button" class="modal-qty-btn px-4 py-2 text-slate-600 hover:bg-slate-100" data-action="increase">+</button>
            </div>

            <button type="button" id="modal-add-to-cart-btn"
                    class="rounded-xl bg-slate-900 px-6 py-2.5 font-semibold text-white transition hover:bg-slate-800 disabled:bg-slate-400 disabled:cursor-not-allowed">
                Tambah ke Keranjang
            </button>
        </div>

        {{-- Error Message --}}
        <p id="modal-error" class="mt-2 text-sm text-red-500 hidden"></p>
    </div>
</div>