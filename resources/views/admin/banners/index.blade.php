@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Banner
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Kelola banner yang ditampilkan pada halaman utama toko.
            </p>
        </div>


        <a
            href="{{ route('admin.banners.create') }}"
            class="inline-flex items-center justify-center gap-2
                   rounded-lg
                   bg-blue-600
                   px-4 py-2.5
                   text-sm font-semibold
                   text-white
                   transition
                   hover:bg-blue-700"
        >

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 4v16m8-8H4"
                />
            </svg>

            Tambah Banner

        </a>

    </div>


    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))

        <div
            class="flex items-start gap-3
                   rounded-lg
                   border border-green-200
                   bg-green-50
                   px-4 py-3
                   text-sm text-green-700"
        >

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="mt-0.5 h-5 w-5 shrink-0"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M5 13l4 4L19 7"
                />
            </svg>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- ERROR MESSAGE --}}
    @if (session('error'))

        <div
            class="flex items-start gap-3
                   rounded-lg
                   border border-red-200
                   bg-red-50
                   px-4 py-3
                   text-sm text-red-700"
        >

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="mt-0.5 h-5 w-5 shrink-0"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 9v2m0 4h.01M10.29 3.86l-7.82 13.5A2 2 0 004.2 20.36h15.6a2 2 0 001.73-3L13.71 3.86a2 2 0 00-3.42 0z"
                />
            </svg>

            <span>
                {{ session('error') }}
            </span>

        </div>

    @endif


    {{-- VALIDATION ERROR --}}
    @if ($errors->any())

        <div
            class="rounded-lg
                   border border-red-200
                   bg-red-50
                   px-4 py-3
                   text-sm text-red-700"
        >

            <p class="font-semibold">
                Terjadi kesalahan:
            </p>


            <ul class="mt-2 list-inside list-disc">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- TABLE CARD --}}
    <div
        class="overflow-hidden
               rounded-xl
               bg-white
               shadow-sm
               ring-1
               ring-gray-200"
    >

        {{-- TABLE HEADER --}}

        <div
            class="border-b
                   border-gray-200
                   px-6 py-4"
        >

            <div class="flex items-center justify-between">

                <div>

                    <h2 class="text-base font-semibold text-gray-900">
                        Daftar Banner
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Total {{ $banners->count() }} banner
                    </p>

                </div>

            </div>

        </div>


        {{-- TABLE --}}

        @if ($banners->isEmpty())

            {{-- EMPTY STATE --}}

            <div
                class="flex flex-col
                       items-center
                       justify-center
                       px-6 py-16
                       text-center"
            >

                <div
                    class="flex h-16 w-16
                           items-center justify-center
                           rounded-full
                           bg-gray-100"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-8 w-8 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 7.5A2.5 2.5 0 015.5 5h13A2.5 2.5 0 0121 7.5v9a2.5 2.5 0 01-2.5 2.5h-13A2.5 2.5 0 013 16.5v-9z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 8l6.5 5a4 4 0 005 0L21 8"
                        />
                    </svg>

                </div>


                <h3
                    class="mt-4
                           text-base
                           font-semibold
                           text-gray-900"
                >
                    Belum ada banner
                </h3>


                <p
                    class="mt-1
                           max-w-sm
                           text-sm
                           text-gray-500"
                >
                    Tambahkan banner pertama untuk ditampilkan
                    pada halaman utama toko.
                </p>


                <a
                    href="{{ route(
                        'admin.banners.create'
                    ) }}"
                    class="mt-5
                           rounded-lg
                           bg-blue-600
                           px-4 py-2.5
                           text-sm
                           font-semibold
                           text-white
                           hover:bg-blue-700"
                >
                    Tambah Banner
                </a>

            </div>

        @else

            <div class="overflow-x-auto">

                <table
                    class="min-w-full
                           divide-y
                           divide-gray-200"
                >

                    <thead class="bg-gray-50">

                        <tr>

                            <th
                                class="px-6 py-3
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-gray-500"
                            >
                                Banner
                            </th>


                            <th
                                class="px-6 py-3
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-gray-500"
                            >
                                Tombol
                            </th>


                            <th
                                class="px-6 py-3
                                       text-center
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-gray-500"
                            >
                                Urutan
                            </th>


                            <th
                                class="px-6 py-3
                                       text-center
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-gray-500"
                            >
                                Status
                            </th>


                            <th
                                class="px-6 py-3
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-gray-500"
                            >
                                Periode
                            </th>


                            <th
                                class="px-6 py-3
                                       text-right
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-gray-500"
                            >
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody
                        class="divide-y
                               divide-gray-200
                               bg-white"
                    >

                        @foreach (
                            $banners
                            as $banner
                        )

                            <tr
                                class="transition
                                       hover:bg-gray-50"
                            >

                                {{-- BANNER --}}

                                <td class="px-6 py-4">

                                    <div
                                        class="flex
                                               min-w-[300px]
                                               items-center
                                               gap-4"
                                    >

                                        <div
                                            class="h-20
                                                   w-32
                                                   shrink-0
                                                   overflow-hidden
                                                   rounded-lg
                                                   bg-gray-100"
                                        >

                                            <img
                                                src="{{ asset(
                                                    'storage/' .
                                                    $banner->image
                                                ) }}"
                                                alt="{{ $banner->title }}"
                                                class="h-full
                                                       w-full
                                                       object-cover"
                                            >

                                        </div>


                                        <div
                                            class="min-w-0"
                                        >

                                            <p
                                                class="truncate
                                                       text-sm
                                                       font-semibold
                                                       text-gray-900"
                                            >
                                                {{ $banner->title }}
                                            </p>


                                            @if (
                                                $banner->subtitle
                                            )

                                                <p
                                                    class="mt-1
                                                           line-clamp-2
                                                           text-xs
                                                           text-gray-500"
                                                >
                                                    {{ $banner->subtitle }}
                                                </p>

                                            @endif

                                        </div>

                                    </div>

                                </td>


                                {{-- BUTTON --}}

                                <td class="px-6 py-4">

                                    @if (
                                        $banner->button_text
                                    )

                                        <div>

                                            <p
                                                class="text-sm
                                                       font-medium
                                                       text-gray-700"
                                            >
                                                {{ $banner->button_text }}
                                            </p>


                                            @if (
                                                $banner->button_url
                                            )

                                                <p
                                                    class="mt-1
                                                           max-w-[180px]
                                                           truncate
                                                           text-xs
                                                           text-gray-400"
                                                    title="{{ $banner->button_url }}"
                                                >
                                                    {{ $banner->button_url }}
                                                </p>

                                            @endif

                                        </div>

                                    @else

                                        <span
                                            class="text-sm
                                                   text-gray-400"
                                        >
                                            Tidak ada

                                        </span>

                                    @endif

                                </td>


                                {{-- SORT ORDER --}}

                                <td
                                    class="whitespace-nowrap
                                           px-6 py-4
                                           text-center"
                                >

                                    <span
                                        class="inline-flex
                                               h-8
                                               min-w-8
                                               items-center
                                               justify-center
                                               rounded-lg
                                               bg-gray-100
                                               px-2
                                               text-sm
                                               font-semibold
                                               text-gray-700"
                                    >
                                        {{ $banner->sort_order }}
                                    </span>

                                </td>


                                {{-- STATUS --}}

                                <td
                                    class="whitespace-nowrap
                                           px-6 py-4
                                           text-center"
                                >

                                    @if (
                                        $banner->is_active
                                    )

                                        <span
                                            class="inline-flex
                                                   items-center
                                                   gap-1.5
                                                   rounded-full
                                                   bg-green-50
                                                   px-3 py-1
                                                   text-xs
                                                   font-semibold
                                                   text-green-700"
                                        >

                                            <span
                                                class="h-1.5
                                                       w-1.5
                                                       rounded-full
                                                       bg-green-500"
                                            ></span>

                                            Aktif

                                        </span>

                                    @else

                                        <span
                                            class="inline-flex
                                                   items-center
                                                   gap-1.5
                                                   rounded-full
                                                   bg-gray-100
                                                   px-3 py-1
                                                   text-xs
                                                   font-semibold
                                                   text-gray-600"
                                        >

                                            <span
                                                class="h-1.5
                                                       w-1.5
                                                       rounded-full
                                                       bg-gray-400"
                                            ></span>

                                            Nonaktif

                                        </span>

                                    @endif

                                </td>


                                {{-- PERIODE --}}

                                <td class="px-6 py-4">

                                    <div
                                        class="text-sm"
                                    >

                                        @if (
                                            $banner->starts_at
                                        )

                                            <p
                                                class="text-gray-700"
                                            >
                                                Mulai:
                                                {{ $banner->starts_at->format(
                                                    'd M Y H:i'
                                                ) }}
                                            </p>

                                        @else

                                            <p
                                                class="text-gray-400"
                                            >
                                                Mulai: -
                                            </p>

                                        @endif


                                        @if (
                                            $banner->ends_at
                                        )

                                            <p
                                                class="mt-1
                                                       text-gray-700"
                                            >
                                                Berakhir:
                                                {{ $banner->ends_at->format(
                                                    'd M Y H:i'
                                                ) }}
                                            </p>

                                        @else

                                            <p
                                                class="mt-1
                                                       text-gray-400"
                                            >
                                                Berakhir: -
                                            </p>

                                        @endif

                                    </div>

                                </td>


                                {{-- ACTION --}}

                                <td
                                    class="whitespace-nowrap
                                           px-6 py-4"
                                >

                                    <div
                                        class="flex
                                               items-center
                                               justify-end
                                               gap-2"
                                    >

                                        {{-- EDIT --}}

                                        <a
                                            href="{{ route(
                                                'admin.banners.edit',
                                                $banner
                                            ) }}"
                                            class="inline-flex
                                                   items-center
                                                   gap-1.5
                                                   rounded-lg
                                                   bg-blue-50
                                                   px-3 py-2
                                                   text-sm
                                                   font-medium
                                                   text-blue-600
                                                   transition
                                                   hover:bg-blue-100"
                                        >

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652l-9.193 9.193a4.5 4.5 0 01-1.897 1.13l-3.18.954.954-3.18a4.5 4.5 0 011.13-1.897l7.847-7.164z"
                                                />

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M19.5 7.125L16.875 4.5"
                                                />
                                            </svg>

                                            Edit

                                        </a>


                                        {{-- DELETE --}}

                                        <form
                                            action="{{ route(
                                                'admin.banners.destroy',
                                                $banner
                                            ) }}"
                                            method="POST"
                                            onsubmit="return confirm(
                                                'Yakin ingin menghapus banner ini? Gambar banner juga akan dihapus secara permanen.'
                                            )"
                                        >

                                            @csrf

                                            @method('DELETE')


                                            <button
                                                type="submit"
                                                class="inline-flex
                                                       items-center
                                                       gap-1.5
                                                       rounded-lg
                                                       bg-red-50
                                                       px-3 py-2
                                                       text-sm
                                                       font-medium
                                                       text-red-600
                                                       transition
                                                       hover:bg-red-100"
                                            >

                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-3a1 1 0 00-1 1v3m-4 0h14"
                                                    />
                                                </svg>

                                                Hapus

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @endif

    </div>

</div>

@endsection