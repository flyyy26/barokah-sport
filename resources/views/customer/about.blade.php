@extends('layouts.customer')

@section('title', $about->title . ' - Barokah Sport')

@section('content')

<style>
    html {
        scroll-behavior: smooth;
    }

    .about_container {
        width: 100%;
        margin: 0 auto;
        border-top: 0.1vw solid #076694;
    }

    .about_top_container {
        width: 100%;
        padding: 1.3vw 7.54vw;
        padding-bottom: 1.8vw;
        background: #f9fafb;
    }

    .about-header h1 {
        font-size: 2.3vw;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        font-family: heading, sans-serif;
        text-transform: uppercase;
    }

    .about-header p {
        font-size: 0.85vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }

    .about_content_container {
        width: 100%;
        padding: 3vw 7.4vw;
        display: grid;
        grid-template-columns: 60% 40%;
        align-items: start;
    }

    .about_main_content {
        background: white;
        border-radius: 0.7vw;
        padding: 1vw 2vw;
        box-shadow: 0 0.1vw 0.5vw rgba(0, 0, 0, 0.05);
        border: 0.1vw solid #e2e8f0;
    }

    .about_main_content .content {
        font-size: 0.9vw;
        line-height: 1.8;
        color: #334155;
    }

    .about_main_content .content h2,
    .about_main_content .content h3 {
        color: #076694;
        margin-top: 1.5vw;
        margin-bottom: 0.5vw;
        font-family: heading, sans-serif;
    }

    .about_main_content .content h1 {
        color: #076694;
        font-size: 1.8vw;
        margin-top: 1.5vw;
        margin-bottom: 0.5vw;
        font-family: heading, sans-serif;
    }

    .about_main_content .content p {
        margin-bottom: 1vw;
    }

    .about_main_content .content ul,
    .about_main_content .content ol {
        padding-left: 1.5vw;
        margin-bottom: 1vw;
    }

    .about_main_content .content li {
        margin-bottom: 0.3vw;
    }

    .about_main_content .content strong {
        color: #0f172a;
    }

    .about_sidebar {
        display: flex;
        flex-direction: column;
        gap: 1.5vw;
        padding-left:2vw;
    }

    .about_sidebar_box {
        background: white;
        border-radius: 0.7vw;
        padding: 1.5vw;
        box-shadow: 0 0.1vw 0.5vw rgba(0, 0, 0, 0.05);
        border: 0.1vw solid #e2e8f0;
    }

    .about_sidebar_box h3 {
        font-size: 1.6vw;
        letter-spacing:.03vw;
        color: #076694;
        margin-bottom: 0.8vw;
        font-family: heading, sans-serif;
        text-transform:uppercase;
        padding-bottom: 0.5vw;
        border-bottom: 0.1vw solid #e2e8f0;
    }

    .about_sidebar_box .vision-content,
    .about_sidebar_box .mission-content {
        font-size: 0.85vw;
        color: #475569;
        line-height: 1.8;
    }

    .about_sidebar_box .vision-content p,
    .about_sidebar_box .mission-content p {
        margin-bottom: 0.5vw;
    }

    .about_sidebar_box .vision-content ul,
    .about_sidebar_box .mission-content ul, .mission-content ol {
        list-style: disc;
        margin-left: 1vw;
        margin-top: 0.5vw;
    }

    .about_sidebar_box .vision-content ul li,
    .about_sidebar_box .mission-content ul li {
        margin-bottom: 0.3vw;
        font-size: 0.85vw;
        color: #475569;
        list-style: disc;
    }

    .about_sidebar_box ul {
        list-style: none;
        padding: 0;
        margin-left: 1vw;
    }

    .about_sidebar_box ul li {
        padding: 0.5vw 0;
        border-bottom: 0.05vw solid #f1f5f9;
        font-size: 0.85vw;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 0.5vw;
    }

    .about_sidebar_box ul li:last-child {
        border-bottom: none;
    }

    .about_sidebar_box ul li iconify-icon {
        color: #076694;
        font-size: 1.2vw;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .about_content_container {
            grid-template-columns: 1fr;
            padding: 3vw 5vw;
            gap: 5vw;
        }

        .about-header h1 {
            font-size: 5vw;
        }

        .about-header p {
            font-size: 2.5vw;
        }

        .about_main_content {
            padding: 4vw;
        }

        .about_main_content .content {
            font-size: 2.5vw;
        }

        .about_main_content .content h1 {
            font-size: 4vw;
        }

        .about_main_content .content h2 {
            font-size: 3.5vw;
        }

        .about_main_content .content h3 {
            font-size: 3vw;
        }

        .about_sidebar_box {
            padding: 4vw;
        }

        .about_sidebar_box h3 {
            font-size: 3.5vw;
        }

        .about_sidebar_box .vision-content,
        .about_sidebar_box .mission-content {
            font-size: 2.5vw;
        }

        .about_sidebar_box .vision-content ul li,
        .about_sidebar_box .mission-content ul li {
            font-size: 2.5vw;
        }

        .about_sidebar_box ul li {
            font-size: 2.5vw;
        }

        .about_sidebar_box ul li iconify-icon {
            font-size: 3.5vw;
        }
    }

    @media (max-width: 480px) {
        .about_content_container {
            padding: 3vw 3vw;
        }

        .about_top_container {
            padding: 1.3vw 3vw;
        }

        .about_main_content .content {
            font-size: 3vw;
        }

        .about_sidebar_box h3 {
            font-size: 4vw;
        }

        .about_sidebar_box .vision-content,
        .about_sidebar_box .mission-content {
            font-size: 2.8vw;
        }

        .about_sidebar_box .vision-content ul li,
        .about_sidebar_box .mission-content ul li {
            font-size: 2.8vw;
        }

        .about_sidebar_box ul li {
            font-size: 2.8vw;
        }
    }
</style>

<div class="about_container">
    {{-- HEADER --}}
    <div class="about_top_container">
        <div class="about-header">
            <h1>{{ $about->title }}</h1>
            <p>Mengetahui lebih dalam tentang perjalanan dan komitmen kami</p>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="about_content_container">
        {{-- MAIN CONTENT --}}
        <div class="about_main_content">
            <div class="content">
                {!! $about->content !!}
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="about_sidebar">
            {{-- VISI --}}
            @if($about->vision)
                <div class="about_sidebar_box">
                    <h3>Visi Kami</h3>
                    <div class="vision-content">
                        {!! $about->vision !!}
                    </div>
                </div>
            @endif

            {{-- MISI --}}
            @if($about->mission)
                <div class="about_sidebar_box">
                    <h3>Misi Kami</h3>
                    <div class="mission-content">
                        {!! $about->mission !!}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection