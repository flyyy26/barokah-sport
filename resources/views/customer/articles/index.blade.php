@extends('layouts.customer')

@section('title', 'Artikel - Barokah Sport')

@section('content')

<style>
    /* ============================================
       CATALOG CONTAINER (sama seperti produk)
       ============================================ */
    .catalog-container {
        width: 100%;
        margin: 0 auto;
        border-top: 0.1vw solid #076694;
    }

    .catalog-header {
        margin-bottom: 1.5vw;
    }

    .catalog-header h1 {
        font-size: 2.6vw;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        font-family: heading, sans-serif;
        text-transform: uppercase;
    }

    .catalog-header p {
        font-size: 0.85vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }

    /* ============================================
       FILTER ROW
       ============================================ */
    .katalog_top_container {
        width: 100%;
        padding: 1.3vw 7.54vw;
        background: #f9fafb;
    }

    .filter-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.8vw;
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.8vw;
        padding: 0.8vw 1.2vw;
    }

    .filter-row .filter-label {
        font-size: 0.75vw;
        font-weight: 600;
        color: #475569;
        margin-right: 0.2vw;
        white-space: nowrap;
    }

    /* ============================================
       CUSTOM SELECT (sama seperti produk)
       ============================================ */
    .custom-select-wrapper {
        position: relative;
        display: inline-block;
        min-width: 8vw;
    }

    .custom-select-wrapper .custom-select-trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.4vw .8vw 0.4vw 0.8vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.5vw;
        font-size: 0.75vw;
        color: #0f172a;
        background: #f8fafc;
        cursor: pointer;
        transition: border-color 0.2s;
        user-select: none;
        min-height: 2.2vw;
        gap: 0.5vw;
    }

    .custom-select-wrapper .custom-select-trigger:hover {
        border-color: #94a3b8;
    }

    .custom-select-wrapper .custom-select-trigger.open {
        border-color: #076694;
        box-shadow: 0 0 0 0.15vw rgba(7, 102, 148, 0.2);
    }

    .custom-select-wrapper .custom-select-trigger .trigger-text {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .custom-select-wrapper .custom-select-trigger .trigger-arrow {
        font-size: 0.6vw;
        color: #94a3b8;
        transition: transform 0.3s ease;
        flex-shrink: 0;
    }

    .custom-select-wrapper .custom-select-trigger.open .trigger-arrow {
        transform: rotate(180deg);
    }

    .custom-select-wrapper .custom-select-dropdown {
        position: absolute;
        top: calc(100% + 0.2vw);
        left: 0;
        right: 0;
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.5vw;
        box-shadow: 0 0.5vw 2vw rgba(0, 0, 0, 0.1);
        max-height: 20vw;
        overflow-y: auto;
        z-index: 999;
        display: none;
        min-width: 12vw;
    }

    .custom-select-wrapper .custom-select-dropdown.open {
        display: block;
    }

    .custom-select-wrapper .custom-select-dropdown .dropdown-item {
        padding: 0.4vw 1vw;
        font-size: 0.75vw;
        color: #0f172a;
        cursor: pointer;
        transition: background 0.15s;
        display: flex;
        align-items: center;
        gap: 0.4vw;
    }

    .custom-select-wrapper .custom-select-dropdown .dropdown-item:hover {
        background: #f1f5f9;
    }

    .custom-select-wrapper .custom-select-dropdown .dropdown-item.active {
        background: #eff6ff;
        color: #076694;
        font-weight: 600;
    }

    .custom-select-wrapper .custom-select-dropdown .dropdown-item .check-icon {
        margin-left: auto;
        color: #076694;
        font-size: 0.7vw;
        opacity: 0;
    }

    .custom-select-wrapper .custom-select-dropdown .dropdown-item.active .check-icon {
        opacity: 1;
    }

    .filter-divider {
        width: 0.05vw;
        height: 1.8vw;
        background: #e2e8f0;
        flex-shrink: 0;
    }

    .filter-reset {
        font-size: 0.7vw;
        color: #ef4444;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
        white-space: nowrap;
    }

    .filter-reset:hover {
        color: #dc2626;
        text-decoration: underline;
    }

    .filter-count {
        font-size: 0.75vw;
        color: #94a3b8;
        margin-left: auto;
        white-space: nowrap;
    }

    /* ============================================
       ARTICLE GRID
       ============================================ */
    .article-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2.3vw;
        padding: 2vw 7.54vw;
        padding-bottom:5vw;
    }

    /* ============================================
       ARTICLE CARD
       ============================================ */
    .article-card {
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.8vw;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
    }

    .article-card:hover {
        box-shadow: 0 0.3vw 1.5vw rgba(0, 0, 0, 0.08);
        transform: translateY(-0.2vw);
    }

    .article-card .article-image {
        aspect-ratio: 16/9;
        overflow: hidden;
        background: #f8fafc;
        position: relative;
    }

    .article-card .article-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .article-card:hover .article-image img {
        transform: scale(1.05);
    }

    .article-card .article-image .placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-size: 3vw;
        color: #cbd5e1;
        background: #f1f5f9;
    }

    .article-card .article-badge {
        position: absolute;
        top: 0.8vw;
        right: 0.8vw;
        padding: 0.2vw 0.8vw;
        border-radius: 0.3vw;
        font-size: 0.55vw;
        font-weight: 700;
        text-transform: uppercase;
        color: #ffffff;
        z-index: 2;
        background: #f59e0b;
    }

    .article-card .article-badge.featured {
        background: #f59e0b;
    }

    .article-card .article-content {
        padding: 1.2vw;
    }

    .article-card .article-meta {
        display: flex;
        gap: 1vw;
        margin-bottom: 0.5vw;
        font-size: 0.6vw;
        color: #94a3b8;
    }

    .article-card .article-meta .meta-item {
        display: flex;
        align-items: center;
        gap: 0.3vw;
    }

    .article-card .article-meta .meta-item iconify-icon {
        font-size: 0.7vw;
    }

    .article-card .article-category {
        display: inline-block;
        padding: 0.1vw 0.6vw;
        border-radius: 0.2vw;
        font-size: 0.55vw;
        font-weight: 600;
        color: #076694;
        background: #e0f2fe;
        margin-bottom: 0.4vw;
    }

    .article-card .article-title {
        font-size: 1.1vw;
        font-weight: 700;
        color: #0f172a;
        margin: 0.3vw 0;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .article-card .article-title a {
        color: #0f172a;
        text-decoration: none;
        transition: color 0.2s;
    }

    .article-card .article-title a:hover {
        color: #076694;
    }

    .article-card .article-excerpt {
        font-size: 0.75vw;
        color: #64748b;
        line-height: 1.6;
        margin: 0.4vw 0 0.8vw 0;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .article-card .article-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 0.6vw;
        border-top: 0.05vw solid #f1f5f9;
    }

    .article-card .article-footer .btn-read {
        padding: 0.3vw 1vw;
        border-radius: 0.4vw;
        border: none;
        background: #076694;
        color: #ffffff;
        font-size: 0.6vw;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.05vw;
        display: inline-block;
    }

    .article-card .article-footer .btn-read:hover {
        background: #055a7a;
    }

    .article-card .article-footer .article-views {
        font-size: 0.6vw;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 0.2vw;
    }

    .article-card .article-footer .article-views iconify-icon {
        font-size: 0.7vw;
    }

    /* ============================================
       PAGINATION
       ============================================ */
    .pagination-wrapper {
        margin-top: 2vw;
        display: flex;
        justify-content: center;
        padding-bottom: 2vw;
    }

    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4vw 0;
        color: #94a3b8;
    }

    .empty-state .empty-icon {
        font-size: 4vw;
        margin-bottom: 1vw;
    }

    .empty-state p {
        font-size: 1.2vw;
    }

    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 1200px) {
        .article-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 1024px) {
        .katalog_top_container {
            padding: 1.5vw 3vw;
        }

        .filter-row {
            padding: 1vw 1.5vw;
            gap: 1vw;
        }

        .custom-select-wrapper {
            min-width: 10vw;
        }
    }

    @media (max-width: 768px) {
        .katalog_top_container {
            padding: 2vw 3vw;
        }

        .catalog-header h1 {
            font-size: 2.8vw;
        }

        .catalog-header p {
            font-size: 1.4vw;
        }

        .filter-row {
            padding: 1.2vw 1.8vw;
            gap: 1.2vw;
            flex-wrap: wrap;
        }

        .filter-row .filter-label {
            font-size: 1.2vw;
        }

        .custom-select-wrapper {
            min-width: 14vw;
        }

        .custom-select-wrapper .custom-select-trigger {
            font-size: 1.2vw;
            padding: 0.6vw 2.5vw 0.6vw 1.2vw;
            min-height: 3vw;
        }

        .custom-select-wrapper .custom-select-dropdown .dropdown-item {
            font-size: 1.2vw;
            padding: 0.6vw 1.2vw;
        }

        .filter-divider {
            height: 2.5vw;
        }

        .filter-count {
            font-size: 1.2vw;
        }

        .filter-reset {
            font-size: 1.2vw;
        }

        .article-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5vw;
            padding: 2vw 3vw;
        }

        .article-card .article-title {
            font-size: 1.4vw;
        }

        .article-card .article-excerpt {
            font-size: 1vw;
        }
    }

    @media (max-width: 480px) {
        .katalog_top_container {
            padding: 2vw 2vw;
        }

        .catalog-header h1 {
            font-size: 3.6vw;
        }

        .catalog-header p {
            font-size: 1.8vw;
        }

        .filter-row {
            padding: 1.5vw 2vw;
            gap: 1.5vw;
            border-radius: 1vw;
        }

        .filter-row .filter-label {
            font-size: 1.6vw;
        }

        .custom-select-wrapper {
            min-width: 20vw;
            flex: 1;
        }

        .custom-select-wrapper .custom-select-trigger {
            font-size: 1.6vw;
            padding: 0.8vw 3.5vw 0.8vw 1.5vw;
            min-height: 4vw;
            border-radius: 0.8vw;
        }

        .custom-select-wrapper .custom-select-dropdown .dropdown-item {
            font-size: 1.6vw;
            padding: 0.8vw 1.5vw;
        }

        .filter-divider {
            display: none;
        }

        .filter-count {
            font-size: 1.6vw;
            width: 100%;
            text-align: center;
            margin-top: 0.5vw;
        }

        .filter-reset {
            font-size: 1.6vw;
        }

        .article-grid {
            grid-template-columns: 1fr;
            gap: 2vw;
            padding: 2vw 2vw;
        }

        .article-card .article-title {
            font-size: 2.4vw;
        }

        .article-card .article-excerpt {
            font-size: 1.8vw;
        }
    }
