<!-- Search Popup Overlay -->
<div id="search-popup-overlay" class="search-popup-overlay" style="display: none;">
    <div class="search-popup-container">
        <button type="button" class="search-popup-close" onclick="closeSearchPopup()">
            <iconify-icon icon="mdi:close"></iconify-icon>
        </button>

        <div class="search-popup-content">
            {{-- Logo --}}
            <div class="search-popup-logo">
                <a href="{{ route('customer.home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Barokah Sport" style="height: 40px;">
                </a>
            </div>

            {{-- Search Input --}}
            <div class="search-popup-input-wrapper">
                <form id="search-popup-form" action="{{ route('customer.products.index') }}" method="GET">
                    <iconify-icon icon="mdi:search" class="search-popup-icon"></iconify-icon>
                    <input 
                        type="text" 
                        name="search" 
                        id="search-popup-input" 
                        class="search-popup-input" 
                        placeholder="Cari produk..." 
                        autocomplete="off"
                        autofocus
                        value="{{ request('search') }}"
                    >
                    <button type="submit" class="search-popup-submit">Cari</button>
                </form>
            </div>

            {{-- Trending Search --}}
            <div class="search-popup-trending">
                <div class="search-popup-trending-header">
                    <span>Trending Search</span>
                </div>
                <div class="search-popup-trending-list">
                    @php
                        $trendingSearches = [
                            'Sepatu Futsal',
                            'Jersey Bola',
                            'Kaos Olahraga',
                            'Sepatu Lari',
                            'Raket Badminton',
                            'Bola Futsal',
                            'Celana Training',
                            'Tas Gym'
                        ];
                    @endphp
                    @foreach($trendingSearches as $trend)
                        <a href="{{ route('customer.products.index', ['search' => $trend]) }}" 
                           class="search-popup-trending-item"
                           onclick="closeSearchPopup()">
                            <iconify-icon icon="mdi:fire" class="trending-icon"></iconify-icon>
                            {{ $trend }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Navigation Links --}}
            <div class="search-popup-nav">
                <a href="{{ route('customer.about') }}" class="search-popup-nav-link" onclick="closeSearchPopup()">
                    <iconify-icon icon="mdi:information-outline"></iconify-icon>
                    Tentang Kami
                </a>
                <a href="{{ route('customer.contact') }}#faq-section" class="search-popup-nav-link" onclick="closeSearchPopup()">
                    <iconify-icon icon="mdi:help-circle-outline"></iconify-icon>
                    FAQ
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* ============================================
       SEARCH POPUP OVERLAY
       ============================================ */
    .search-popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        z-index: 10000;
        display: none;
        justify-content: center;
        align-items: center;
        animation: searchFadeIn 0.3s ease;
    }

    .search-popup-overlay.active {
        display: flex;
    }

    @keyframes searchFadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes searchFadeOut {
        from {
            opacity: 1;
            transform: scale(1);
        }
        to {
            opacity: 0;
            transform: scale(0.95);
        }
    }

    .search-popup-overlay.fade-out {
        animation: searchFadeOut 0.3s ease forwards;
    }

    /* ============================================
       SEARCH POPUP CONTAINER
       ============================================ */
    .search-popup-container {
        background: #ffffff;
        border-radius: 1.2vw;
        max-width: 50vw;
        width: 100%;
        max-height: 80vh;
        overflow-y: auto;
        padding: 2.5vw 3vw;
        position: relative;
        box-shadow: 0 1vw 4vw rgba(0, 0, 0, 0.2);
    }

    .search-popup-container::-webkit-scrollbar {
        width: 0.3vw;
    }

    .search-popup-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 0.3vw;
    }

    .search-popup-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 0.3vw;
    }

    /* ============================================
       CLOSE BUTTON
       ============================================ */
    .search-popup-close {
        position: absolute;
        top: 1vw;
        right: 1.5vw;
        background: #f1f5f9;
        border: none;
        border-radius: 50%;
        width: 2.5vw;
        height: 2.5vw;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1.5vw;
        color: #475569;
    }

    .search-popup-close:hover {
        background: #e2e8f0;
        transform: rotate(90deg);
    }

    /* ============================================
       LOGO
       ============================================ */
    .search-popup-logo {
        text-align: center;
        margin-bottom: 1.5vw;
    }

    .search-popup-logo img {
        height: 3vw;
    }

    /* ============================================
       SEARCH INPUT
       ============================================ */
    .search-popup-input-wrapper {
        margin-bottom: 2vw;
    }

    .search-popup-input-wrapper form {
        display: flex;
        align-items: center;
        background: #f1f5f9;
        border-radius: 0.8vw;
        padding: 0 0.5vw;
        border: 0.15vw solid transparent;
        transition: all 0.3s ease;
    }

    .search-popup-input-wrapper form:focus-within {
        border-color: #076694;
        background: #ffffff;
        box-shadow: 0 0 0 0.3vw rgba(7, 102, 148, 0.1);
    }

    .search-popup-icon {
        font-size: 1.5vw;
        color: #94a3b8;
        padding: 0 0.8vw;
        flex-shrink: 0;
    }

    .search-popup-input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 0.8vw 0.5vw;
        font-size: 1vw;
        color: #0f172a;
        outline: none;
        font-family: inherit;
        min-width: 0;
    }

    .search-popup-input::placeholder {
        color: #94a3b8;
    }

    .search-popup-submit {
        background: #076694;
        color: #ffffff;
        border: none;
        padding: 0.6vw 1.8vw;
        border-radius: 0.6vw;
        font-size: 0.8vw;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.05vw;
        flex-shrink: 0;
    }

    .search-popup-submit:hover {
        background: #055a7a;
    }

    /* ============================================
       TRENDING SEARCH
       ============================================ */
    .search-popup-trending {
        margin-bottom: 2vw;
    }

    .search-popup-trending-header {
        font-size: 0.85vw;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 0.8vw;
        padding-bottom: 0.5vw;
        border-bottom: 0.1vw solid #e2e8f0;
    }

    .search-popup-trending-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6vw;
    }

    .search-popup-trending-item {
        display: flex;
        align-items: center;
        gap: 0.3vw;
        padding: 0.4vw 1vw;
        background: #f1f5f9;
        border-radius: 100vw;
        font-size: 0.75vw;
        color: #475569;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
    }

    .search-popup-trending-item:hover {
        background: #076694;
        color: #ffffff;
    }

    .search-popup-trending-item .trending-icon {
        font-size: 0.8vw;
        color: #ef4444;
    }

    .search-popup-trending-item:hover .trending-icon {
        color: #ffffff;
    }

    /* ============================================
       NAVIGATION LINKS
       ============================================ */
    .search-popup-nav {
        display: flex;
        justify-content: center;
        gap: 2vw;
        padding-top: 1.5vw;
        border-top: 0.1vw solid #e2e8f0;
    }

    .search-popup-nav-link {
        display: flex;
        align-items: center;
        gap: 0.5vw;
        font-size: 0.8vw;
        color: #475569;
        text-decoration: none;
        transition: all 0.3s ease;
        padding: 0.3vw 0.8vw;
        border-radius: 0.4vw;
    }

    .search-popup-nav-link:hover {
        color: #076694;
        background: #f0f9ff;
    }

    .search-popup-nav-link iconify-icon {
        font-size: 1.2vw;
    }

    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 768px) {
        .search-popup-container {
            max-width: 90vw;
            padding: 5vw 6vw;
            border-radius: 3vw;
            max-height: 85vh;
        }

        .search-popup-close {
            width: 6vw;
            height: 6vw;
            font-size: 3.5vw;
            top: 2.5vw;
            right: 3vw;
        }

        .search-popup-logo img {
            height: 6vw;
        }

        .search-popup-input-wrapper form {
            border-radius: 2vw;
            padding: 0 1.5vw;
        }

        .search-popup-icon {
            font-size: 3.5vw;
            padding: 0 1.5vw;
        }

        .search-popup-input {
            font-size: 2.5vw;
            padding: 2vw 1vw;
        }

        .search-popup-submit {
            padding: 1.5vw 4vw;
            font-size: 2vw;
            border-radius: 1.5vw;
        }

        .search-popup-trending-header {
            font-size: 2.5vw;
        }

        .search-popup-trending-item {
            padding: 1vw 2.5vw;
            font-size: 2vw;
            gap: 0.8vw;
        }

        .search-popup-trending-item .trending-icon {
            font-size: 2vw;
        }

        .search-popup-nav {
            gap: 4vw;
            padding-top: 3vw;
        }

        .search-popup-nav-link {
            font-size: 2vw;
            gap: 1vw;
        }

        .search-popup-nav-link iconify-icon {
            font-size: 3vw;
        }
    }

    @media (max-width: 480px) {
        .search-popup-container {
            max-width: 95vw;
            padding: 6vw 5vw;
            border-radius: 4vw;
        }

        .search-popup-close {
            width: 8vw;
            height: 8vw;
            font-size: 4.5vw;
            top: 2vw;
            right: 2.5vw;
        }

        .search-popup-logo img {
            height: 8vw;
        }

        .search-popup-input {
            font-size: 3vw;
            padding: 2.5vw 1.5vw;
        }

        .search-popup-submit {
            padding: 2vw 5vw;
            font-size: 2.5vw;
        }

        .search-popup-trending-header {
            font-size: 3vw;
        }

        .search-popup-trending-item {
            padding: 1.5vw 3vw;
            font-size: 2.5vw;
        }

        .search-popup-nav-link {
            font-size: 2.5vw;
        }

        .search-popup-nav-link iconify-icon {
            font-size: 4vw;
        }
    }
</style>