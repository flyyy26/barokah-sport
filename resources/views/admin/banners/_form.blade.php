@csrf


<div class="space-y-6">


    {{-- TITLE --}}

    <div>

        <label
            class="block text-sm font-medium text-gray-700"
        >
            Judul Banner
        </label>


        <input
            type="text"
            name="title"
            value="{{ old(
                'title',
                $banner->title ?? ''
            ) }}"
            required
            class="mt-2 block w-full
                   rounded-lg
                   border-gray-300
                   shadow-sm
                   focus:border-blue-500
                   focus:ring-blue-500"
            placeholder="Contoh: Koleksi Terbaru"
        >

    </div>


    {{-- SUBTITLE --}}

    <div>

        <label
            class="block text-sm font-medium text-gray-700"
        >
            Subjudul
        </label>


        <input
            type="text"
            name="subtitle"
            value="{{ old(
                'subtitle',
                $banner->subtitle ?? ''
            ) }}"
            class="mt-2 block w-full
                   rounded-lg
                   border-gray-300
                   shadow-sm
                   focus:border-blue-500
                   focus:ring-blue-500"
            placeholder="Contoh: Temukan produk terbaik kami"
        >

    </div>


    {{-- IMAGE --}}

    <div>

        <label
            class="block text-sm font-medium text-gray-700"
        >
            Gambar Banner
        </label>


        @if (
            isset($banner) &&
            $banner->image
        )

            <div class="mt-3 mb-4">

                <img
                    src="{{ asset(
                        'storage/' .
                        $banner->image
                    ) }}"
                    class="h-48
                           w-full
                           rounded-xl
                           object-cover"
                    alt="{{ $banner->title }}"
                >

            </div>

        @endif


        <input
            type="file"
            name="image"
            accept="image/jpeg,image/png,image/webp"
            {{ isset($banner)
                ? ''
                : 'required'
            }}
            class="mt-2 block w-full
                   text-sm text-gray-600
                   file:mr-4
                   file:rounded-lg
                   file:border-0
                   file:bg-blue-50
                   file:px-4
                   file:py-2
                   file:text-sm
                   file:font-semibold
                   file:text-blue-700
                   hover:file:bg-blue-100"
        >


        @if (isset($banner))

            <p class="mt-2 text-xs text-gray-500">
                Kosongkan jika tidak ingin mengganti gambar.
            </p>

        @endif

    </div>


    {{-- BUTTON TEXT --}}

    <div>

        <label
            class="block text-sm font-medium text-gray-700"
        >
            Teks Tombol
        </label>


        <input
            type="text"
            name="button_text"
            value="{{ old(
                'button_text',
                $banner->button_text ?? ''
            ) }}"
            class="mt-2 block w-full
                   rounded-lg
                   border-gray-300
                   shadow-sm
                   focus:border-blue-500
                   focus:ring-blue-500"
            placeholder="Contoh: Belanja Sekarang"
        >

    </div>


    {{-- BUTTON URL --}}

    <div>

        <label
            class="block text-sm font-medium text-gray-700"
        >
            Link Tombol
        </label>


        <input
            type="text"
            name="button_url"
            value="{{ old(
                'button_url',
                $banner->button_url ?? ''
            ) }}"
            class="mt-2 block w-full
                   rounded-lg
                   border-gray-300
                   shadow-sm
                   focus:border-blue-500
                   focus:ring-blue-500"
            placeholder="/products"
        >

    </div>


    {{-- SORT ORDER --}}

    <div>

        <label
            class="block text-sm font-medium text-gray-700"
        >
            Urutan
        </label>


        <input
            type="number"
            name="sort_order"
            min="0"
            value="{{ old(
                'sort_order',
                $banner->sort_order ?? 0
            ) }}"
            required
            class="mt-2 block w-full
                   rounded-lg
                   border-gray-300
                   shadow-sm
                   focus:border-blue-500
                   focus:ring-blue-500"
        >

    </div>


    {{-- DATE --}}

    <div
        class="grid grid-cols-1
               gap-5
               md:grid-cols-2"
    >

        <div>

            <label
                class="block text-sm font-medium text-gray-700"
            >
                Mulai Tampil
            </label>


            <input
                type="datetime-local"
                name="starts_at"
                value="{{ old(
                    'starts_at',
                    isset($banner->starts_at)
                        ? $banner->starts_at
                            ->format('Y-m-d\TH:i')
                        : ''
                ) }}"
                class="mt-2 block w-full
                       rounded-lg
                       border-gray-300
                       shadow-sm
                       focus:border-blue-500
                       focus:ring-blue-500"
            >

        </div>


        <div>

            <label
                class="block text-sm font-medium text-gray-700"
            >
                Berakhir
            </label>


            <input
                type="datetime-local"
                name="ends_at"
                value="{{ old(
                    'ends_at',
                    isset($banner->ends_at)
                        ? $banner->ends_at
                            ->format('Y-m-d\TH:i')
                        : ''
                ) }}"
                class="mt-2 block w-full
                       rounded-lg
                       border-gray-300
                       shadow-sm
                       focus:border-blue-500
                       focus:ring-blue-500"
            >

        </div>

    </div>


    {{-- ACTIVE --}}

    <div>

        <label
            class="flex items-center gap-3"
        >

            <input
                type="checkbox"
                name="is_active"
                value="1"
                @checked(
                    old(
                        'is_active',
                        $banner->is_active ?? true
                    )
                )
                class="rounded
                       border-gray-300
                       text-blue-600
                       focus:ring-blue-500"
            >


            <span class="text-sm text-gray-700">
                Banner aktif
            </span>

        </label>

    </div>


    {{-- ACTION --}}

    <div
        class="flex justify-end gap-3
               border-t
               pt-6"
    >

        <a
            href="{{ route(
                'admin.banners.index'
            ) }}"
            class="rounded-lg
                   border
                   border-gray-300
                   px-5 py-2.5
                   text-sm
                   font-medium
                   text-gray-700
                   hover:bg-gray-50"
        >
            Batal
        </a>


        <button
            type="submit"
            class="rounded-lg
                   bg-blue-600
                   px-5 py-2.5
                   text-sm
                   font-medium
                   text-white
                   hover:bg-blue-700"
        >

            {{ isset($banner)
                ? 'Simpan Perubahan'
                : 'Simpan Banner'
            }}

        </button>

    </div>

</div>