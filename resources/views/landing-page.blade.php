<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sawdeera Tour & Travel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    {{-- Bootsrap 5 Ikon --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/landing-page.css') }}">
</head>
<body>

{{-- Content --}}
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top py-3">
        <div class="container">

            <div class="w-100 d-flex justify-content-between align-items-center px-4 py-2 shadow"
                style="background:#f3e7d2; border-radius:50px;">

                <a class="navbar-brand fw-bold" href="/">
                    <img src="{{ asset('img/logo.png') }}" height="45">
                </a>

                <ul class="navbar-nav mx-auto mb-0 gap-3">
                    <li class="nav-item"><a class="nav-link text-dark fw-medium" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-medium" href="#tentang-kami">Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-medium" href="#kolaborasi">Kolaborasi</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-medium" href="#jenis-layanan">Jenis Layanan</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-medium" href="#testimoni">Testimoni</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-medium" href="#galeri">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-medium" href="#kontak">Kontak</a></li>
                </ul>

                <a href="#" class="btn px-4"
                style="background:#e6c27a; border-radius:50px; font-weight:600;">
                    Login
                </a>

            </div>

        </div>
    </nav>

    <!-- Beranda Section -->
    <section id="beranda" class="position-relative text-white" style="background: url('{{ asset('img/background1.jpg') }}') center/cover no-repeat; min-height:100vh;">

        <!-- Overlay -->
        <div style="position:absolute; inset:0; background:rgba(60,30,10,0.50);"></div>

        <div class="container position-relative py-5">

            <!-- HERO CONTENT -->
            <div class="text-center pt-5 mt-5">

                <h1 class="fw-bold display-4 fade-up"
                    style="font-family:'Playfair Display', serif;">
                    Umrah Nyaman, Aman dan Berkesan <br>
                    Bersama Sawdeera Tour & Travel
                </h1>

                <p class="mt-3 fs-5 fade-up" style="animation-delay:.3s;">
                    <i>
                        "Nikmati perjalanan spiritual Anda dengan layanan profesional,
                        bimbingan ibadah, dan akomodasi terbaik."
                    </i>
                </p>

                <div class="mt-4 d-flex justify-content-center gap-3">

                    <a href="#" class="btn btn-gold slide-left btn-active-light">
                        Daftar Sekarang
                    </a>

                    <a href="https://wa.me/6281287234572?text=Saya%20ingin%20konsultasi%20terkait%20umrah"
                        target="_blank"
                        class="btn btn-outline-gold slide-right btn-active-light">
                        Konsultasi Gratis
                    </a>

                </div>

            </div>

            <!-- SERVICES (MASIH DI DALAM HERO) -->
            <div class="row g-4 mt-5 pt-4">

                <div class="col-md-4">
                    <div class="service-card fade-up-service delay-1">
                        <img src="{{ asset('img/thumb1.jpg') }}" class="w-100">
                        <div class="service-overlay"></div>
                        <div class="service-title">Umrah</div>
                    </div>
                </div>
            
                <div class="col-md-4">
                    <div class="service-card fade-up-service delay-2">
                        <img src="{{ asset('img/thumb2.jpg') }}" class="w-100">
                        <div class="service-overlay"></div>
                        <div class="service-title">Wedding Umrah</div>
                    </div>
                </div>
            
                <div class="col-md-4">
                    <div class="service-card fade-up-service delay-3">
                        <img src="{{ asset('img/thumb3.jpg') }}" class="w-100">
                        <div class="service-overlay"></div>
                        <div class="service-title">Wisata Thaif</div>
                    </div>
                </div>
            
            </div>

        </div>
    </section>

    <!-- Tentang Kami Section -->
    <section class="py-5 text-white" id="tentang-kami">
        <div class="container text-center">

            <h3 class="text-uppercase mb-3" style="letter-spacing:2px; opacity:.8;">
                Tentang Kami
            </h3>

            <hr style="width:300px; margin:0 auto 30px auto; border:1px solid #e6c27a;">

            <h3 class="mb-4" 
                style="font-family:'Playfair Display', serif;">
                Sawdeera Tour & Travel
            </h3>

            <p class="mx-auto" style="max-width:900px; line-height:1.8; opacity:.9;">
                Didirikan pada tahun 2024 oleh H. Nanang Kusnadi, Sawdeera Tour hadir sebagai biro perjalanan ibadah Umrah dan Haji
                yang memadukan layanan profesional dengan bimbingan spiritual sesuai sunnah Rasulullah ﷺ.
                Dengan dukungan tim berpengalaman dan pembimbing syar’i, kami melayani berbagai program seperti
                Umrah Reguler, Umrah + Thaif, hingga Umrah + Wedding di Mekkah.
                Komitmen kami adalah menyelenggarakan perjalanan ibadah yang amanah, terarah, dan bermakna bagi setiap jamaah.
            </p>

        </div>
    </section>

    <!-- Kolaborasi Section -->
    <section class="py-5 text-white" id="kolaborasi">
        <div class="container text-center">

            <h3 class="text-uppercase mb-3" style="letter-spacing:2px; opacity:.8;">
                Mitra & Kolaborasi Strategis
            </h3>

            <hr style="width:600px; margin:0 auto 30px auto; border:1px solid #e6c27a;">

            <h3 class="mb-5" 
                style="font-family:'Playfair Display', serif;">
                Dipercaya Oleh Berbagai Institusi & Partner
            </h3>

            <div class="row g-4">

                <!-- 8 LOGO (4x2) -->
                <!-- Row 1 -->
                <div class="col-6 col-md-3">
                    <div class="partner-logo">Ikon Kerja Sama</div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="partner-logo">Ikon Kerja Sama</div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="partner-logo">Ikon Kerja Sama</div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="partner-logo">Ikon Kerja Sama</div>
                </div>

                <!-- Row 2 -->
                <div class="col-6 col-md-3">
                    <div class="partner-logo">Ikon Kerja Sama</div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="partner-logo">Ikon Kerja Sama</div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="partner-logo">Ikon Kerja Sama</div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="partner-logo">Ikon Kerja Sama</div>
                </div>

            </div>

        </div>
    </section>

    <!-- Jenis Layanan Section -->
    <section class="py-5 text-white" id="jenis-layanan">
        <div class="container text-center">

            <h3 class="text-uppercase mb-3" style="letter-spacing:2px; opacity:.8;">
                Jenis Layanan Tersedia
            </h3>

            <hr style="width:450px; margin:0 auto 30px auto; border:1px solid #e6c27a;">

            <div class="position-relative d-flex justify-content-center align-items-center">

                <!-- Prev -->
                <button id="prevBtn" class="nav-btn me-4">&#10094;</button>

                <!-- Wrapper -->
                <div class="overflow-hidden" style="width: 2300px;">
                    <div id="paketContainer" class="d-flex transition-slide"></div>
                </div>

                <!-- Next -->
                <button id="nextBtn" class="nav-btn ms-4">&#10095;</button>

            </div>

        </div>
    </section>

    <!-- Testimoni Section -->
    <section class="py-5 text-white" id="testimoni">
        <div class="container text-center">

            <h3 class="text-uppercase mb-3" style="letter-spacing:2px; opacity:.8;">
                Apa Kata Mereka?
            </h3>

            <hr style="width:400px; margin:0 auto 40px auto; border:1px solid #e6c27a;">

            <div class="position-relative d-flex justify-content-center align-items-center">

                <!-- Prev -->
                <button id="prevTestimoni" class="nav-btn me-3">&#10094;</button>

                <!-- Card -->
                <div id="testimoniWrapper" style="max-width:700px; width:100%;">
                    <div id="testimoniContainer"></div>
                </div>

                <!-- Next -->
                <button id="nextTestimoni" class="nav-btn ms-3">&#10095;</button>

            </div>

        </div>
    </section>

    <!-- Galeri Section -->
    <section class="py-5 galeri-section text-white" id="galeri">
        <div class="container text-center">

            <h3 class="text-uppercase mb-3" style="letter-spacing:2px; opacity:.8;">
                Galeri
            </h3>

            <hr style="width:150px; margin:0 auto 30px auto; border:1px solid #e6c27a;">

            <div class="row g-4">

                <!-- Gambar Besar -->
                <div class="col-lg-6">
                    <div class="galeri-item galeri-large">
                        <img src="{{ asset('img/Rectangle20.png')}}" class="img-fluid" alt="Galeri 1">
                    </div>
                </div>

                <!-- Gambar Kecil -->
                <div class="col-lg-6">
                    <div class="row g-4">

                        <div class="col-6">
                            <div class="galeri-item">
                                <img src="{{ asset('img/Rectangle21.png')}}" class="img-fluid" alt="Galeri 2">
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="galeri-item">
                                <img src="{{ asset('img/Rectangle22.png')}}" class="img-fluid" alt="Galeri 3">
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="galeri-item">
                                <img src="{{ asset('img/Rectangle23.png')}}" class="img-fluid" alt="Galeri 4">
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="galeri-item">
                                <img src="{{ asset('img/Rectangle26.png')}}" class="img-fluid" alt="Galeri 5">
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </section>

    {{-- Kontak Section --}}
    <section class="footer-section py-5" id="kontak">
        <div class="container position-relative footer-relative">
    
            <!-- LEFT -->
            <div class="footer-block footer-left">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ asset('img/logo.png') }}" width="60" class="me-2">
                    <h5 class="mb-0 fw-bold text-brown">Sawdeera</h5>
                </div>
    
                <p class="footer-text">
                    Sawdeera adalah biro perjalanan ibadah yang berkomitmen memberikan layanan profesional
                    untuk perjalanan Umrah dan Haji. Kami menghadirkan pengalaman spiritual yang aman,
                    nyaman, dan bermakna bersama tim pembimbing ibadah yang berpengalaman.
                </p>
            </div>
    
    
            <!-- CENTER (Alamat) -->
            <div class="footer-block footer-center">
                <h6 class="footer-title">Alamat</h6>
                <p class="footer-text">
                    Jl. Kp. Tukang Kajang, RT.25/RW.12, Bojong Renged,<br>
                    Kec. Teluknaga, Tangerang
                </p>
    
                <h6 class="footer-title mt-3">Kontak</h6>
                <p class="footer-text mb-1">081287234572</p>
                <p class="footer-text">info@sawdeera.com</p>
            </div>
    
    
            <!-- RIGHT -->
            <div class="footer-block footer-right">
                <h6 class="footer-title">Social Media</h6>
    
                <div class="social-group mb-3">
                    <a href="#" class="social-box">
                        <i class="bi bi-facebook"></i>
                    </a>
                
                    <a href="#" class="social-box">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                
                    <a href="#" class="social-box">
                        <i class="bi bi-tiktok"></i>
                    </a>
                
                    <a href="#" class="social-box">
                        <i class="bi bi-instagram"></i>
                    </a>
                </div>
    
                <h6 class="footer-title">Layanan Lainnya</h6>
                <p class="footer-text mb-0">
                    Umrah<br>
                    Wedding Umrah<br>
                    Wisata Thaif<br>
                    Syarat & Ketentuan
                </p>
            </div>
    
        </div>
    </section>
{{-- End Content --}}