</style>

<div class="catalog-container">
    <div class="katalog_top_container">
        <div class="catalog-header">
            <h1>Artikel</h1>
            <p>Informasi dan tips seputar olahraga dari Barokah Sport</p>
        </div>

        {{-- ============================================ --}}
        {{-- FILTER ROW - CUSTOM SELECT --}}
        {{-- ============================================ --}}
        <form id="filter-form" method="GET" action="{{ route('customer.articles.index') }}" class="filter-row">
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            {{-- KATEGORI --}}
            <span class="filter-label">Kategori</span>
            <div class="custom-select-wrapper" data-name="category">
                <div class="custom-select-trigger">
                    <span class="trigger-text">
                        {{ request('category') ? $categories->firstWhere('id', request('category'))->name ?? 'Semua' : 'Semua' }}
                    </span>
                    <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                </div>
                <div class="custom-select-dropdown">
                    <div class="dropdown-item {{ !request('category') ? 'active' : '' }}" data-value="">
                        <span>Semua</span>
                        <span class="check-icon">✓</span>
                    </div>
                    @foreach ($categories as $category)
                        <div class="dropdown-item {{ request('category') == $category->id ? 'active' : '' }}" 
                             data-value="{{ $category->id }}">
                            <span>{{ $category->name }}</span>
                            <span class="check-icon">✓</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="filter-divider"></div>

            {{-- SORT BY --}}
            <span class="filter-label">Urutkan</span>
            <div class="custom-select-wrapper" data-name="sort">
                <div class="custom-select-trigger">
                    <span class="trigger-text">
                        @php
                            $sortOptions = [
                                'newest' => 'Terbaru',
                                'oldest' => 'Terlama',
                                'title_asc' => 'Judul (A-Z)',
                                'title_desc' => 'Judul (Z-A)'
                            ];
                        @endphp
                        {{ $sortOptions[request('sort', 'newest')] ?? 'Terbaru' }}
                    </span>
                    <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                </div>
                <div class="custom-select-dropdown">
                    <div class="dropdown-item {{ request('sort', 'newest') == 'newest' ? 'active' : '' }}" data-value="newest">
                        <span>Terbaru</span>
                        <span class="check-icon">✓</span>
                    </div>
                    <div class="dropdown-item {{ request('sort') == 'oldest' ? 'active' : '' }}" data-value="oldest">
                        <span>Terlama</span>
                        <span class="check-icon">✓</span>
                    </div>
                    <div class="dropdown-item {{ request('sort') == 'title_asc' ? 'active' : '' }}" data-value="title_asc">
                        <span>Judul (A-Z)</span>
                        <span class="check-icon">✓</span>
                    </div>
                    <div class="dropdown-item {{ request('sort') == 'title_desc' ? 'active' : '' }}" data-value="title_desc">
                        <span>Judul (Z-A)</span>
                        <span class="check-icon">✓</span>
                    </div>
                </div>
            </div>

            {{-- RESET FILTER --}}
            @if(request()->anyFilled(['category', 'sort']))
                <a href="{{ route('customer.articles.index') }}" class="filter-reset">
                    ✕ Reset
                </a>
            @endif

            <span class="filter-count">{{ $articles->total() }} artikel</span>
        </form>
    </div>

    {{-- ============================================ --}}
    {{-- ARTICLE GRID --}}
    {{-- ============================================ --}}
    <div class="article-grid">
        @forelse ($articles as $article)
            <div class="artikel_section_box">
                <div class="artikel_section_box_img">
                    <a href="{{ route('customer.articles.show', $article->slug) }}">
                        @if($article->image)
                            <img src="{{ Storage::url($article->image) }}" alt="{{ $article->title }}">
                        @else
                            <img src="{{ asset('images/default-article.jpg') }}" alt="{{ $article->title }}">
                        @endif
                    </a>
                </div>
                <div class="artikel_section_content">
                    <div class="artikel_section_meta">
                        <div class="artikel_section_meta_box">
                            <iconify-icon icon="mdi:user"></iconify-icon>
                            <span>{{ $article->author ?? 'Admin' }}</span>
                        </div>
                        <div class="artikel_section_meta_box">
                            <iconify-icon icon="lets-icons:date-fill"></iconify-icon>
                            <span>{{ $article->formatted_published_at }}</span>
                        </div>
                        {{-- 🔥 GANTI category dengan articleCategory->name --}}
                        @if($article->articleCategory)
                            <div class="artikel_section_meta_box">
                                <iconify-icon icon="material-symbols:category"></iconify-icon>
                                <span>{{ $article->articleCategory->name }}</span>
                            </div>
                        @endif
                    </div>
                    <h3>{{ $article->title }}</h3>
                    <p>{{ Str::limit(strip_tags($article->excerpt ?: $article->content), 120) }}</p>
                    <a href="{{ route('customer.articles.show', $article->slug) }}">
                        <button>Baca Selengkapnya</button>
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <p>Belum ada artikel yang tersedia.</p>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if ($articles->hasPages())
        <div class="pagination-wrapper">
            {{ $articles->links() }}
        </div>
    @endif
</div>

{{-- ============================================ --}}
{{-- JAVASCRIPT - CUSTOM SELECT (sama seperti produk) --}}
{{-- ============================================ --}}
<script>
// ============================================
// CUSTOM SELECT - TOGGLE DROPDOWN
// ============================================

function toggleDropdown(trigger) {
    var wrapper = trigger.closest('.custom-select-wrapper');
    if (!wrapper) return;
    
    var dropdown = wrapper.querySelector('.custom-select-dropdown');
    if (!dropdown) return;
    
    var isOpen = dropdown.classList.contains('open');

    var allDropdowns = document.querySelectorAll('.custom-select-dropdown.open');
    allDropdowns.forEach(function(d) {
        if (d !== dropdown) {
            d.classList.remove('open');
            var t = d.closest('.custom-select-wrapper')?.querySelector('.custom-select-trigger');
            if (t) t.classList.remove('open');
        }
    });

    if (isOpen) {
        dropdown.classList.remove('open');
        trigger.classList.remove('open');
    } else {
        dropdown.classList.add('open');
        trigger.classList.add('open');
    }
}

function closeAllDropdowns() {
    var allDropdowns = document.querySelectorAll('.custom-select-dropdown.open');
    allDropdowns.forEach(function(dropdown) {
        dropdown.classList.remove('open');
        var trigger = dropdown.closest('.custom-select-wrapper')?.querySelector('.custom-select-trigger');
        if (trigger) {
            trigger.classList.remove('open');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {

    // EVENT LISTENER UNTUK TRIGGER
    var triggers = document.querySelectorAll('.custom-select-trigger');
    triggers.forEach(function(trigger) {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleDropdown(this);
        });
    });

    // SELECT ITEM
    var items = document.querySelectorAll('.dropdown-item');
    items.forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.stopPropagation();
            
            var wrapper = this.closest('.custom-select-wrapper');
            if (!wrapper) return;
            
            var trigger = wrapper.querySelector('.custom-select-trigger');
            var dropdown = wrapper.querySelector('.custom-select-dropdown');
            var name = wrapper.dataset.name;
            var value = this.dataset.value;
            var text = this.querySelector('span')?.textContent || '';

            var triggerText = trigger?.querySelector('.trigger-text');
            if (triggerText) {
                triggerText.textContent = text;
            }

            if (dropdown) {
                var allItems = dropdown.querySelectorAll('.dropdown-item');
                allItems.forEach(function(d) {
                    d.classList.remove('active');
                });
            }
            this.classList.add('active');

            if (dropdown) dropdown.classList.remove('open');
            if (trigger) trigger.classList.remove('open');

            var form = document.getElementById('filter-form');
            if (!form) return;
            
            var oldInput = form.querySelector('input[name="' + name + '"]');
            if (oldInput) {
                oldInput.remove();
            }

            if (value !== '') {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            }

            form.submit();
        });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-select-wrapper')) {
            closeAllDropdowns();
        }
    });

    console.log('📝 Artikel page ready');
});
</script>

@endsection