<div class="space-y-6">

    {{-- Nama --}}
    <div>

        <label
            for="name"
            class="block text-sm font-medium
                   text-gray-700 mb-2"
        >
            Nama Kategori
        </label>

        <input
            type="text"
            name="name"
            id="name"
            value="{{ old(
                'name',
                $category->name ?? ''
            ) }}"
            required
            class="w-full px-4 py-3
                   border border-gray-300
                   rounded-lg
                   focus:outline-none
                   focus:ring-2
                   focus:ring-blue-500"
        >

        @error('name')

            <p class="text-sm text-red-600 mt-2">
                {{ $message }}
            </p>

        @enderror

    </div>


    {{-- Deskripsi --}}
    <div>

        <label
            for="description"
            class="block text-sm font-medium
                   text-gray-700 mb-2"
        >
            Deskripsi
        </label>

        <textarea
            name="description"
            id="description"
            rows="4"
            class="w-full px-4 py-3
                   border border-gray-300
                   rounded-lg
                   focus:outline-none
                   focus:ring-2
                   focus:ring-blue-500"
        >{{ old(
            'description',
            $category->description ?? ''
        ) }}</textarea>

        @error('description')

            <p class="text-sm text-red-600 mt-2">
                {{ $message }}
            </p>

        @enderror

    </div>


    {{-- Gambar --}}
    <div>

        <label
            for="image"
            class="block text-sm font-medium
                   text-gray-700 mb-2"
        >
            Gambar Kategori
        </label>

        <input
            type="file"
            name="image"
            id="image"
            accept="image/jpeg,image/png,image/webp"
            class="w-full px-4 py-3
                   border border-gray-300
                   rounded-lg"
        >

        <p class="text-xs text-gray-500 mt-2">
            Maksimal 2MB. Format JPG, PNG, atau WebP.
        </p>

        @error('image')

            <p class="text-sm text-red-600 mt-2">
                {{ $message }}
            </p>

        @enderror


        @if(isset($category) && $category->image)

            <div class="mt-4">

                <img
                    src="{{ asset(
                        'storage/' . $category->image
                    ) }}"
                    class="w-24 h-24
                           object-cover
                           rounded-lg"
                >

            </div>

        @endif

    </div>


    {{-- Status --}}
    <div>

        <label class="flex items-center gap-3">

            <input
                type="checkbox"
                name="is_active"
                value="1"
                @checked(
                    old(
                        'is_active',
                        $category->is_active ?? true
                    )
                )
                class="w-5 h-5
                       text-blue-600
                       rounded"
            >

            <span class="text-sm text-gray-700">

                Aktifkan kategori

            </span>

        </label>

    </div>

</div>