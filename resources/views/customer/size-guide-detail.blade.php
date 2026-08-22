@extends('layouts.customer')

@section('title', 'Panduan Ukuran ' . $category->name . ' - Barokah Sport')

@section('content')

<style>
    html {
        scroll-behavior: smooth;
    }

    .sizeguide_detail_container {
        width: 100%;
        margin: 0 auto;
        border-top: 0.1vw solid #076694;
    }

    .sizeguide_detail_top_container {
        width: 100%;
        padding: 1.3vw 7.54vw;
        padding-bottom: 1.8vw;
        background: #f9fafb;
    }

    .sizeguide_detail_header h1 {
        font-size: 2.3vw;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        font-family: heading, sans-serif;
        text-transform: uppercase;
    }

    .sizeguide_detail_header p {
        font-size: 0.85vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }

    .sizeguide_detail_header .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5vw;
        color: #076694;
        font-size: 0.85vw;
        text-decoration: none;
        margin-top: 0.5vw;
    }

    .sizeguide_detail_header .back-link:hover {
        text-decoration: underline;
    }

    .sizeguide_detail_content_container {
        width: 100%;
        padding: 3vw 7.4vw;
    }

    .sizeguide_detail_table_wrapper {
        background: white;
        border-radius: 0.7vw;
        padding: 2vw;
        box-shadow: 0 0.1vw 0.5vw rgba(0, 0, 0, 0.05);
        border: 0.1vw solid #e2e8f0;
        overflow-x: auto;
    }

    .sizeguide_detail_table_wrapper h2 {
        font-size: 1.2vw;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 1.5vw;
    }

    .sizeguide_detail_table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85vw;
    }

    .sizeguide_detail_table thead {
        background: #f8fafc;
    }

    .sizeguide_detail_table thead th {
        padding: 0.8vw 1.2vw;
        text-align: center;
        font-weight: 600;
        color: #0f172a;
        border-bottom: 0.15vw solid #e2e8f0;
    }

    .sizeguide_detail_table tbody td {
        padding: 0.8vw 1.2vw;
        text-align: center;
        border-bottom: 0.05vw solid #f1f5f9;
        color: #475569;
    }

    .sizeguide_detail_table tbody tr:hover {
        background: #f8fafc;
    }

    .sizeguide_detail_table tbody tr:last-child td {
        border-bottom: none;
    }

    .sizeguide_detail_table .size-label {
        font-weight: 600;
        color: #076694;
        background: #f0f9ff;
    }

    .sizeguide_detail_empty {
        text-align: center;
        padding: 3vw;
    }

    .sizeguide_detail_empty p {
        color: #94a3b8;
        font-size: 0.9vw;
    }

    .sizeguide_detail_note {
        margin-top: 1.5vw;
        padding: 1vw 1.5vw;
        background: #f8fafc;
        border-radius: 0.5vw;
        border-left: 0.3vw solid #076694;
        font-size: 0.8vw;
        color: #64748b;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sizeguide_detail_header h1 {
            font-size: 5vw;
        }

        .sizeguide_detail_header p {
            font-size: 2.5vw;
        }

        .sizeguide_detail_header .back-link {
            font-size: 2.5vw;
        }

        .sizeguide_detail_content_container {
            padding: 3vw 5vw;
        }

        .sizeguide_detail_table_wrapper {
            padding: 4vw;
        }

        .sizeguide_detail_table_wrapper h2 {
            font-size: 3.5vw;
        }

        .sizeguide_detail_table {
            font-size: 2.5vw;
        }

        .sizeguide_detail_table thead th,
        .sizeguide_detail_table tbody td {
            padding: 2vw 1.5vw;
        }

        .sizeguide_detail_note {
            font-size: 2.3vw;
            padding: 2vw 3vw;
        }

        .sizeguide_detail_empty p {
            font-size: 2.5vw;
        }
    }

    @media (max-width: 480px) {
        .sizeguide_detail_table {
            font-size: 2.8vw;
        }

        .sizeguide_detail_table thead th,
        .sizeguide_detail_table tbody td {
            padding: 2vw 1vw;
        }
    }
</style>

<div class="sizeguide_detail_container">
    {{-- HEADER --}}
    <div class="sizeguide_detail_top_container">
        <div class="sizeguide_detail_header">
            <a href="{{ route('customer.size-guide') }}" class="back-link">
                <iconify-icon icon="ic:sharp-arrow-back"></iconify-icon>
                Kembali ke Panduan Ukuran
            </a>
            <h1>Panduan Ukuran {{ $category->name }}</h1>
            <p>Temukan ukuran yang tepat untuk produk {{ $category->name }}</p>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="sizeguide_detail_content_container">
        @php
            $dimensionLabels = $category->dimension_labels ?? [];
            $sizeGuides = $category->sizeGuides;
        @endphp

        @if($sizeGuides->isNotEmpty() && !empty($dimensionLabels))
            <div class="sizeguide_detail_table_wrapper">
                <h2>Tabel Ukuran {{ $category->name }}</h2>
                
                <table class="sizeguide_detail_table">
                    <thead>
                        <tr>
                            <th>Ukuran</th>
                            @foreach($dimensionLabels as $label)
                                <th>{{ $label }} (cm)</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sizeGuides as $guide)
                            <tr>
                                <td class="size-label">{{ $guide->size }}</td>
                                @foreach($dimensionLabels as $label)
                                    <td>{{ $guide->dimensions[$label] ?? '-' }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="sizeguide_detail_note">
                <strong>Tips:</strong> 
                Ukur tubuh Anda dengan pita meteran dan bandingkan dengan tabel di atas. 
                Pilih ukuran yang paling mendekati ukuran tubuh Anda.
            </div>
        @else
            <div class="sizeguide_detail_table_wrapper">
                <div class="sizeguide_detail_empty">
                    <p>Maaf, panduan ukuran untuk kategori ini belum tersedia.</p>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection