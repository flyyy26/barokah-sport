@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>

            <h1 class="text-2xl font-bold text-gray-900">
                Produk
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Kelola semua produk yang tersedia di toko.
            </p>

        </div>


        <a
            href="{{ route('admin.products.create') }}"
            class="inline-flex items-center justify-center
                   rounded-lg bg-blue-600
                   px-4 py-2.5
                   text-sm font-semibold
                   text-white
                   transition
                   hover:bg-blue-700"
        >

            <svg
                class="mr-2 h-5 w-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4v16m8-8H4"
                />

            </svg>

            Tambah Produk

        </a>

    </div>


    {{-- ========================================================= --}}
    {{-- SUCCESS MESSAGE --}}
    {{-- ========================================================= --}}

    @if (session('success'))

        <div
            class="rounded-lg border border-green-200
                   bg-green-50 px-4 py-3
                   text-sm text-green-700"
        >

            {{ session('success') }}

        </div>

    @endif

    @if (session('error'))

        <div
            class="rounded-lg border border-red-200
                   bg-red-50 px-4 py-3
                   text-sm text-red-700"
        >

            {{ session('error') }}

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- ERROR MESSAGE --}}
    {{-- ========================================================= --}}

    @if ($errors->any())

        <div
            class="rounded-lg border border-red-200
                   bg-red-50 px-4 py-3"
        >

            <p class="text-sm font-semibold text-red-700">
                Terjadi kesalahan:
            </p>

            <ul class="mt-2 list-inside list-disc text-sm text-red-600">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- TABLE --}}
    {{-- ========================================================= --}}

    <div
        class="overflow-hidden rounded-xl
               border border-gray-200
               bg-white shadow-sm"
    >

        {{-- TABLE WRAPPER --}}

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                {{-- ================================================= --}}
                {{-- TABLE HEADER --}}
                {{-- ================================================= --}}

                <thead class="bg-gray-50">

                    <tr>

                        <th
                            scope="col"
                            class="px-6 py-4 text-left
                                   text-xs font-semibold
                                   uppercase tracking-wider
                                   text-gray-500"
                        >
                            Produk
                        </th>


                        <th
                            scope="col"
                            class="px-6 py-4 text-left
                                   text-xs font-semibold
                                   uppercase tracking-wider
                                   text-gray-500"
                        >
                            Kategori
                        </th>


                        <th
                            scope="col"
                            class="px-6 py-4 text-left
                                   text-xs font-semibold
                                   uppercase tracking-wider
                                   text-gray-500"
                        >
                            Varian
                        </th>


                        <th
                            scope="col"
                            class="px-6 py-4 text-left
                                   text-xs font-semibold
                                   uppercase tracking-wider
                                   text-gray-500"
                        >
                            Harga
                        </th>


                        <th
                            scope="col"
                            class="px-6 py-4 text-left
                                   text-xs font-semibold
                                   uppercase tracking-wider
                                   text-gray-500"
                        >
                            Stok
                        </th>


                        <th
                            scope="col"
                            class="px-6 py-4 text-left
                                   text-xs font-semibold
                                   uppercase tracking-wider
                                   text-gray-500"
                        >
                            Status
                        </th>


                        <th
                            scope="col"
                            class="px-6 py-4 text-right
                                   text-xs font-semibold
                                   uppercase tracking-wider
                                   text-gray-500"
                        >
                            Aksi
                        </th>

                    </tr>

                </thead>


                {{-- ================================================= --}}
                {{-- TABLE BODY --}}
                {{-- ================================================= --}}

                <tbody class="divide-y divide-gray-100 bg-white">

                    @forelse (
                        $products
                        as $product
                    )

                        <tr
                            class="transition hover:bg-gray-50"
                        >

                            {{-- ===================================== --}}
                            {{-- PRODUK --}}
                            {{-- ===================================== --}}

                            <td class="whitespace-nowrap px-6 py-4">

                                <div class="flex items-center gap-4">

                                    {{-- GAMBAR --}}

                                    <div
                                        class="h-14 w-14
                                               flex-shrink-0
                                               overflow-hidden
                                               rounded-lg
                                               bg-gray-100"
                                    >

                                        @if (
                                            $product->images->isNotEmpty()
                                        )

                                            <img
                                                src="{{ asset(
                                                    'storage/' .
                                                    $product
                                                        ->images
                                                        ->first()
                                                        ->image
                                                ) }}"
                                                alt="{{ $product->name }}"
                                                class="h-full w-full
                                                       object-cover"
                                            >

                                        @else

                                            <div
                                                class="flex h-full
                                                       w-full
                                                       items-center
                                                       justify-center
                                                       text-gray-400"
                                            >

                                                <svg
                                                    class="h-6 w-6"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 016.828 0L20 16m-2-2l1.586-1.586a2 2 0 011.414-.586M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                    />

                                                </svg>

                                            </div>

                                        @endif

                                    </div>


                                    {{-- NAMA --}}

                                    <div>

                                        <div
                                            class="font-semibold
                                                   text-gray-900"
                                        >
                                            {{ $product->name }}
                                        </div>

                                        <div
                                            class="mt-1 text-xs
                                                   text-gray-500"
                                        >
                                            {{ $product->slug }}
                                        </div>

                                    </div>

                                </div>

                            </td>


                            {{-- ===================================== --}}
                            {{-- KATEGORI --}}
                            {{-- ===================================== --}}

                            <td class="whitespace-nowrap px-6 py-4">

                                <span
                                    class="rounded-full
                                           bg-blue-50
                                           px-3 py-1
                                           text-xs font-medium
                                           text-blue-700"
                                >
                                    {{ $product->category->name ?? '-' }}
                                </span>

                            </td>


                            {{-- ===================================== --}}
                            {{-- VARIAN --}}
                            {{-- ===================================== --}}

                            <td class="whitespace-nowrap px-6 py-4">

                                <span
                                    class="text-sm
                                           font-medium
                                           text-gray-700"
                                >
                                    {{ $product->variants->count() }}
                                    varian
                                </span>

                            </td>


                            {{-- ===================================== --}}
                            {{-- HARGA --}}
                            {{-- ===================================== --}}

                            <td class="whitespace-nowrap px-6 py-4">

                                @php

                                    $minPrice =
                                        $product
                                            ->variants
                                            ->min('price');

                                    $maxPrice =
                                        $product
                                            ->variants
                                            ->max('price');

                                @endphp


                                @if (
                                    $minPrice !== null
                                    &&
                                    $maxPrice !== null
                                )

                                    @if (
                                        $minPrice ==
                                        $maxPrice
                                    )

                                        <span
                                            class="text-sm
                                                   font-semibold
                                                   text-gray-900"
                                        >
                                            Rp
                                            {{ number_format(
                                                $minPrice,
                                                0,
                                                ',',
                                                '.'
                                            ) }}
                                        </span>

                                    @else

                                        <span
                                            class="text-sm
                                                   font-semibold
                                                   text-gray-900"
                                        >
                                            Rp
                                            {{ number_format(
                                                $minPrice,
                                                0,
                                                ',',
                                                '.'
                                            ) }}

                                            -

                                            Rp
                                            {{ number_format(
                                                $maxPrice,
                                                0,
                                                ',',
                                                '.'
                                            ) }}
                                        </span>

                                    @endif

                                @else

                                    <span class="text-sm text-gray-400">
                                        Belum ada harga
                                    </span>

                                @endif

                            </td>


                            {{-- ===================================== --}}
                            {{-- STOK --}}
                            {{-- ===================================== --}}

                            <td class="whitespace-nowrap px-6 py-4">

                                @php

                                    $totalStock =
                                        $product
                                            ->variants
                                            ->sum('stock');

                                @endphp


                                @if (
                                    $totalStock > 0
                                )

                                    <span
                                        class="text-sm
                                               font-medium
                                               text-gray-700"
                                    >
                                        {{ number_format(
                                            $totalStock,
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                    </span>

                                @else

                                    <span
                                        class="text-sm
                                               font-medium
                                               text-red-600"
                                    >
                                        Habis
                                    </span>

                                @endif

                            </td>


                            {{-- ===================================== --}}
                            {{-- STATUS --}}
                            {{-- ===================================== --}}

                            <td class="whitespace-nowrap px-6 py-4">

                                @if (
                                    $product->is_active
                                )

                                    <span
                                        class="inline-flex
                                               rounded-full
                                               bg-green-50
                                               px-3 py-1
                                               text-xs
                                               font-medium
                                               text-green-700"
                                    >
                                        Aktif
                                    </span>

                                @else

                                    <span
                                        class="inline-flex
                                               rounded-full
                                               bg-gray-100
                                               px-3 py-1
                                               text-xs
                                               font-medium
                                               text-gray-600"
                                    >
                                        Nonaktif
                                    </span>

                                @endif

                            </td>


                            {{-- ===================================== --}}
                            {{-- AKSI --}}
                            {{-- ===================================== --}}

                            <td class="whitespace-nowrap px-6 py-4">

                                <div
                                    class="flex items-center
                                           justify-end gap-2"
                                >

                                    {{-- EDIT --}}

                                    <a
                                        href="{{ route(
                                            'admin.products.edit',
                                            $product
                                        ) }}"
                                        class="rounded-lg
                                               border border-gray-200
                                               p-2 text-gray-500
                                               transition
                                               hover:border-blue-200
                                               hover:bg-blue-50
                                               hover:text-blue-600"
                                        title="Edit Produk"
                                    >

                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-9.5a2.121 2.121 0 013 3L12 14l-4 1 1-4 7.5-7.5z"
                                            />

                                        </svg>

                                    </a>


                                    {{-- DELETE --}}

                                    <form
                                        action="{{ route('admin.products.destroy', $product) }}"
                                        method="POST"
                                        onsubmit="return confirm(
                                            'Apakah kamu yakin ingin menghapus produk ini? Semua varian dan gambar produk juga akan dihapus.'
                                        )"
                                        class="inline"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="rounded-lg border border-gray-200 p-2 text-gray-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                                            title="Hapus Produk"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>

                                </div>

                            </td>

                        </tr>


                    @empty

                        {{-- ========================================= --}}
                        {{-- EMPTY STATE --}}
                        {{-- ========================================= --}}

                        <tr>

                            <td
                                colspan="7"
                                class="px-6 py-16 text-center"
                            >

                                <div
                                    class="mx-auto flex h-16 w-16
                                           items-center justify-center
                                           rounded-full bg-gray-100"
                                >

                                    <svg
                                        class="h-8 w-8 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4m4 4h8"
                                        />

                                    </svg>

                                </div>


                                <h3
                                    class="mt-4 text-sm
                                           font-semibold
                                           text-gray-900"
                                >
                                    Belum ada produk
                                </h3>


                                <p
                                    class="mt-1 text-sm
                                           text-gray-500"
                                >
                                    Mulai tambahkan produk pertama
                                    ke toko kamu.
                                </p>


                                <a
                                    href="{{ route(
                                        'admin.products.create'
                                    ) }}"
                                    class="mt-5 inline-flex
                                           items-center
                                           rounded-lg
                                           bg-blue-600
                                           px-4 py-2
                                           text-sm font-semibold
                                           text-white
                                           hover:bg-blue-700"
                                >
                                    Tambah Produk
                                </a>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ========================================================= --}}
        {{-- PAGINATION --}}
        {{-- ========================================================= --}}

        @if (
            $products->hasPages()
        )

            <div
                class="border-t border-gray-200
                       px-6 py-4"
            >

                {{ $products->links() }}

            </div>

        @endif

    </div>

</div>

@endsection