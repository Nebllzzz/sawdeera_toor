<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Semua Paket Umrah</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/landing-page.css') }}">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #4C2B15;
        }

        .section-title {
            letter-spacing: 2px;
            opacity: .8;
        }

        #paketContainer {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
        }

        .package-card {
            background: linear-gradient(160deg, #5b2f15, #32180b);
            border-radius: 25px;
            padding: 35px;
            width: 350px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);

            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .package-title {
            min-height: 60px;
        }

        .package-price {
            font-size: 26px;
            font-weight: bold;
            color: #f1b521;
        }

        .badge-days {
            width: 70px;
            height: 70px;
            background: #2a140a;
            border-radius: 15px;
            margin: 0 auto 15px;
            padding-top: 8px;
        }

        .btn-detail {
            background: #FFFBDE;
            border-radius: 12px;
            font-weight: 500;
        }

        .btn-detail:hover {
            background: #e6c27a;
        }

        /* ✅ RESPONSIVE */
        @media (max-width: 992px) {
            .package-card {
                width: 45%;
            }
        }

        @media (max-width: 576px) {
            .package-card {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="container py-5 text-center text-white">

    <h3 class="text-uppercase section-title mb-3">
        Semua Paket Umrah
    </h3>

    <hr style="width:380px; margin:auto; border:1px solid #e6c27a;">

    <div id="paketContainer" class="mt-5"></div>

</div>


<!-- MODAL DETAIL (COPY DARI HOME BIAR CONSISTENT) -->
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

                <h6>Fasilitas:</h6>
                <ul id="modalFasilitas"></ul>

                <hr>

                <h6>Program:</h6>
                <ul id="modalProgram"></ul>

                <hr>

                <h6>Keberangkatan:</h6>
                <div id="modalKeberangkatanList"></div>

                <hr>

                <h4 id="modalHarga"></h4>

            </div>

        </div>
    </div>
</div>


<!-- SCRIPT -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

function loadAllPaket() {

    $.get('/paket/all', function(res) {

        let html = "";

        res.forEach((paket) => {

            html += `
                <div class="package-card text-white">

                    <div>

                        <div class="badge-days text-center">
                            <h4>${paket.durasi}</h4>
                            <small>HARI</small>
                        </div>

                        <h6 class="text-warning text-center">
                            ${paket.nama_paket.split(' ')[0] ?? 'PAKET'}
                        </h6>

                        <h5 class="fw-bold text-center package-title">
                            ${paket.nama_paket}
                        </h5>

                        <p class="mt-3">
                            Hotel Makkah<br>
                            <i>${paket.hotel_makkah?.nama ?? '-'}</i>
                        </p>

                        <hr>

                        <p>
                            Hotel Madinah<br>
                            <i>${paket.hotel_madinah?.nama ?? '-'}</i>
                        </p>

                    </div>

                    <div>
                        <div class="package-price my-3 text-center">
                            Rp ${parseInt(paket.harga).toLocaleString()}
                        </div>

                        <button class="btn w-100 btn-detail"
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

loadAllPaket();

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

</body>
</html>
