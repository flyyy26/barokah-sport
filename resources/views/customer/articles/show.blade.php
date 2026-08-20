@extends('layouts.customer')

@section('title', $article->title . ' - Barokah Sport')

@section('content')

<style>
    /* ============================================
       LAYOUT UTAMA 75% - 25%
       ============================================ */
    .article-detail-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2vw 7.54vw 4vw;
        display: grid;
        grid-template-columns: 75% 25%;
        gap: 2.5vw;
    }

    /* ============================================
       KONTEN UTAMA
       ============================================ */
    .article-main {
        min-width: 0;
    }

    .article-detail-header {
        margin-bottom: 1.5vw;
    }

    .article-detail-header .article-back {
        display: inline-flex;
        align-items: center;
        gap: 0.4vw;
        color: #076694;
        text-decoration: none;
        font-size: 0.8vw;
        margin-bottom: 0.8vw;
        transition: color 0.2s;
        font-weight: 500;
    }

    .article-detail-header .article-back:hover {
        color: #055a7a;
    }

    .article-detail-header .article-category {
        display: inline-block;
        padding: 0.2vw 1vw;
        border-radius: 100vw;
        font-size: 0.65vw;
        font-weight: 600;
        color: #076694;
        background: #e0f2fe;
        margin-bottom: 0.5vw;
        text-transform: uppercase;
        letter-spacing: 0.05vw;
    }

    .article-detail-header h1 {
        font-size: 2.2vw;
        font-weight: 700;
        color: #0f172a;
        margin: 0.5vw 0;
        line-height: 1.3;
    }

    .article-detail-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 1.5vw;
        margin-top: 0.8vw;
        font-size: 0.75vw;
        color: #94a3b8;
        padding-bottom: 1vw;
        border-bottom: 0.1vw solid #f1f5f9;
    }

    .article-detail-meta .meta-item {
        display: flex;
        align-items: center;
        gap: 0.3vw;
    }

    .article-detail-meta .meta-item iconify-icon {
        font-size: 0.9vw;
    }

    .article-detail-meta .meta-divider {
        width: 0.1vw;
        height: 1.2vw;
        background: #e2e8f0;
    }

    .article-detail-image {
        width: 100%;
        aspect-ratio: 16/8;
        border-radius: 0.8vw;
        overflow: hidden;
        margin: 1.2vw 0;
        background: #f1f5f9;
        position: relative;
    }

    .article-detail-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .article-detail-image .image-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        color: #94a3b8;
        font-size: 4vw;
    }

    .article-detail-content {
        font-size: 0.95vw;
        line-height: 1.5;
        color: #1e293b;
    }

    .article-detail-content h2 {
        font-size: 1.4vw;
        margin: 1.8vw 0 0.6vw;
        color: #0f172a;
        font-weight: 700;
    }

    .article-detail-content h3 {
        font-size: 1.1vw;
        margin: 1.2vw 0 0.5vw;
        color: #0f172a;
        font-weight: 600;
    }

    .article-detail-content ul,
    .article-detail-content ol {
        margin: 0.5vw 0 0.8vw 1.5vw;
    }

    .article-detail-content li {
        margin-bottom: 0.3vw;
    }

    .article-detail-content img {
        max-width: 100%;
        border-radius: 0.5vw;
        margin: 0.8vw 0;
    }

    .article-detail-content blockquote {
        border-left: 0.3vw solid #076694;
        padding: 0.8vw 1.2vw;
        margin: 1vw 0;
        background: #f8fafc;
        border-radius: 0 0.4vw 0.4vw 0;
        font-style: italic;
        color: #475569;
    }

    .article-detail-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1vw 0;
        font-size: 0.85vw;
    }

    .article-detail-content table th,
    .article-detail-content table td {
        padding: 0.5vw 0.8vw;
        border: 0.05vw solid #e2e8f0;
        text-align: left;
    }

    .article-detail-content table th {
        background: #f1f5f9;
        font-weight: 600;
    }

    .article-detail-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4vw;
        margin: 2vw 0 1vw;
        padding-top: 1.5vw;
        border-top: 0.1vw solid #e2e8f0;
    }

    .article-detail-tags .tag-label {
        font-size: 0.75vw;
        font-weight: 600;
        color: #475569;
        margin-right: 0.3vw;
    }

    .article-detail-tags .tag {
        padding: 0.2vw 0.8vw;
        border-radius: 100vw;
        font-size: 0.65vw;
        color: #475569;
        background: #f1f5f9;
        border: 0.05vw solid #e2e8f0;
        transition: all 0.2s;
        text-decoration: none;
    }

    .article-detail-tags .tag:hover {
        background: #076694;
        color: #ffffff;
        border-color: #076694;
    }

    /* ============================================
       SHARE BUTTONS
       ============================================ */
    .article-share {
        display: flex;
        align-items: center;
        gap: 0.8vw;
        margin: 1.5vw 0 2vw;
        padding: 1vw 1.5vw;
        background: #f8fafc;
        border-radius: 0.6vw;
        border: 0.05vw solid #e2e8f0;
        flex-wrap: wrap;
    }

    .article-share .share-label {
        font-size: 0.75vw;
        font-weight: 600;
        color: #475569;
    }

    .article-share .share-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.2vw;
        height: 2.2vw;
        border-radius: 50%;
        border: none;
        color: #ffffff;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        font-size: 1vw;
    }

    .article-share .share-btn:hover {
        transform: scale(1.1);
    }

    .article-share .share-btn.facebook {
        background: #1877f2;
    }
    .article-share .share-btn.twitter {
        background: #000000;
    }
    .article-share .share-btn.whatsapp {
        background: #25d366;
    }
    .article-share .share-btn.telegram {
        background: #0088cc;
    }
    .article-share .share-btn.email {
        background: #ea4335;
    }

    /* ============================================
       SIDEBAR
       ============================================ */
    .article-sidebar {
        min-width: 0;
    }

    .sidebar-widget {
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.8vw;
        padding: 1.2vw;
        margin-bottom: 1.5vw;
    }

    .sidebar-widget .widget-title {
        font-size: 0.9vw;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 0.8vw 0;
        padding-bottom: 0.6vw;
        border-bottom: 0.15vw solid #076694;
        display: flex;
        align-items: center;
        gap: 0.4vw;
    }

    .sidebar-widget .widget-title iconify-icon {
        color: #076694;
        font-size: 1.1vw;
    }

    /* Search Widget */
    .widget-search-form {
        display: flex;
        gap: 0.5vw;
    }

    .widget-search-form input {
        flex: 1;
        padding: 0.4vw 0.8vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.4vw;
        font-size: 0.75vw;
        outline: none;
        transition: border-color 0.2s;
    }

    .widget-search-form input:focus {
        border-color: #076694;
        box-shadow: 0 0 0 0.15vw rgba(7, 102, 148, 0.1);
    }

    .widget-search-form button {
        padding: 0.4vw 1vw;
        background: #076694;
        color: #ffffff;
        border: none;
        border-radius: 0.4vw;
        font-size: 0.75vw;
        cursor: pointer;
        transition: background 0.2s;
    }

    .widget-search-form button:hover {
        background: #055a7a;
    }

    /* Category Widget */
    .category-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .category-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.4vw 0;
        border-bottom: 0.05vw solid #f1f5f9;
    }

    .category-list li:last-child {
        border-bottom: none;
    }

    .category-list li a {
        color: #475569;
        text-decoration: none;
        font-size: 0.75vw;
        transition: color 0.2s;
    }

    .category-list li a:hover {
        color: #076694;
    }

    .category-list li .count {
        font-size: 0.6vw;
        color: #94a3b8;
        background: #f1f5f9;
        padding: 0.1vw 0.5vw;
        border-radius: 100vw;
    }

    /* Article List Widget */
    .widget-article-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .widget-article-list li {
        display: flex;
        gap: 0.8vw;
        padding: 0.6vw 0;
        border-bottom: 0.05vw solid #f1f5f9;
    }

    .widget-article-list li:last-child {
        border-bottom: none;
    }

    .widget-article-list .widget-article-image {
        width: 4.5vw;
        height: 4.5vw;
        border-radius: 0.4vw;
        overflow: hidden;
        flex-shrink: 0;
        background: #f1f5f9;
    }

    .widget-article-list .widget-article-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .widget-article-list .widget-article-image .no-image {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        color: #cbd5e1;
        font-size: 1.5vw;
    }

    .widget-article-list .widget-article-info {
        flex: 1;
        min-width: 0;
    }

    .widget-article-list .widget-article-info h4 {
        font-size: 0.75vw;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 0.2vw 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.3;
    }

    .widget-article-list .widget-article-info h4 a {
        color: #0f172a;
        text-decoration: none;
        transition: color 0.2s;
    }

    .widget-article-list .widget-article-info h4 a:hover {
        color: #076694;
    }

    .widget-article-list .widget-article-info .widget-article-meta {
        font-size: 0.6vw;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 0.4vw;
    }

    .widget-article-list .widget-article-info .widget-article-meta iconify-icon {
        font-size: 0.65vw;
    }

    /* Popular - with ranking number */
    .popular-list {
        list-style: none;
        padding: 0;
        margin: 0;
        counter-reset: popular-counter;
    }

    .popular-list li {
        counter-increment: popular-counter;
        display: flex;
        gap: 0.8vw;
        padding: 0.5vw 0;
        border-bottom: 0.05vw solid #f1f5f9;
        align-items: center;
    }

    .popular-list li:last-child {
        border-bottom: none;
    }

    .popular-list .popular-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 1.8vw;
        height: 1.8vw;
        border-radius: 50%;
        background: #f1f5f9;
        color: #475569;
        font-size: 0.65vw;
        font-weight: 700;
        flex-shrink: 0;
    }

    .popular-list li:nth-child(1) .popular-number {
        background: #f59e0b;
        color: #ffffff;
    }
    .popular-list li:nth-child(2) .popular-number {
        background: #94a3b8;
        color: #ffffff;
    }
    .popular-list li:nth-child(3) .popular-number {
        background: #cd7f32;
        color: #ffffff;
    }

    .popular-list .popular-info {
        flex: 1;
        min-width: 0;
    }

    .popular-list .popular-info h4 {
        font-size: 0.75vw;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 0.1vw 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.3;
    }

    .popular-list .popular-info h4 a {
        color: #0f172a;
        text-decoration: none;
        transition: color 0.2s;
    }

    .popular-list .popular-info h4 a:hover {
        color: #076694;
    }

    .popular-list .popular-info .popular-views {
        font-size: 0.6vw;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 0.2vw;
    }

    /* Badge Rekomendasi */
    .recommendation-badge {
        display: inline-block;
        padding: 0.1vw 0.5vw;
        border-radius: 0.2vw;
        font-size: 0.5vw;
        font-weight: 600;
        color: #ffffff;
        background: #8b5cf6;
        margin-left: 0.3vw;
        vertical-align: middle;
    }

    .widget-divider {
        height: 0.1vw;
        background: linear-gradient(to right, #e2e8f0, #076694, #e2e8f0);
        margin: 1.5vw 0;
        opacity: 0.3;
    }

    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 1200px) {
        .article-detail-wrapper {
            padding: 2vw 4vw 4vw;
            gap: 2vw;
        }
    }

    @media (max-width: 1024px) {
        .article-detail-wrapper {
            grid-template-columns: 70% 30%;
            padding: 2vw 3vw 4vw;
        }
    }

    @media (max-width: 768px) {
        .article-detail-wrapper {
            grid-template-columns: 1fr;
            padding: 2vw 3vw 4vw;
            gap: 2vw;
        }

        .article-detail-header h1 {
            font-size: 3.5vw;
        }

        .article-detail-meta {
            font-size: 1.2vw;
            gap: 1.2vw;
        }

        .article-detail-content {
            font-size: 1.3vw;
        }

        .article-detail-content h2 {
            font-size: 1.8vw;
        }

        .article-detail-content h3 {
            font-size: 1.5vw;
        }

        .article-share .share-btn {
            width: 3vw;
            height: 3vw;
            font-size: 1.3vw;
        }

        /* Sidebar */
        .sidebar-widget .widget-title {
            font-size: 1.4vw;
        }

        .widget-search-form input {
            font-size: 1.1vw;
            padding: 0.6vw 1vw;
        }

        .widget-search-form button {
            font-size: 1.1vw;
            padding: 0.6vw 1.2vw;
        }

        .category-list li a {
            font-size: 1.1vw;
        }

        .widget-article-list .widget-article-info h4 {
            font-size: 1.1vw;
        }

        .widget-article-list .widget-article-info .widget-article-meta {
            font-size: 0.9vw;
        }

        .popular-list .popular-info h4 {
            font-size: 1.1vw;
        }
    }

    @media (max-width: 480px) {
        .article-detail-wrapper {
            padding: 3vw 2vw 5vw;
        }

        .article-detail-header h1 {
            font-size: 4.5vw;
        }

        .article-detail-meta {
            font-size: 1.6vw;
            gap: 1.5vw;
        }

        .article-detail-meta .meta-item iconify-icon {
            font-size: 1.4vw;
        }

        .article-detail-content {
            font-size: 1.8vw;
        }

        .article-detail-content h2 {
            font-size: 2.4vw;
        }

        .article-detail-content h3 {
            font-size: 2vw;
        }

        .article-detail-tags .tag {
            font-size: 1.2vw;
            padding: 0.3vw 1.2vw;
        }

        .article-share {
            flex-wrap: wrap;
            padding: 1.5vw 2vw;
        }

        .article-share .share-label {
            font-size: 1.3vw;
        }

        .article-share .share-btn {
            width: 4.5vw;
            height: 4.5vw;
            font-size: 2vw;
        }

        /* Sidebar */
        .sidebar-widget {
            padding: 2vw;
        }

        .sidebar-widget .widget-title {
            font-size: 2vw;
        }

        .sidebar-widget .widget-title iconify-icon {
            font-size: 2vw;
        }

        .widget-search-form input {
            font-size: 1.6vw;
            padding: 0.8vw 1.5vw;
        }

        .widget-search-form button {
            font-size: 1.6vw;
            padding: 0.8vw 1.8vw;
        }

        .category-list li a {
            font-size: 1.6vw;
        }

        .category-list li .count {
            font-size: 1.2vw;
        }

        .widget-article-list li {
            gap: 1.2vw;
            padding: 1vw 0;
        }

        .widget-article-list .widget-article-image {
            width: 8vw;
            height: 8vw;
        }

        .widget-article-list .widget-article-info h4 {
            font-size: 1.6vw;
        }

        .widget-article-list .widget-article-info .widget-article-meta {
            font-size: 1.3vw;
        }

        .popular-list .popular-number {
            width: 3.5vw;
            height: 3.5vw;
            font-size: 1.2vw;
        }

        .popular-list .popular-info h4 {
            font-size: 1.6vw;
        }

        .popular-list .popular-info .popular-views {
            font-size: 1.2vw;
        }
    }
