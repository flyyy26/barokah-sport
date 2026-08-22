@extends('layouts.customer')

@section('title', 'Panduan Ukuran - Barokah Sport')

@section('content')

<style>
    html {
        scroll-behavior: smooth;
    }

    .sizeguide_container {
        width: 100%;
        margin: 0 auto;
        border-top: 0.1vw solid #076694;
    }

    .sizeguide_top_container {
        width: 100%;
        padding: 1.3vw 7.54vw;
        padding-bottom: 1.8vw;
        background: #f9fafb;
    }

    .sizeguide-header h1 {
        font-size: 2.3vw;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        font-family: heading, sans-serif;
        text-transform: uppercase;
    }

    .sizeguide-header p {
        font-size: 0.85vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }

    .sizeguide_content_container {
        width: 100%;
        padding: 3vw 7.4vw;
    }

    .sizeguide_grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 2vw;
    }

    .sizeguide_card {
        background: white;
        border-radius: 0.9vw;
        padding: .7vw;
        padding-bottom:1.2vw;
        box-shadow: 0 0.1vw 0.5vw rgba(0, 0, 0, 0.05);
        border: 0.1vw solid #e2e8f0;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
        text-align: center;
    }

    .sizeguide_card:hover {
        transform: translateY(-0.3vw);
        box-shadow: 0 0.5vw 1.5vw rgba(0, 0, 0, 0.1);
        border-color: #076694;
    }

    .sizeguide_card_icon {
        font-size: 3vw;
        margin-bottom: 1vw;
        display: block;
    }
    
    .sizeguide_image{
        width:100%;
        height:15vw;
        position:relative;
        overflow:hidden;
        border-radius:.7vw;
    }
    .sizeguide_image img{
        width: 100%;
        height: 100%;
        top:0;
        left: 0;
        object-fit:cover;
        object-position:center;
    }

    .sizeguide_card h3 {
        font-size: 1.7vw;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 0.5vw;
        font-family: heading, sans-serif;
    }

    .sizeguide_card p {
        font-size: 0.8vw;
        color: #94a3b8;
        margin: 0;
    }

    .sizeguide_card .badge {
        display: inline-block;
        margin-top: 0.8vw;
        padding: 0.2vw 1vw;
        background: #076694;
        color: white;
        border-radius: 100vw;
        font-size: .85vw;
        font-weight: 500;
    }

    .sizeguide_empty {
        text-align: center;
        padding: 4vw;
        background: white;
        border-radius: 0.7vw;
        border: 0.1vw solid #e2e8f0;
    }

    .sizeguide_empty iconify-icon {
        font-size: 4vw;
        color: #94a3b8;
        margin-bottom: 1vw;
        display: block;
    }

    .sizeguide_empty h3 {
        font-size: 1.2vw;
        color: #0f172a;
        margin-bottom: 0.5vw;
    }

    .sizeguide_empty p {
        font-size: 0.85vw;
        color: #94a3b8;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sizeguide-header h1 {
            font-size: 5vw;
        }

        .sizeguide-header p {
            font-size: 2.5vw;
        }

        .sizeguide_content_container {
            padding: 3vw 5vw;
        }

        .sizeguide_grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 3vw;
        }

        .sizeguide_card {
            padding: 4vw;
        }

        .sizeguide_card_icon {
            font-size: 8vw;
        }

        .sizeguide_card h3 {
            font-size: 3.5vw;
        }

        .sizeguide_card p {
            font-size: 2.5vw;
        }

        .sizeguide_card .badge {
            font-size: 2vw;
            padding: 0.5vw 3vw;
        }

        .sizeguide_empty iconify-icon {
            font-size: 10vw;
        }

        .sizeguide_empty h3 {
            font-size: 4vw;
        }

        .sizeguide_empty p {
            font-size: 2.5vw;
        }
    }

    @media (max-width: 480px) {
        .sizeguide_grid {
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        }
    }
</style>

<div class="sizeguide_container">
    {{-- HEADER --}}
    <div class="sizeguide_top_container">
        <div class="sizeguide-header">
            <h1>Panduan Ukuran</h1>
            <p>Temukan panduan ukuran yang tepat untuk setiap kategori produk</p>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="sizeguide_content_container">
        @if($categories->count() > 0)
            <div class="sizeguide_grid">
                @foreach($categories as $category)
                    <a href="{{ route('customer.size-guide.show', $category->slug) }}" class="sizeguide_card">
                        <span class="sizeguide_card_icon">
                            @if($category->image)
                                <div class="sizeguide_image">
                                    <img src="{{ asset('storage/' . $category->image) }}" 
                                     alt="{{ $category->name }}"/>
                                </div>
                            @else
                                📦
                            @endif
                        </span>
                        <h3>{{ $category->name }}</h3>
                        <p>{{ $category->sizeGuides->count() }} ukuran tersedia</p>
                        <span class="badge">Lihat Panduan</span>
                    </a>
                @endforeach
            </div>
        @else
            <div class="sizeguide_empty">
                <iconify-icon icon="mdi:ruler-square-compass"></iconify-icon>
                <h3>Belum Ada Panduan Ukuran</h3>
                <p>Saat ini belum tersedia panduan ukuran untuk produk kami.</p>
                <p class="mt-2 text-sm text-gray-400">Silakan cek kembali nanti.</p>
            </div>
        @endif
    </div>
</div>

@endsection