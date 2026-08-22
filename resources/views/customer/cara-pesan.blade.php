@extends('layouts.customer')

@section('title', 'Cara Pesan - Barokah Sport')

@section('content')

<style>
    html {
        scroll-behavior: smooth;
    }

    /* ============================================
       CARA PESAN CONTAINER
       ============================================ */
    .cara-pesan-container {
        width: 100%;
        margin: 0 auto;
        border-top: 0.1vw solid #076694;
    }

    .cara-pesan-header {
        width: 100%;
        padding: 1.6vw 7.54vw;
        padding-bottom: 1.9vw;
        background: #f9fafb;
    }

    .cara-pesan-header h1 {
        font-size: 2.3vw;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        font-family: heading, sans-serif;
        text-transform: uppercase;
    }

    .cara-pesan-header p {
        font-size: 0.85vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }

    /* ============================================
       CONTENT
       ============================================ */
    .cara-pesan-content {
        width: 100%;
        padding: 3vw 7.4vw 4vw 7.4vw;
        background: #fff;
    }

    .cara-pesan-steps {
        max-width: 80%;
        margin: 0 auto;
    }

    .cara-pesan-steps .section-title {
        font-size: 1.6vw;
        font-weight: 700;
        color: #0f172a;
        text-align: center;
        margin-bottom: 0.3vw;
    }

    .cara-pesan-steps .section-subtitle {
        text-align: center;
        font-size: 0.85vw;
        color: #94a3b8;
        margin-bottom: 2.5vw;
    }

    /* ============================================
       STEP CARDS
       ============================================ */
    .steps-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5vw;
        margin-bottom: 3vw;
    }

    .step-card {
        background: #ffffff;
        padding: 2vw 1.5vw;
        border-radius: 0.7vw;
        border: 0.1vw solid #e2e8f0;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 0.1vw 0.5vw rgba(0, 0, 0, 0.05);
        position: relative;
    }

    .step-card:hover {
        transform: translateY(-0.3vw);
        box-shadow: 0 0.5vw 1.5vw rgba(0, 0, 0, 0.1);
    }

    .step-card .step-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3.5vw;
        height: 3.5vw;
        border-radius: 50%;
        background: #076694;
        color: #ffffff;
        font-size: 1.4vw;
        font-weight: 700;
        margin-bottom: 0.8vw;
    }

    .step-card .step-icon {
        font-size: 2.5vw;
        margin-bottom: 0.5vw;
        display: block;
    }

    .step-card h3 {
        font-size: 1vw;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.3vw;
    }

    .step-card p {
        font-size: 0.8vw;
        color: #64748b;
        line-height: 1.6;
    }

    /* ============================================
       VIDEO / ILUSTRASI
       ============================================ */
    .cara-pesan-illustration {
        background: #ffffff;
        border-radius: 0.7vw;
        border: 0.1vw solid #e2e8f0;
        padding: 2vw;
        margin-top: 1vw;
        box-shadow: 0 0.1vw 0.5vw rgba(0, 0, 0, 0.05);
    }

    .cara-pesan-illustration h3 {
        font-size: 1.2vw;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.5vw;
        text-align: center;
    }

    .cara-pesan-illustration p {
        font-size: 0.85vw;
        color: #94a3b8;
        text-align: center;
        margin-bottom: 1.5vw;
    }

    .illustration-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5vw;
    }

    .illustration-item {
        text-align: center;
        padding: 1.5vw;
        background: #f8fafc;
        border-radius: 0.5vw;
        border: 0.05vw solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .illustration-item:hover {
        background: #f1f5f9;
    }

    .illustration-item .ill-icon {
        font-size: 3vw;
        margin-bottom: 0.5vw;
        display: block;
    }

    .illustration-item h4 {
        font-size: 0.85vw;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 0.2vw;
    }

    .illustration-item p {
        font-size: 0.7vw;
        color: #94a3b8;
        margin: 0;
    }

    /* ============================================
       FAQ SECTION (CARA PESAN)
       ============================================ */
    .cara-pesan-faq {
        margin-top: 2.5vw;
        padding-top: 2vw;
        border-top: 0.1vw solid #e2e8f0;
    }

    .cara-pesan-faq h3 {
        font-size: 1.2vw;
        font-weight: 700;
        color: #0f172a;
        text-align: center;
        margin-bottom: 0.3vw;
    }

    .cara-pesan-faq .faq-subtitle {
        text-align: center;
        font-size: 0.85vw;
        color: #94a3b8;
        margin-bottom: 2vw;
    }

    .cara-pesan-faq .faq-list {
        max-width: 80%;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 0.8vw;
    }

    .cara-pesan-faq .faq-item {
        background: #ffffff;
        border-radius: 0.7vw;
        overflow: hidden;
        border: 0.1vw solid #e2e8f0;
        box-shadow: 0 0.1vw 0.5vw rgba(0, 0, 0, 0.05);
    }

    .cara-pesan-faq .faq-question {
        width: 100%;
        padding: 0.8vw 1.5vw;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
        border: none;
        cursor: pointer;
        font-size: 0.85vw;
        font-weight: 600;
        color: #0f172a;
        text-align: left;
        transition: all 0.3s ease;
        font-family: heading, sans-serif;
    }

    .cara-pesan-faq .faq-question:hover {
        background: #f8fafc;
    }

    .cara-pesan-faq .faq-question .faq-icon {
        font-size: 1.2vw;
        color: #076694;
        transition: transform 0.3s ease;
        flex-shrink: 0;
        margin-left: 1vw;
    }

    .cara-pesan-faq .faq-question .faq-icon.open {
        transform: rotate(180deg);
    }

    .cara-pesan-faq .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease, padding 0.3s ease;
        background: #f8fafc;
    }

    .cara-pesan-faq .faq-answer.active {
        max-height: 500px;
    }

    .cara-pesan-faq .faq-answer-content {
        padding: 1vw 1.5vw 1.2vw 1.5vw;
        font-size: 0.8vw;
        color: #475569;
        line-height: 1.6;
        border-top: 0.1vw solid #e2e8f0;
    }

    .cara-pesan-faq .faq-answer-content ul,
    .cara-pesan-faq .faq-answer-content ol {
        margin-left: 1.2vw;
        margin-bottom: 0.5vw;
    }

    .cara-pesan-faq .faq-empty {
        text-align: center;
        padding: 2vw;
        color: #94a3b8;
        font-size: 0.9vw;
    }

    /* ============================================
       CONTACT BUTTON
       ============================================ */
    .cara-pesan-contact {
        text-align: center;
        margin-top: 2.5vw;
        padding-top: 2vw;
        border-top: 0.1vw solid #e2e8f0;
    }

    .cara-pesan-contact p {
        font-size: 0.85vw;
        color: #475569;
        margin-bottom: 1vw;
    }

    .cara-pesan-contact .btn-contact {
        display: inline-flex;
        align-items: center;
        gap: 0.6vw;
        padding: 0.7vw 2.5vw;
        background: #076694;
        color: #ffffff;
        border: none;
        border-radius: 0.5vw;
        font-size: 1.85vw;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        font-family: heading, sans-serif;
        text-transform:uppercase;
    }

    .cara-pesan-contact .btn-contact:hover {
        background: #055a7a;
        transform: scale(1.02);
    }

    .cara-pesan-contact .btn-contact iconify-icon {
        font-size: 1.7vw;
    }

    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 768px) {
        .cara-pesan-header {
            padding: 2vw 5vw;
        }

        .cara-pesan-header h1 {
            font-size: 5vw;
        }

        .cara-pesan-header p {
            font-size: 2.5vw;
        }

        .cara-pesan-content {
            padding: 3vw 5vw 5vw 5vw;
        }

        .cara-pesan-steps {
            max-width: 100%;
        }

        .cara-pesan-steps .section-title {
            font-size: 4vw;
        }

        .cara-pesan-steps .section-subtitle {
            font-size: 2.5vw;
        }

        .steps-grid {
            grid-template-columns: 1fr;
            gap: 2vw;
        }

        .step-card {
            padding: 4vw 3vw;
        }

        .step-card .step-number {
            width: 8vw;
            height: 8vw;
            font-size: 3.5vw;
        }

        .step-card .step-icon {
            font-size: 6vw;
        }

        .step-card h3 {
            font-size: 3vw;
        }

        .step-card p {
            font-size: 2.5vw;
        }

        .cara-pesan-illustration {
            padding: 4vw;
        }

        .cara-pesan-illustration h3 {
            font-size: 3.5vw;
        }

        .cara-pesan-illustration p {
            font-size: 2.5vw;
        }

        .illustration-grid {
            grid-template-columns: 1fr;
            gap: 2vw;
        }

        .illustration-item {
            padding: 3vw;
        }

        .illustration-item .ill-icon {
            font-size: 8vw;
        }

        .illustration-item h4 {
            font-size: 2.8vw;
        }

        .illustration-item p {
            font-size: 2.2vw;
        }

        .cara-pesan-faq h3 {
            font-size: 3.5vw;
        }

        .cara-pesan-faq .faq-subtitle {
            font-size: 2.5vw;
        }

        .cara-pesan-faq .faq-list {
            max-width: 100%;
        }

        .cara-pesan-faq .faq-question {
            font-size: 2.5vw;
            padding: 2.5vw 3vw;
        }

        .cara-pesan-faq .faq-answer-content {
            font-size: 2.3vw;
            padding: 0 3vw 2.5vw 3vw;
        }

        .cara-pesan-contact p {
            font-size: 2.5vw;
        }

        .cara-pesan-contact .btn-contact {
            padding: 2vw 5vw;
            font-size: 2.5vw;
        }

        .cara-pesan-contact .btn-contact iconify-icon {
            font-size: 3.5vw;
        }
    }

    @media (max-width: 480px) {
        .cara-pesan-header {
            padding: 3vw 3vw;
        }

        .cara-pesan-header h1 {
            font-size: 6vw;
        }

        .cara-pesan-header p {
            font-size: 3vw;
        }

        .cara-pesan-content {
            padding: 4vw 3vw 6vw 3vw;
        }

        .cara-pesan-steps .section-title {
            font-size: 5vw;
        }

        .cara-pesan-steps .section-subtitle {
            font-size: 3vw;
        }

        .step-card .step-number {
            width: 10vw;
            height: 10vw;
            font-size: 4.5vw;
        }

        .step-card h3 {
            font-size: 3.5vw;
        }

        .step-card p {
            font-size: 3vw;
        }

        .cara-pesan-illustration h3 {
            font-size: 4vw;
        }

        .cara-pesan-illustration p {
            font-size: 3vw;
        }

        .illustration-item .ill-icon {
            font-size: 10vw;
        }

        .illustration-item h4 {
            font-size: 3.5vw;
        }

        .illustration-item p {
            font-size: 2.8vw;
        }

        .cara-pesan-faq h3 {
            font-size: 4vw;
        }

        .cara-pesan-faq .faq-subtitle {
            font-size: 3vw;
        }

        .cara-pesan-faq .faq-question {
            font-size: 3vw;
            padding: 3vw 4vw;
        }

        .cara-pesan-faq .faq-answer-content {
            font-size: 2.8vw;
            padding: 0 4vw 3vw 4vw;
        }

        .cara-pesan-contact .btn-contact {
            font-size: 3vw;
            padding: 2.5vw 6vw;
        }
    }