{{-- Modal --}}
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content custom-brown-modal">

            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <p><strong>Durasi:</strong> <span id="modalHari"></span></p>
                <p><strong>Maskapai:</strong> <span id="modalMaskapai"></span></p>
                <p><strong>Hotel Makkah:</strong> <span id="modalHotelMakkah"></span></p>
                <p><strong>Hotel Madinah:</strong> <span id="modalHotelMadinah"></span></p>
                <p><strong>Keberangkatan:</strong> <span id="modalKeberangkatan"></span></p>

                <hr>

                <h6 class="fw-bold">Fasilitas:</h6>
                <ul id="modalFasilitas"></ul>

                <hr>

                <h4 class="text-warning fw-bold" id="modalHarga"></h4>

            </div>

        </div>
    </div>
</div>
{{-- End Modal --}}


{{-- Script --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

    const paketData = [
        {
            hari:12,
            label:"SIRAH",
            nama:"UMRAH SIRAH NABAWIYAH THAIF",
            pax:30,
            maskapai:"Garuda Indonesia",
            harga:"Rp34.900.000,-",

            hotelMakkah:"Swissotel Makkah",
            hotelMadinah:"Anwar Al Madinah Mövenpick",
            keberangkatan:"10 Oktober 2026",
            fasilitas:[
                "Tiket Pesawat PP",
                "Visa Umrah",
                "Hotel Bintang 5",
                "Makan 3x sehari",
                "Bus AC Full Trip",
                "Tour Thaif",
                "Pembimbing Ustadz",
                "Air Zamzam 5L"
            ]
        },
        {
            hari:9,
            label:"WEDDING",
            nama:"UMRAH + WEDDING DI MEKKAH",
            pax:25,
            maskapai:"Saudi Airlines",
            harga:"Rp33.900.000,-",

            hotelMakkah:"Pullman Zamzam Makkah",
            hotelMadinah:"Al Aqeeq Madinah",
            keberangkatan:"15 November 2026",
            fasilitas:[
                "Tiket Pesawat PP",
                "Visa Umrah",
                "Hotel Bintang 5",
                "Dokumentasi Wedding",
                "Makeup & Busana Wedding",
                "Makan 3x sehari",
                "Pembimbing Ustadz",
                "Air Zamzam 5L"
            ]
        },
        {
            hari:9,
            label:"THAIF",
            nama:"UMRAH WISATA THAIF",
            pax:20,
            maskapai:"Lion Air",
            harga:"Rp35.900.000,-",

            hotelMakkah:"Hilton Suites Makkah",
            hotelMadinah:"Saja Al Madinah",
            keberangkatan:"5 Desember 2026",
            fasilitas:[
                "Tiket Pesawat PP",
                "Visa Umrah",
                "Hotel Bintang 4",
                "Makan 3x sehari",
                "Tour Thaif Full Day",
                "City Tour Makkah & Madinah",
                "Pembimbing Ibadah"
            ]
        },
        {
            hari:9,
            label:"REGULER",
            nama:"UMRAH REGULER EKONOMI",
            pax:40,
            maskapai:"Emirates",
            harga:"Rp31.500.000,-",

            hotelMakkah:"Emaar Grand Hotel",
            hotelMadinah:"Odst Al Madinah",
            keberangkatan:"12 Januari 2027",
            fasilitas:[
                "Tiket Pesawat PP",
                "Visa Umrah",
                "Hotel Bintang 4",
                "Makan 2x sehari",
                "Bus AC",
                "Manasik 2x",
                "Air Zamzam 5L"
            ]
        },
        {
            hari:12,
            label:"VIP",
            nama:"UMRAH VIP HOTEL 5★ VIEW KA'BAH",
            pax:15,
            maskapai:"Qatar Airways",
            harga:"Rp45.000.000,-",

            hotelMakkah:"Fairmont Clock Tower",
            hotelMadinah:"Madinah Hilton",
            keberangkatan:"20 Januari 2027",
            fasilitas:[
                "Tiket Pesawat PP Direct",
                "Visa Umrah",
                "Hotel Bintang 5 View Ka'bah",
                "Makan Buffet 3x sehari",
                "Private Bus",
                "Pembimbing Eksklusif",
                "Handling VIP"
            ]
        },
        {
            hari:15,
            label:"RAMADHAN",
            nama:"UMRAH RAMADHAN 15 HARI",
            pax:35,
            maskapai:"Etihad Airways",
            harga:"Rp49.000.000,-",

            hotelMakkah:"Swissotel Maqam",
            hotelMadinah:"Anwar Al Madinah",
            keberangkatan:"5 Ramadhan 1448 H",
            fasilitas:[
                "Tiket Pesawat PP",
                "Visa Umrah",
                "Hotel Bintang 5",
                "Sahur & Berbuka",
                "I'tikaf 10 Hari Terakhir",
                "Kajian Ramadhan",
                "Air Zamzam 5L"
            ]
        },
        {
            hari:13,
            label:"PLUS TURKI",
            nama:"UMRAH PLUS TURKI 13 HARI",
            pax:28,
            maskapai:"Turkish Airlines",
            harga:"Rp52.000.000,-",

            hotelMakkah:"Pullman Zamzam",
            hotelMadinah:"Sofitel Madinah",
            keberangkatan:"15 Februari 2027",
            fasilitas:[
                "Tiket Pesawat PP",
                "Visa Umrah",
                "Hotel Bintang 5",
                "City Tour Istanbul",
                "Blue Mosque & Hagia Sophia",
                "Makan 3x sehari",
                "Tour Leader Indonesia"
            ]
        },
        {
            hari:10,
            label:"PRIVATE",
            nama:"UMRAH PRIVATE FAMILY",
            pax:10,
            maskapai:"Garuda Indonesia",
            harga:"Rp55.000.000,-",

            hotelMakkah:"Hilton Convention Makkah",
            hotelMadinah:"Intercontinental Madinah",
            keberangkatan:"Custom Schedule",
            fasilitas:[
                "Tiket Pesawat PP Flexible",
                "Visa Umrah",
                "Hotel Bintang 5",
                "Private Bus & Guide",
                "Jadwal Fleksibel",
                "Manasik Private",
                "Air Zamzam 5L"
            ]
        }
    ];
    
    function loadPaket() {
        const container = document.getElementById("paketContainer");
        container.innerHTML = "";
    
        paketData.forEach((paket, index) => {
            container.innerHTML += `
                <div class="package-card text-white">

                    <div class="package-content">

                        <div class="badge-days">
                            <h4>${paket.hari}</h4>
                            <small>HARI</small>
                        </div>

                        <h6 class="text-warning mb-3">${paket.label}</h6>

                        <h5 class="fw-bold mb-3 package-title">
                            ${paket.nama}
                        </h5>

                        <p>Available Pax: ${paket.pax} Pax</p>
                        <p>Maskapai: ${paket.maskapai}</p>

                    </div>

                    <div>
                        <div class="package-price my-3">
                            ${paket.harga}
                        </div>

                        <button class="btn w-100"
                                style="background-color:#FFFBDE;"
                                onclick="showDetail(${index})">
                            Lihat Detail
                        </button>
                    </div>

                </div>
            `;
        });
    }
    
    let currentIndex = 0;
    const visibleItems = 2;
    
    document.getElementById("nextBtn").onclick = function() {
        if (currentIndex < paketData.length - visibleItems) {
            currentIndex++;
            updateSlide();
        }
    };
    
    document.getElementById("prevBtn").onclick = function() {
        if (currentIndex > 0) {
            currentIndex--;
            updateSlide();
        }
    };
    
    function updateSlide() {
        const container = document.getElementById("paketContainer");
        const cardWidth = document.querySelector(".package-card").offsetWidth + 30;
        container.style.transform = `translateX(-${currentIndex * cardWidth}px)`;
    }
    
    loadPaket();

    function showDetail(index) {
        const paket = paketData[index];

        document.getElementById("modalTitle").innerText = paket.nama;
        document.getElementById("modalHari").innerText = paket.hari + " Hari";
        document.getElementById("modalMaskapai").innerText = paket.maskapai;
        document.getElementById("modalHotelMakkah").innerText = paket.hotelMakkah;
        document.getElementById("modalHotelMadinah").innerText = paket.hotelMadinah;
        document.getElementById("modalKeberangkatan").innerText = paket.keberangkatan;
        document.getElementById("modalHarga").innerText = paket.harga;

        const fasilitasList = document.getElementById("modalFasilitas");
        fasilitasList.innerHTML = "";

        paket.fasilitas.forEach(item => {
            fasilitasList.innerHTML += `<li>${item}</li>`;
        });

        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();
    }
    
</script>

<script>

    const testimoniData = [
        {
            nama: "Ibu Gendis",
            kota: "Tangerang",
            pesan: "Alhamdulillah proses umrah saya bersama Sawdeera sangat mudah dan nyaman. Semua informasi jelas dan transparan. Pelayanannya ramah, saya jadi fokus beribadah.",
            rating: 5
        },
        {
            nama: "Pak Ahmad",
            kota: "Bekasi",
            pesan: "Hotel dekat Masjidil Haram, makan enak, pembimbing sangat membantu. Sangat direkomendasikan!",
            rating: 5
        },
        {
            nama: "Ibu Siti",
            kota: "Depok",
            pesan: "Perjalanan sangat terorganisir dengan baik. Dari keberangkatan sampai pulang semuanya lancar.",
            rating: 4
        }
    ];
    
    let currentTestimoni = 0;
    
    function loadTestimoni() {
        const data = testimoniData[currentTestimoni];
        const container = document.getElementById("testimoniContainer");
    
        let stars = "";
        for (let i = 0; i < data.rating; i++) {
            stars += "★";
        }
    
        container.innerHTML = `
            <div class="testimoni-card">
                <div class="testimoni-stars">${stars}</div>
                <div class="testimoni-text">
                    “${data.pesan}”
                </div>
                <div class="testimoni-name">
                    — ${data.nama}, ${data.kota}
                </div>
            </div>
        `;
    }
    
    // Next
    document.getElementById("nextTestimoni").onclick = function () {
        if (currentTestimoni < testimoniData.length - 1) {
            currentTestimoni++;
        } else {
            currentTestimoni = 0;
        }
        loadTestimoni();
    };
    
    // Prev
    document.getElementById("prevTestimoni").onclick = function () {
        if (currentTestimoni > 0) {
            currentTestimoni--;
        } else {
            currentTestimoni = testimoniData.length - 1;
        }
        loadTestimoni();
    };
    
    // Load pertama
    loadTestimoni();
    
</script>
{{-- End Script --}}

</body>
</html>