</style>

{{-- ============================================ --}}
{{-- LAYOUT UTAMA 75% - 25% --}}
{{-- ============================================ --}}
<div class="article-detail-wrapper">
    {{-- ============================================ --}}
    {{-- KONTEN UTAMA (75%) --}}
    {{-- ============================================ --}}
    <div class="article-main">
        <div class="article-detail-header">
            <a href="{{ route('customer.articles.index') }}" class="article-back">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali ke Artikel
            </a>

            <h1>{{ $article->title }}</h1>

            <div class="article-detail-meta">
                <span class="meta-item">
                    <iconify-icon icon="mdi:user"></iconify-icon>
                    {{ $article->author ?? 'Admin' }}
                </span>
                <span class="meta-divider"></span>
                <span class="meta-item">
                    <iconify-icon icon="lets-icons:date-fill"></iconify-icon>
                    {{ $article->formatted_published_at }}
                </span>
                <span class="meta-divider"></span>
                <span class="meta-item">
                    <iconify-icon icon="mdi:eye"></iconify-icon>
                    <span id="article-views-count">{{ number_format($article->views ?? 0) }}</span> dilihat
                </span>
                @if($article->articleCategory)
                    <span class="meta-divider"></span>
                    <span class="meta-item">
                        <iconify-icon icon="mdi:folder"></iconify-icon>
                        {{ $article->articleCategory->name }}
                    </span>
                @endif
            </div>
        </div>

        @if($article->image)
            <div class="article-detail-image">
                <img src="{{ Storage::url($article->image) }}" alt="{{ $article->title }}">
            </div>
        @else
            <div class="article-detail-image">
                <div class="image-placeholder">
                    <iconify-icon icon="mdi:newspaper-variant-outline"></iconify-icon>
                </div>
            </div>
        @endif

        <div class="article-detail-content">
            {!! preg_replace('/<p>\s*(&nbsp;|\s)*\s*<\/p>/i', '', $article->content) !!}
        </div>

        @if($article->tags && count($article->tags) > 0)
            <div class="article-detail-tags">
                <span class="tag-label">Tags:</span>
                @foreach($article->tags as $tag)
                    <a href="{{ route('customer.articles.index', ['tag' => $tag]) }}" class="tag">#{{ $tag }}</a>
                @endforeach
            </div>
        @endif

        {{-- SHARE BUTTONS --}}
        <div class="article-share">
            <span class="share-label">Bagikan:</span>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
               target="_blank" class="share-btn facebook">
                <iconify-icon icon="mdi:facebook"></iconify-icon>
            </a>
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}" 
               target="_blank" class="share-btn twitter">
                <iconify-icon icon="mdi:twitter"></iconify-icon>
            </a>
            <a href="https://api.whatsapp.com/send?text={{ urlencode($article->title . ' - ' . url()->current()) }}" 
               target="_blank" class="share-btn whatsapp">
                <iconify-icon icon="mdi:whatsapp"></iconify-icon>
            </a>
            <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}" 
               target="_blank" class="share-btn telegram">
                <iconify-icon icon="mdi:telegram"></iconify-icon>
            </a>
            <a href="mailto:?subject={{ urlencode($article->title) }}&body={{ urlencode($article->title . '\n\n' . url()->current()) }}" 
               class="share-btn email">
                <iconify-icon icon="mdi:email"></iconify-icon>
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SIDEBAR (25%) --}}
    {{-- ============================================ --}}
    <div class="article-sidebar">
        {{-- WIDGET 1: SEARCH --}}
        <div class="sidebar-widget">
            <div class="widget-title">
                <iconify-icon icon="mdi:search"></iconify-icon>
                Cari Artikel
            </div>
            <form action="{{ route('customer.articles.index') }}" method="GET" class="widget-search-form">
                <input type="text" name="search" placeholder="Cari artikel..." value="{{ request('search') }}">
                <button type="submit">
                    <iconify-icon icon="mdi:search"></iconify-icon>
                </button>
            </form>
        </div>

        {{-- WIDGET 2: KATEGORI --}}
        @if($categories->isNotEmpty())
            <div class="sidebar-widget">
                <div class="widget-title">
                    <iconify-icon icon="mdi:folder"></iconify-icon>
                    Kategori
                </div>
                <ul class="category-list">
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('customer.articles.index', ['category' => $category->id]) }}">
                                {{ $category->name }}
                            </a>
                            <span class="count">{{ $category->articles_count }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- WIDGET 3: ARTIKEL POPULER --}}
        @if($popularArticles->isNotEmpty())
            <div class="sidebar-widget">
                <div class="widget-title">
                    <iconify-icon icon="mdi:fire"></iconify-icon>
                    Populer
                </div>
                <ol class="popular-list">
                    @foreach($popularArticles as $popular)
                        <li>
                            <span class="popular-number">{{ $loop->iteration }}</span>
                            <div class="popular-info">
                                <h4>
                                    <a href="{{ route('customer.articles.show', $popular->slug) }}">
                                        {{ $popular->title }}
                                    </a>
                                </h4>
                                <span class="popular-views">
                                    <iconify-icon icon="mdi:eye"></iconify-icon>
                                    {{ number_format($popular->views ?? 0) }} dilihat
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif

        {{-- WIDGET 4: REKOMENDASI ARTIKEL LAIN (MIX) --}}
        @if($recommendedArticles->isNotEmpty() || $tagRelatedArticles->isNotEmpty())
            <div class="sidebar-widget">
                <div class="widget-title">
                    <iconify-icon icon="mdi:star"></iconify-icon>
                    Rekomendasi Untukmu
                </div>
                <ul class="widget-article-list">
                    @if($tagRelatedArticles->isNotEmpty())
                        {{-- Artikel dengan tags yang sama --}}
                        @foreach($tagRelatedArticles as $related)
                            <li>
                                <div class="widget-article-image">
                                    @if($related->image)
                                        <img src="{{ Storage::url($related->image) }}" alt="{{ $related->title }}">
                                    @else
                                        <div class="no-image">
                                            <iconify-icon icon="mdi:newspaper-variant-outline"></iconify-icon>
                                        </div>
                                    @endif
                                </div>
                                <div class="widget-article-info">
                                    <h4>
                                        <a href="{{ route('customer.articles.show', $related->slug) }}">
                                            {{ $related->title }}
                                        </a>
                                        <span class="recommendation-badge">Tag</span>
                                    </h4>
                                    <span class="widget-article-meta">
                                        <iconify-icon icon="lets-icons:date-fill"></iconify-icon>
                                        {{ $related->formatted_published_at }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    @endif

                    @if($recommendedArticles->isNotEmpty())
                        {{-- Artikel dari kategori lain --}}
                        @foreach($recommendedArticles as $recommended)
                            <li>
                                <div class="widget-article-image">
                                    @if($recommended->image)
                                        <img src="{{ Storage::url($recommended->image) }}" alt="{{ $recommended->title }}">
                                    @else
                                        <div class="no-image">
                                            <iconify-icon icon="mdi:newspaper-variant-outline"></iconify-icon>
                                        </div>
                                    @endif
                                </div>
                                <div class="widget-article-info">
                                    <h4>
                                        <a href="{{ route('customer.articles.show', $recommended->slug) }}">
                                            {{ $recommended->title }}
                                        </a>
                                        <span class="recommendation-badge">Rekomendasi</span>
                                    </h4>
                                    <span class="widget-article-meta">
                                        <iconify-icon icon="lets-icons:date-fill"></iconify-icon>
                                        {{ $recommended->formatted_published_at }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const articleId = {{ $article->id }};
    const storageKey = 'article_viewed_' + articleId;
    const viewsKey = 'article_views_count_' + articleId;
    
    // 🔥 AMBIL VIEWS DARI SERVER (DATABASE)
    const serverViews = {{ $article->views ?? 0 }};
    
    // 🔥 SET VIEWS DARI SERVER TERLEBIH DAHULU
    const viewsSpan = document.getElementById('article-views-count');
    if (viewsSpan) {
        viewsSpan.textContent = new Intl.NumberFormat('id-ID').format(serverViews);
    }
    
    // 🔥 CEK APAKAH ARTIKEL SUDAH PERNAH DIBACA
    const alreadyViewed = localStorage.getItem(storageKey);
    
    if (!alreadyViewed) {
        // 🔥 TANDAI ARTIKEL SUDAH DIBACA
        localStorage.setItem(storageKey, 'true');
        
        // 🔥 KIRIM REQUEST KE SERVER UNTUK MENAMBAH VIEWS
        fetch('{{ route("customer.articles.record-view") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                article_id: articleId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Views recorded:', data.views);
                
                // 🔥 SIMPAN JUMLAH VIEWS TERBARU DI LOCALSTORAGE
                localStorage.setItem(viewsKey, data.views);
                
                // 🔥 UPDATE ANGKA VIEWS DI HALAMAN DENGAN DATA DARI SERVER
                if (viewsSpan) {
                    viewsSpan.textContent = new Intl.NumberFormat('id-ID').format(data.views);
                }
            }
        })
        .catch(error => {
            console.error('❌ Error recording view:', error);
        });
    } else {
        console.log('ℹ️ Article already viewed, skip counting');
        
        // 🔥 TETAP GUNAKAN VIEWS DARI SERVER, BUKAN LOCALSTORAGE
        // LocalStorage hanya untuk tracking, bukan untuk menampilkan data
        if (viewsSpan) {
            viewsSpan.textContent = new Intl.NumberFormat('id-ID').format(serverViews);
        }
    }
    
    console.log('📊 Article view tracking initialized with LocalStorage');
    console.log('📊 Current views from database:', serverViews);
});
</script>

@endsection