</style>

<div class="cara-pesan-container">
    {{-- HEADER --}}
    <div class="cara-pesan-header">
        <h1>Cara Pesan</h1>
        <p>Panduan lengkap cara memesan produk di Barokah Sport</p>
    </div>

    {{-- CONTENT --}}
    <div class="cara-pesan-content">
        <div class="cara-pesan-steps">
            <h2 class="section-title">Mudah & Cepat</h2>
            <p class="section-subtitle">Ikuti langkah-langkah berikut untuk memesan produk</p>

            {{-- STEP GRID --}}
            <div class="steps-grid">
                <div class="step-card">
                    <span class="step-number">1</span>
                    <h3>Pilih Produk</h3>
                    <p>Cari dan pilih produk yang Anda inginkan dari katalog kami. Klik produk untuk melihat detail lengkapnya.</p>
                </div>

                <div class="step-card">
                    <span class="step-number">2</span>
                    <h3>Pilih Varian</h3>
                    <p>Pilih ukuran, warna, dan varian yang sesuai dengan kebutuhan Anda. Pastikan stok tersedia.</p>
                </div>

                <div class="step-card">
                    <span class="step-number">3</span>
                    <h3>Tambahkan ke Keranjang</h3>
                    <p>Klik "Tambah ke Keranjang" untuk menyimpan produk. Anda bisa lanjut belanja atau langsung checkout.</p>
                </div>

                <div class="step-card">
                    <span class="step-number">4</span>
                    <h3>Checkout</h3>
                    <p>Isi alamat pengiriman, pilih metode pengiriman, dan pilih metode pembayaran yang Anda inginkan.</p>
                </div>

                <div class="step-card">
                    <span class="step-number">5</span>
                    <h3>Pembayaran</h3>
                    <p>Lakukan pembayaran sesuai instruksi yang diberikan. Kami akan memproses pesanan setelah pembayaran terkonfirmasi.</p>
                </div>

                <div class="step-card">
                    <span class="step-number">6</span>
                    <h3>Pesanan Dikirim</h3>
                    <p>Pesanan akan dikirim dan Anda akan mendapatkan nomor resi untuk melacak paket Anda.</p>
                </div>
            </div>

            {{-- ILUSTRASI --}}
            <div class="cara-pesan-illustration">
                <h3>Tips Berbelanja</h3>
                <p>Beberapa tips untuk pengalaman berbelanja yang lebih nyaman</p>

                <div class="illustration-grid">
                    <div class="illustration-item">
                        <h4>Cek Foto Produk</h4>
                        <p>Periksa foto produk dari berbagai sudut untuk melihat detailnya.</p>
                    </div>
                    <div class="illustration-item">
                        <h4>Periksa Ukuran</h4>
                        <p>Gunakan panduan ukuran untuk memilih ukuran yang tepat.</p>
                    </div>
                    <div class="illustration-item">
                        <h4>Baca Ulasan</h4>
                        <p>Lihat ulasan pembeli lain untuk mengetahui kualitas produk.</p>
                    </div>
                </div>
            </div>

            {{-- FAQ CARA PESAN --}}
            <div class="cara-pesan-faq" id="faq-section">
                <h3>Pertanyaan Seputar Pemesanan</h3>
                <p class="faq-subtitle">Pertanyaan yang sering diajukan tentang cara pemesanan</p>

                <div class="faq-list">
                    @if($faqs->count() > 0)
                        @foreach($faqs as $faq)
                            <div class="faq-item">
                                <button class="faq-question" onclick="toggleFaq(this)">
                                    <span>{{ $faq->question }}</span>
                                    <span class="faq-icon"><iconify-icon icon="tabler:chevron-down"></iconify-icon></span>
                                </button>
                                <div class="faq-answer">
                                    <div class="faq-answer-content">
                                        {!! $faq->answer !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="faq-empty">
                            <p>Belum ada FAQ terkait cara pemesanan.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- CONTACT --}}
            <div class="cara-pesan-contact">
                <p>Masih bingung? Kami siap membantu Anda!</p>
                <a href="{{ route('customer.contact') }}" class="btn-contact">
                    <iconify-icon icon="mdi:headset"></iconify-icon>
                    Hubungi Kami
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // ============================================
    // FAQ TOGGLE
    // ============================================
    function toggleFaq(button) {
        const answer = button.nextElementSibling;
        const icon = button.querySelector('.faq-icon');
        const isActive = answer.classList.contains('active');

        // Close all other FAQs
        document.querySelectorAll('.cara-pesan-faq .faq-answer').forEach(function(el) {
            if (el !== answer) {
                el.classList.remove('active');
                el.previousElementSibling.querySelector('.faq-icon').classList.remove('open');
            }
        });

        // Toggle current FAQ
        if (isActive) {
            answer.classList.remove('active');
            icon.classList.remove('open');
        } else {
            answer.classList.add('active');
            icon.classList.add('open');
        }
    }

    // Auto-open first FAQ
    document.addEventListener('DOMContentLoaded', function() {
        const firstFaq = document.querySelector('.cara-pesan-faq .faq-item');
        if (firstFaq) {
            const firstButton = firstFaq.querySelector('.faq-question');
            if (firstButton) {
                setTimeout(function() {
                    toggleFaq(firstButton);
                }, 500);
            }
        }

        // Cek URL hash
        if (window.location.hash === '#faq-section') {
            setTimeout(function() {
                const faqSection = document.getElementById('faq-section');
                if (faqSection) {
                    faqSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }, 500);
        }
    });
</script>

@endsection