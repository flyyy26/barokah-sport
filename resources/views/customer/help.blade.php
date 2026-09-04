@extends('layouts.customer')

@section('title', 'Bantuan & Dukungan - Barokah Sport')

@section('content')

<style>
    html {
        scroll-behavior: smooth;
    }

    /* ============================================
       HELP CONTAINER
       ============================================ */
    .help_container {
        width: 100%;
        margin: 0 auto;
        border-top: 0.1vw solid #076694;
    }

    .help_top_container {
        width: 100%;
        padding: 1.6vw 7.54vw;
        padding-bottom: 1.9vw;
        background: #f9fafb;
    }

    .help-header h1 {
        font-size: 2.3vw;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        font-family: heading, sans-serif;
        text-transform: uppercase;
    }

    .help-header p {
        font-size: 0.85vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }

    /* ============================================
       HELP TABS
       ============================================ */
    .help_tabs_container {
        width: 100%;
        padding: 2vw 7.4vw 1vw 7.4vw;
        background: #ffffff;
        border-bottom: 0.1vw solid #e2e8f0;
    }

    .help_tabs {
        display: flex;
        gap: 1.5vw;
        flex-wrap: wrap;
    }

    .help_tab_btn {
        padding: 0.7vw 1.8vw;
        border: none;
        border-bottom: 0.2vw solid transparent;
        background: transparent;
        font-size: 0.85vw;
        font-weight: 600;
        color: #94a3b8;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.03vw;
    }

    .help_tab_btn:hover {
        color: #0f172a;
    }

    .help_tab_btn.active {
        color: #076694;
        border-bottom-color: #076694;
    }

    /* ============================================
       HELP CONTENT
       ============================================ */
    .help_content_container {
        width: 100%;
        padding: 3vw 7.4vw 4vw 7.4vw;
        background: #fff;
        min-height: 30vw;
    }

    .help_tab_content {
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .help_tab_content.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ============================================
       TAB 1 - KONTAK KAMI
       ============================================ */
    .help_contact_layout {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-gap: 2vw;
        align-items: center;
    }

    .help_contact_maps {
        width: 100%;
        height: 19vw;
        position: relative;
        border-radius: 0.7vw;
        overflow: hidden;
    }

    .help_contact_maps iframe {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }

    .help_contact_info {
        background: white;
        padding: 1.5vw 2vw;
        border-radius: 0.7vw;
        border: 0.1vw solid #e2e8f0;
        box-shadow: 0 0.1vw 0.5vw rgba(0, 0, 0, 0.05);
    }

    .help_contact_info h3 {
        font-size: 1.2vw;
        color: #076694;
        margin-bottom: 0.5vw;
    }

    .help_contact_info p {
        font-size: 0.85vw;
        color: #94a3b8;
        margin-bottom: 1.5vw;
    }

    .help_contact_info ul {
        list-style: none;
        padding: 0;
    }

    .help_contact_info ul li {
        margin-bottom: 0.8vw;
        display: flex;
        align-items: center;
        gap: 0.8vw;
        font-size: 0.87vw;
        color: #0f172a;
    }

    .help_contact_info ul li iconify-icon {
        font-size: 1.3vw;
        color: #076694;
        flex-shrink: 0;
    }

    .help_contact_social {
        display: flex;
        align-items: center;
        gap: 1vw;
        padding-top: 1vw;
        border-top: 0.1vw solid #e2e8f0;
        margin-top: 0.5vw;
    }

    .help_contact_social_box {
        width: 2.2vw;
        height: 2.2vw;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 100vw;
        border: 0.1vw solid #e2e8f0;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .help_contact_social_box:hover {
        background: #076694;
        border-color: #076694;
    }

    .help_contact_social_box:hover iconify-icon {
        color: white;
    }

    .help_contact_social_box iconify-icon {
        font-size: 1.2vw;
        color: #0f172a;
        transition: color 0.3s ease;
    }

    /* ============================================
       TAB 2 - CARA PESAN
       ============================================ */
    .help_cara_pesan {
        max-width: 80%;
        margin: 0 auto;
    }

    .help_cara_pesan h3 {
        font-size: 1.4vw;
        color: #0f172a;
        text-align: center;
        margin-bottom: 0.3vw;
    }

    .help_cara_pesan .subtitle {
        text-align: center;
        font-size: 0.85vw;
        color: #94a3b8;
        margin-bottom: 2vw;
    }

    .help_steps {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5vw;
    }

    .help_step {
        background: white;
        padding: 2vw 1.5vw;
        border-radius: 0.7vw;
        border: 0.1vw solid #e2e8f0;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 0.1vw 0.5vw rgba(0, 0, 0, 0.05);
    }

    .help_step:hover {
        transform: translateY(-0.3vw);
        box-shadow: 0 0.5vw 1.5vw rgba(0, 0, 0, 0.1);
    }

    .help_step .step_number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3vw;
        height: 3vw;
        border-radius: 50%;
        background: #076694;
        color: white;
        font-size: 1.2vw;
        font-weight: 700;
        margin-bottom: 0.8vw;
    }

    .help_step h4 {
        font-size: 0.95vw;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 0.3vw;
    }

    .help_step p {
        font-size: 0.8vw;
        color: #64748b;
        line-height: 1.6;
    }

    /* ============================================
       TAB 3 - CHAT WHATSAPP
       ============================================ */
    .help_whatsapp {
        max-width: 80%;
        margin: 0 auto;
        text-align: center;
    }

    .help_whatsapp .wa_icon {
        font-size: 5vw;
        color: #25D366;
        margin-bottom: .7vw;
        display: block;
    }

    .help_whatsapp h3 {
        font-size: 1.6vw;
        color: #0f172a;
        margin-bottom: 0.3vw;
        font-family: heading, sans-serif;
        text-transform:uppercase;
    }

    .help_whatsapp p {
        font-size: 0.85vw;
        color: #94a3b8;
        margin-bottom: 1.5vw;
    }

    .help_whatsapp .wa_info {
        background: white;
        padding: 2vw;
        border-radius: 0.7vw;
        border: 0.1vw solid #e2e8f0;
        box-shadow: 0 0.1vw 0.5vw rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5vw;
        text-align: left;
    }

    .help_whatsapp .wa_info .info_item {
        display: flex;
        align-items: center;
        gap: 0.8vw;
        padding: 0.5vw 0;
        border-bottom: 0.05vw solid #f1f5f9;
        font-size: 0.85vw;
        color: #475569;
    }

    .help_whatsapp .wa_info .info_item:last-child {
        border-bottom: none;
    }

    .help_whatsapp .wa_info .info_item iconify-icon {
        font-size: 1.2vw;
        color: #076694;
    }

    .help_whatsapp .wa_info .info_item strong {
        color: #0f172a;
    }

    .btn_whatsapp {
        display: inline-flex;
        align-items: center;
        gap: 0.6vw;
        padding: 0.9vw 2.5vw;
        background: #25D366;
        color: white;
        border: none;
        border-radius: 0.5vw;
        font-size: 0.95vw;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn_whatsapp:hover {
        background: #1da851;
        transform: scale(1.02);
    }

    .btn_whatsapp iconify-icon {
        font-size: 1.5vw;
    }

    .help_whatsapp .wa_note {
        font-size: 0.75vw;
        color: #94a3b8;
        margin-top: 1vw;
    }

    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 768px) {
        .help_top_container {
            padding: 2vw 5vw;
        }

        .help-header h1 {
            font-size: 5vw;
        }

        .help-header p {
            font-size: 2.5vw;
        }

        .help_tabs_container {
            padding: 2vw 5vw;
        }

        .help_tab_btn {
            font-size: 2.2vw;
            padding: 1.5vw 3vw;
        }

        .help_content_container {
            padding: 3vw 5vw;
        }

        .help_contact_layout {
            grid-template-columns: 1fr;
        }

        .help_contact_maps {
            height: 50vw;
        }

        .help_contact_info {
            padding: 4vw;
        }

        .help_contact_info h3 {
            font-size: 3.5vw;
        }

        .help_contact_info p {
            font-size: 2.5vw;
        }

        .help_contact_info ul li {
            font-size: 2.5vw;
        }

        .help_contact_info ul li iconify-icon {
            font-size: 3.5vw;
        }

        .help_contact_social_box {
            width: 6vw;
            height: 6vw;
        }

        .help_contact_social_box iconify-icon {
            font-size: 3vw;
        }

        .help_cara_pesan {
            max-width: 100%;
        }

        .help_cara_pesan h3 {
            font-size: 4vw;
        }

        .help_cara_pesan .subtitle {
            font-size: 2.5vw;
        }

        .help_steps {
            grid-template-columns: 1fr;
            gap: 2vw;
        }

        .help_step {
            padding: 4vw 3vw;
        }

        .help_step .step_number {
            width: 8vw;
            height: 8vw;
            font-size: 3.5vw;
        }

        .help_step h4 {
            font-size: 3vw;
        }

        .help_step p {
            font-size: 2.5vw;
        }

        .help_whatsapp {
            max-width: 100%;
        }

        .help_whatsapp .wa_icon {
            font-size: 12vw;
        }

        .help_whatsapp h3 {
            font-size: 4vw;
        }

        .help_whatsapp p {
            font-size: 2.5vw;
        }

        .help_whatsapp .wa_info {
            padding: 4vw;
        }

        .help_whatsapp .wa_info .info_item {
            font-size: 2.5vw;
            padding: 1.5vw 0;
        }

        .help_whatsapp .wa_info .info_item iconify-icon {
            font-size: 3vw;
        }

        .btn_whatsapp {
            padding: 2.5vw 6vw;
            font-size: 2.8vw;
        }

        .btn_whatsapp iconify-icon {
            font-size: 4vw;
        }

        .help_whatsapp .wa_note {
            font-size: 2vw;
        }
    }

    @media (max-width: 480px) {
        .help_tab_btn {
            font-size: 2.8vw;
            padding: 2vw 4vw;
        }

        .help_contact_info ul li {
            font-size: 3vw;
        }

        .help_step h4 {
            font-size: 3.5vw;
        }

        .help_step p {
            font-size: 3vw;
        }

        .help_whatsapp h3 {
            font-size: 5vw;
        }

        .help_whatsapp p {
            font-size: 3vw;
        }

        .btn_whatsapp {
            font-size: 3.5vw;
            padding: 3vw 8vw;
        }
    }
