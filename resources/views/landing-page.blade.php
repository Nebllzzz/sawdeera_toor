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
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
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

                <a href="/login" class="btn px-4"
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
    <section class="py-5 text-white">
        <div class="container text-center">

            <h3 class="text-uppercase mb-3" style="letter-spacing:2px; opacity:.8;">
                Jenis Paket Umrah
            </h3>

            <hr style="width:380px; margin:0 auto 30px auto; border:1px solid #e6c27a;">

            <div id="paketContainer" class="d-flex flex-wrap justify-content-center"></div>

            <div class="mt-5">
                <a href="/paket" class="btn btn-sawdeera1 w-25">Selengkapnya</a>
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
                <p><strong>Hotel Makkah:</strong> <span id="modalHotelMakkah"></span></p>
                <p><strong>Hotel Madinah:</strong> <span id="modalHotelMadinah"></span></p>

                <hr>

                <h6 class="fw-bold">Fasilitas:</h6>
                <ul id="modalFasilitas"></ul>

                <hr>

                <h6 class="fw-bold">Program:</h6>
                <ul id="modalProgram"></ul>

                <hr>

                <h6 class="fw-bold">Jadwal Keberangkatan Yang Tersedia:</h6>
                <div id="modalKeberangkatanList"></div>

                <hr>

                <h4 class="text-warning fw-bold" id="modalHarga"></h4>

            </div>

        </div>
    </div>
</div>
{{-- End Modal --}}


{{-- Script --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function loadPaket() {

        $.get('/paket/home', function(res) {

            let html = "";

            res.forEach((paket) => {

                html += `
                    <div class="package-card text-white">

                        <div class="package-content">

                            <div class="badge-days">
                                <h4>${paket.durasi}</h4>
                                <small>HARI</small>
                            </div>

                            <h6 class="text-warning mb-3">
                                ${paket.nama_paket.split(' ')[0] ?? 'PAKET'}
                            </h6>

                            <h5 class="fw-bold mb-3 package-title">
                                ${paket.nama_paket}
                            </h5>

                            <p>Hotel Makkah<br/><i>${paket.hotel_makkah?.nama ?? '-'}</i></p>
                            <hr/>
                            <p>Hotel Madinah<br/><i>${paket.hotel_madinah?.nama ?? '-'}</i></p>

                        </div>

                        <div>
                            <div class="package-price my-3">
                                Rp ${parseInt(paket.harga).toLocaleString()}
                            </div>

                            <button class="btn w-100"
                                    style="background-color:#FFFBDE;"
                                    onclick="showDetail(${paket.id})">
                                Lihat Detail
                            </button>
                        </div>

                    </div>
                `;
            });

            $("#paketContainer").html(html);
        });
    }

    loadPaket();

    function showDetail(id) {

        $.get('/paket/detail/' + id, function(res) {

            let paket = res.paket;
            let keberangkatan = res.keberangkatan;
            console.log(keberangkatan);


            $("#modalTitle").text(paket.nama_paket);
            $("#modalHari").text(paket.durasi + " Hari");

            $("#modalHotelMakkah").text(paket.hotel_makkah.nama);
            $("#modalHotelMadinah").text(paket.hotel_madinah.nama);

            $("#modalHarga").text("Rp " + parseInt(paket.harga).toLocaleString());

            // ✅ fasilitas
            let fasilitasHtml = "";
            paket.fasilitas.forEach(f => {
                fasilitasHtml += `<li>${f.nama}</li>`;
            });
            $("#modalFasilitas").html(fasilitasHtml);

            // ✅ program
            let programHtml = "";
            paket.program.forEach(p => {
                programHtml += `<li>Hari ${p.hari} - ${p.deskripsi}</li>`;
            });
            $("#modalProgram").html(programHtml);

            // ✅ keberangkatan (ini yang kaya gambar 🔥)
            let keberangkatanHtml = "";

            keberangkatan.forEach(k => {

                keberangkatanHtml += `
                <div class="mb-4 p-3 border rounded">

                    <p><strong>Durasi:</strong> ${paket.durasi} Hari</p>

                    <p><strong>Tanggal Berangkat:</strong> ${k.tanggal_keberangkatan}</p>
                    <p><strong>Tanggal Pulang:</strong> ${k.tanggal_pulang}</p>

                    <p><strong>Maskapai Berangkat:</strong> ${k.maskapai_berangkat.nama}</p>
                    <p><strong>Maskapai Pulang:</strong> ${k.maskapai_pulang.nama}</p>

                    <hr>

                    <p><strong>Jam Berangkat:</strong> ${k.jam_berangkat}</p>
                    <p><strong>Jam Tiba:</strong> ${k.jam_tiba}</p>

                    <p><strong>Tour Leader:</strong>
                        ${k.leader
                            ? `${k.leader.nama} (${k.leader.no_telepon ?? '-'} / ${k.leader.email ?? '-'})`
                            : '-'}
                    </p>

                </div>
                `;
            });

            $("#modalKeberangkatanList").html(keberangkatanHtml);

            new bootstrap.Modal(document.getElementById('detailModal')).show();
        });
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
