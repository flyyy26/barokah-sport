@extends('layouts.customer')

@section('title', 'Akun Saya - Barokah Sport')

@section('content')

<style>
    .account_container {
        width: 100%;
        max-width: 50vw;
        margin: 3vw auto;
        padding: 2.5vw;
        background: #ffffff;
        border-radius: 1vw;
        border: 0.1vw solid #e2e8f0;
        box-shadow: 0 0.5vw 2vw rgba(0, 0, 0, 0.05);
    }

    .account_container h1 {
        font-size: 2vw;
        font-weight: 700;
        color: #0f172a;
        font-family: heading, sans-serif;
        text-transform: uppercase;
        margin-bottom: 0.3vw;
    }

    .account_container .welcome-text {
        font-size: 0.9vw;
        color: #94a3b8;
        margin-bottom: 2vw;
    }

    .account_info {
        display: flex;
        flex-direction: column;
        gap: 0.5vw;
        margin-bottom: 2vw;
    }

    .account_info .info_item {
        display: flex;
        padding: 0.6vw 0;
        border-bottom: 0.05vw solid #f1f5f9;
    }

    .account_info .info_item .label {
        width: 8vw;
        font-size: 0.8vw;
        font-weight: 600;
        color: #475569;
    }

    .account_info .info_item .value {
        font-size: 0.8vw;
        color: #0f172a;
        font-weight: 500;
    }

    .account_menu {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.8vw;
        margin: 1.5vw 0 2vw;
    }

    .account_menu .menu_item {
        display: flex;
        align-items: center;
        gap: 0.8vw;
        padding: 0.8vw 1.2vw;
        background: #f8fafc;
        border-radius: 0.5vw;
        border: 0.05vw solid #e2e8f0;
        text-decoration: none;
        transition: all 0.3s ease;
        color: #0f172a;
    }

    .account_menu .menu_item:hover {
        background: #f1f5f9;
        border-color: #076694;
        transform: translateX(0.2vw);
    }

    .account_menu .menu_item iconify-icon {
        font-size: 1.2vw;
        color: #076694;
    }

    .account_menu .menu_item .menu_text {
        font-size: 0.8vw;
        font-weight: 500;
    }

    .account_menu .menu_item .menu_badge {
        margin-left: auto;
        padding: 0.1vw 0.5vw;
        background: #076694;
        color: #ffffff;
        border-radius: 100vw;
        font-size: 0.6vw;
        font-weight: 600;
    }

    .btn_logout {
        display: inline-flex;
        align-items: center;
        gap: 0.5vw;
        padding: 0.6vw 2vw;
        background: #ef4444;
        color: #ffffff;
        border: none;
        border-radius: 0.5vw;
        font-size: 0.8vw;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn_logout:hover {
        background: #dc2626;
        transform: scale(1.02);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .account_container {
            max-width: 85vw;
            padding: 4vw;
            border-radius: 2vw;
        }

        .account_container h1 {
            font-size: 3.5vw;
        }

        .account_container .welcome-text {
            font-size: 1.5vw;
        }

        .account_info .info_item .label {
            width: 20vw;
            font-size: 1.3vw;
        }

        .account_info .info_item .value {
            font-size: 1.3vw;
        }

        .account_menu {
            grid-template-columns: 1fr;
            gap: 1.2vw;
        }

        .account_menu .menu_item {
            padding: 1.2vw 2vw;
        }

        .account_menu .menu_item iconify-icon {
            font-size: 2vw;
        }

        .account_menu .menu_item .menu_text {
            font-size: 1.3vw;
        }

        .btn_logout {
            font-size: 1.3vw;
            padding: 1vw 3vw;
        }
    }

    @media (max-width: 480px) {
        .account_container {
            max-width: 95vw;
            padding: 5vw;
            border-radius: 3vw;
        }

        .account_container h1 {
            font-size: 5vw;
        }

        .account_container .welcome-text {
            font-size: 2.2vw;
        }

        .account_info .info_item .label {
            width: 25vw;
            font-size: 1.8vw;
        }

        .account_info .info_item .value {
            font-size: 1.8vw;
        }

        .account_menu .menu_item {
            padding: 2vw 3vw;
        }

        .account_menu .menu_item iconify-icon {
            font-size: 3vw;
        }

        .account_menu .menu_item .menu_text {
            font-size: 1.8vw;
        }

        .btn_logout {
            font-size: 1.8vw;
            padding: 1.5vw 4vw;
        }
    }
</style>

<div class="account_container">
    <h1>👤 Akun Saya</h1>
    <p class="welcome-text">Selamat datang, <strong>{{ Auth::guard('customer')->user()->name ?? 'Customer' }}</strong>!</p>

    <div class="account_info">
        <div class="info_item">
            <span class="label">Nama</span>
            <span class="value">{{ Auth::guard('customer')->user()->name ?? '-' }}</span>
        </div>
        <div class="info_item">
            <span class="label">Email</span>
            <span class="value">{{ Auth::guard('customer')->user()->email ?? '-' }}</span>
        </div>
        <div class="info_item">
            <span class="label">Role</span>
            <span class="value">{{ ucfirst(Auth::guard('customer')->user()->role ?? 'Customer') }}</span>
        </div>
    </div>

    <div class="account_menu">
        <a href="{{ route('customer.orders') }}" class="menu_item">
            <iconify-icon icon="mdi:package-variant"></iconify-icon>
            <span class="menu_text">Pesanan Saya</span>
        </a>
        <a href="{{ route('customer.addresses.create') }}" class="menu_item">
            <iconify-icon icon="mdi:map-marker"></iconify-icon>
            <span class="menu_text">Alamat Pengiriman</span>
        </a>
        <a href="{{ route('customer.wishlist.index') }}" class="menu_item">
            <iconify-icon icon="mdi:heart"></iconify-icon>
            <span class="menu_text">Wishlist</span>
        </a>
    </div>

    <form action="{{ route('customer.logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn_logout">
            <iconify-icon icon="mdi:logout"></iconify-icon>
            Logout
        </button>
    </form>
</div>

@endsection