</style>

<div class="help_container">
    {{-- HEADER --}}
    <div class="help_top_container">
        <div class="help-header">
            <h1>Bantuan & Dukungan</h1>
            <p>Kami siap membantu Anda. Pilih topik yang ingin Anda ketahui.</p>
        </div>
    </div>

    {{-- TABS --}}
    <div class="help_tabs_container">
        <div class="help_tabs">
            <button class="help_tab_btn active" data-tab="tab-contact">
                Kontak Kami
            </button>
            <button class="help_tab_btn" data-tab="tab-cara-pesan">
                Cara Pesan
            </button>
            <button class="help_tab_btn" data-tab="tab-whatsapp">
                Chat WhatsApp
            </button>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="help_content_container">
        {{-- TAB 1: KONTAK KAMI --}}
        <div class="help_tab_content active" id="tab-contact">
            <div class="help_contact_layout">
                <div class="help_contact_maps">
                    @if($setting?->google_maps)
                        {!! $setting->google_maps !!}
                    @else
                        <div style="width:100%;height:100%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:0.9vw;">
                            Map belum tersedia
                        </div>
                    @endif
                </div>
                <div class="help_contact_info">
                    <h3>Hubungi Kami</h3>
                    <p>Kami siap membantu Anda melalui berbagai saluran berikut:</p>
                    <ul>
                        <li>
                            <iconify-icon icon="majesticons:map-marker"></iconify-icon>
                            {{ $setting?->address ?? 'Alamat belum diisi' }}
                        </li>
                        <li>
                            <iconify-icon icon="ic:baseline-whatsapp"></iconify-icon>
                            <a href="https://api.whatsapp.com/send?phone={{ $setting?->whatsapp ?? '' }}" 
                               target="_blank" 
                               style="color:#076694;text-decoration:none;">
                                {{ $setting?->whatsapp ?? 'WhatsApp belum diisi' }}
                            </a>
                        </li>
                        <li>
                            <iconify-icon icon="ic:outline-email"></iconify-icon>
                            <a href="mailto:{{ $setting?->email ?? '' }}" 
                               style="color:#076694;text-decoration:none;">
                                {{ $setting?->email ?? 'Email belum diisi' }}
                            </a>
                        </li>
                    </ul>
                    <div class="help_contact_social">
                        <a href="{{ $setting?->facebook ?? '#' }}" target="_blank" class="help_contact_social_box">
                            <iconify-icon icon="ic:baseline-facebook"></iconify-icon>
                        </a>
                        <a href="{{ $setting?->instagram ?? '#' }}" target="_blank" class="help_contact_social_box">
                            <iconify-icon icon="mdi:instagram"></iconify-icon>
                        </a>
                        <a href="{{ $setting?->tiktok ?? '#' }}" target="_blank" class="help_contact_social_box">
                            <iconify-icon icon="ic:baseline-tiktok"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: CARA PESAN --}}
        <div class="help_tab_content" id="tab-cara-pesan">
            <div class="help_cara_pesan">
                <h3>Cara Memesan Produk</h3>
                <p class="subtitle">Ikuti langkah-langkah mudah berikut untuk memesan produk di Barokah Sport</p>

                <div class="help_steps">
                    <div class="help_step">
                        <div class="step_number">1</div>
                        <h4>Pilih Produk</h4>
                        <p>Cari dan pilih produk yang Anda inginkan dari katalog kami. Klik produk untuk melihat detailnya.</p>
                    </div>
                    <div class="help_step">
                        <div class="step_number">2</div>
                        <h4>Pilih Varian</h4>
                        <p>Pilih ukuran, warna, dan varian yang sesuai dengan kebutuhan Anda. Pastikan stok tersedia.</p>
                    </div>
                    <div class="help_step">
                        <div class="step_number">3</div>
                        <h4>Tambahkan ke Keranjang</h4>
                        <p>Klik "Tambah ke Keranjang" atau "Beli Sekarang" untuk melanjutkan ke proses checkout.</p>
                    </div>
                    <div class="help_step">
                        <div class="step_number">4</div>
                        <h4>Checkout</h4>
                        <p>Isi alamat pengiriman, pilih metode pembayaran, dan konfirmasi pesanan Anda.</p>
                    </div>
                    <div class="help_step">
                        <div class="step_number">5</div>
                        <h4>Pembayaran</h4>
                        <p>Lakukan pembayaran sesuai instruksi yang diberikan. Kami akan memproses pesanan Anda.</p>
                    </div>
                    <div class="help_step">
                        <div class="step_number">6</div>
                        <h4>Pesanan Dikirim</h4>
                        <p>Pesanan akan dikirim dan Anda akan mendapatkan nomor resi untuk melacak paket Anda.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 3: CHAT WHATSAPP --}}
        <div class="help_tab_content" id="tab-whatsapp">
            <div class="help_whatsapp">
                <span class="wa_icon">
                    <iconify-icon icon="mdi:whatsapp"></iconify-icon>
                </span>
                <h3>Chat WhatsApp</h3>
                <p>Hubungi kami langsung melalui WhatsApp untuk bantuan cepat dan responsif.</p>

                <div class="wa_info">
                    <div class="info_item">
                        <iconify-icon icon="mdi:whatsapp"></iconify-icon>
                        <span><strong>Nomor WhatsApp:</strong> {{ $setting?->whatsapp ?? 'Belum diatur' }}</span>
                    </div>
                    <div class="info_item">
                        <iconify-icon icon="mdi:clock-outline"></iconify-icon>
                        <span><strong>Jam Operasional:</strong> Senin - Sabtu (08.00 - 16.00 WIB)</span>
                    </div>
                    <div class="info_item">
                        <iconify-icon icon="mdi:message-text-outline"></iconify-icon>
                        <span><strong>Layanan:</strong> Konsultasi produk, bantuan pemesanan, info stok, dan pengaduan</span>
                    </div>
                    <div class="info_item">
                        <iconify-icon icon="mdi:response"></iconify-icon>
                        <span><strong>Waktu Respon:</strong> 1-12 jam (dalam jam operasional)</span>
                    </div>
                </div>

                <a href="https://api.whatsapp.com/send?phone={{ $setting?->whatsapp ?? '' }}" 
                   target="_blank" 
                   class="btn_whatsapp">
                    <iconify-icon icon="mdi:whatsapp"></iconify-icon>
                    Chat Sekarang
                </a>

                <p class="wa_note">
                    ⚡ Siapkan nomor pesanan untuk membantu kami melayani Anda lebih cepat.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ============================================
        // TAB SWITCHING
        // ============================================
        const tabBtns = document.querySelectorAll('.help_tab_btn');
        const tabContents = document.querySelectorAll('.help_tab_content');

        tabBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const targetTab = this.dataset.tab;

                // Update active button
                tabBtns.forEach(function(b) {
                    b.classList.remove('active');
                });
                this.classList.add('active');

                // Update active content
                tabContents.forEach(function(content) {
                    content.classList.remove('active');
                    if (content.id === targetTab) {
                        content.classList.add('active');
                    }
                });

                // Scroll ke content
                const contentContainer = document.querySelector('.help_content_container');
                if (contentContainer) {
                    setTimeout(function() {
                        contentContainer.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }, 100);
                }
            });
        });

        // ============================================
        // CEK URL HASH UNTUK TAB
        // ============================================
        if (window.location.hash) {
            const hash = window.location.hash.replace('#', '');
            const targetBtn = document.querySelector(`.help_tab_btn[data-tab="${hash}"]`);
            if (targetBtn) {
                targetBtn.click();
            }
        }
    });
</script>

@endsection