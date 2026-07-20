@extends('layouts.main')
@php
    $isPackagePage = ($mode ?? 'departure') === 'packages';
@endphp
@section('title', $isPackagePage ? 'Paket Umrah' : 'Keberangkatan Saya')
@section('content')
    <div class="content-wrapper trip-page px-3">
        <section class="content py-4">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h2 class="font-weight-bold mb-1">{{ $isPackagePage ? 'Paket Umrah' : 'Keberangkatan Saya' }}</h2>
                        <small class="text-muted">Dashboard &nbsp;›&nbsp;
                            {{ $isPackagePage ? 'Paket Umrah' : 'Keberangkatan Saya' }}</small>
                    </div>
                    <a href="/dashboard" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-2"></i>Kembali ke
                        Dashboard</a>
                </div>

                @if (!$isPackagePage && $pengajuan)
                    @php
                        $p = $pengajuan->paketUmrah;
                    @endphp
                    <div class="selected-trip mb-3">
                        <div class="selected-cover" style="background-image:url('{{ asset('img/thumb1.jpg') }}')">
                            <span class="selected-duration">{{ $p->durasi }} Hari</span>
                        </div>
                        <div class="selected-body">
                            <h3>{{ $p->nama_paket }}</h3>
                            <div class="trip-grid">
                                <div>
                                    <small>Tanggal
                                        Berangkat</small><b>{{ $pengajuan->keberangkatan->tanggal_keberangkatan->translatedFormat('d F Y') }}</b>
                                </div>
                                <div>
                                    <small>Tanggal
                                        Pulang</small><b>{{ $pengajuan->keberangkatan->tanggal_pulang->translatedFormat('d F Y') }}</b>
                                </div>
                                <div><small>Maskapai
                                        Berangkat</small><b>{{ $pengajuan->keberangkatan->maskapaiBerangkat->nama ?? '-' }}</b>
                                </div>
                                <div><small>Maskapai
                                        Pulang</small><b>{{ $pengajuan->keberangkatan->maskapaiPulang->nama ?? '-' }}</b>
                                </div>
                                <div><small>Jam Berangkat</small><b>{{ $pengajuan->keberangkatan->jam_berangkat }}</b></div>
                                <div><small>Jam Tiba</small><b>{{ $pengajuan->keberangkatan->jam_tiba }}</b></div>
                                <div><small>Jam Pulang</small><b>{{ $pengajuan->keberangkatan->jam_pulang }}</b></div>
                                <div><small>Jam Tiba Pulang</small><b>{{ $pengajuan->keberangkatan->jam_tiba_pulang }}</b>
                                </div>
                                <div><small>Hotel Makkah</small><b>{{ $p->hotelMakkah->nama ?? '-' }}</b></div>
                                <div><small>Hotel Madinah</small><b>{{ $p->hotelMadinah->nama ?? '-' }}</b></div>
                                <div><small>Tour
                                        Leader</small><b>{{ $pengajuan->keberangkatan->leader->nama ?? 'Belum ditentukan' }}</b>
                                </div>
                                <div><small>Total Paket</small><b>Rp {{ number_format($p->harga, 0, ',', '.') }}</b></div>
                            </div>
                            <a href="{{ route('jemaah.itinerary') }}" class="btn-gold d-inline-block mt-4">Unduh Itinerary <i
                                    class="fas fa-file-pdf ml-2"></i></a>
                        </div>
                    </div>
                    <div class="selected-detail-grid">
                        <div class="selected-detail-card">
                            <h5><i class="fas fa-check-circle"></i> Fasilitas Paket</h5>
                            @forelse($p->fasilitas as $facility)
                                <span class="facility-chip">{{ $facility->nama }}</span>
                            @empty
                                <p class="text-muted mb-0">Belum ada rincian fasilitas.</p>
                            @endforelse
                        </div>
                        <div class="selected-detail-card">
                            <h5><i class="fas fa-route"></i> Program Perjalanan</h5>
                            @forelse($p->program as $program)
                                <div class="selected-program"><b>Hari
                                        {{ $program->hari }}</b><span>{{ $program->deskripsi }}</span></div>
                            @empty
                                <p class="text-muted mb-0">Belum ada rincian program.</p>
                            @endforelse
                        </div>
                    </div>
                    @php
                        $internalStatus = $pengajuan->keberangkatan?->status;
                        $publicApprovalLabel = in_array($internalStatus, ['pengajuan', 'direvisi'], true)
                            ? 'Dalam Pengajuan'
                            : ($pengajuan->status === 'setuju'
                                ? 'Jadwal Berlaku'
                                : ($pengajuan->status === 'reschedule'
                                    ? 'Pengajuan Perubahan'
                                    : 'Jadwal Berlaku'));
                        $departureLabels = [
                            'draft' => 'Draft',
                            'aktif' => 'Jadwal Aktif',
                            'pengajuan' => 'Dalam Pengajuan',
                            'direvisi' => 'Dalam Pengajuan',
                            'disetujui' => 'Jadwal Disetujui',
                            'berangkat' => 'Berangkat',
                            'berlangsung' => 'Sedang Berlangsung',
                            'pulang' => 'Perjalanan Pulang',
                            'selesai' => 'Selesai',
                        ];
                        $daysToDeparture = today()->diffInDays($pengajuan->keberangkatan->tanggal_keberangkatan, false);
                        $canRequestChange =
                            $daysToDeparture >= 45 && in_array($pengajuan->status, ['pendaftaran', 'setuju'], true);
                    @endphp
                    <div class="schedule-response-card mt-4">
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <small>Status Pengajuan Jadwal</small>
                                <h5>{{ $publicApprovalLabel }}</h5>
                                @if ($pengajuan->pendingReschedule)
                                    <p class="mb-0 text-muted">Perubahan menuju
                                        {{ $pengajuan->pendingReschedule->keberangkatanTujuan?->kode_keberangkatan }}
                                        sedang menunggu review admin.</p>
                                @else
                                    <p class="mb-0 text-muted">Jadwal ini otomatis berlaku setelah pengajuan paket berhasil.</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <small>Status Keberangkatan</small>
                                <h5>{{ $departureLabels[$internalStatus] ?? ucfirst($internalStatus ?? '-') }}</h5>
                                <p class="mb-0 text-muted">Informasi jadwal ditampilkan dalam bahasa yang mudah dipahami
                                    jemaah.</p>
                            </div>
                        </div>

                        @if (in_array($internalStatus, ['aktif', 'disetujui', 'berangkat'], true))
                            <hr>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                @if ($canRequestChange)
                                    <button class="btn btn-warning mb-2" id="btnOpenReschedule"><i
                                            class="fas fa-exchange-alt mr-2"></i>Reschedule</button>
                                @else
                                    <button class="btn btn-warning mb-2" disabled><i class="fas fa-exchange-alt mr-2"></i>Reschedule</button>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="notes-card mt-4">
                        <h5><i class="fas fa-sticky-note mr-2"></i>Notes</h5>
                        <ul class="mb-0">
                            <li>Batas pengajuan perubahan minimal H-45 sebelum berangkat.</li>
                            <li>Harap hadir di titik keberangkatan minimal 3 jam sebelum jadwal.</li>
                            <li>Pastikan identitas, dokumen perjalanan, dan pembayaran telah terverifikasi sebelum keberangkatan.</li>
                        </ul>
                    </div>
                @elseif ($isPackagePage)
                    <form class="filter-card mb-4" method="GET">
                        <div class="row align-items-end">
                            <div class="col-lg-3 col-md-6 form-group mb-lg-0"><label>Cari Paket</label>
                                <div class="input-icon"><i class="fas fa-search"></i><input name="search"
                                        value="{{ request('search') }}" class="form-control"
                                        placeholder="Cari paket umrah..."></div>
                            </div>
                            <div class="col-lg-2 col-md-6 form-group mb-lg-0"><label>Durasi</label><select name="durasi"
                                    class="form-control">
                                    <option value="">Semua Durasi</option>
                                    @foreach ($durations as $d)
                                        <option value="{{ $d }}" @selected(request('durasi') == $d)>
                                            {{ $d }} Hari</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6 form-group mb-lg-0"><label>Maskapai</label><select name="maskapai"
                                    class="form-control">
                                    <option value="">Semua Maskapai</option>
                                    @foreach ($maskapais as $m)
                                        <option value="{{ $m->id }}" @selected(request('maskapai') == $m->id)>
                                            {{ $m->nama }}</option>
                                    @endforeach
                                </select></div>
                            <div class="col-lg-3 col-md-6 form-group mb-lg-0"><label>Harga</label><select name="harga"
                                    class="form-control">
                                    <option value="">Semua Harga</option>
                                    <option value="under_25" @selected(request('harga') === 'under_25')>Di bawah Rp25 juta</option>
                                    <option value="25_35" @selected(request('harga') === '25_35')>Rp25–35 juta</option>
                                    <option value="over_35" @selected(request('harga') === 'over_35')>Di atas Rp35 juta</option>
                                </select></div>
                            <div class="col-lg-2"><button class="btn-filter w-100"><i
                                        class="fas fa-filter mr-2"></i>Filter</button></div>
                        </div>
                    </form>

                    <div class="row">
                        @forelse($pakets as $paket)
                            @php
                                $cover = ['thumb1.jpg', 'thumb2.jpg', 'thumb3.jpg', 'Rectangle20.png'][
                                    ($paket->id - 1) % 4
                                ];
                            @endphp
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <article class="package-card">
                                    <div class="package-cover"
                                        style="background-image:url('{{ asset('img/' . $cover) }}')">
                                        <span>{{ $paket->durasi }} Hari</span><i class="far fa-heart"></i>
                                    </div>
                                    <div class="package-body">
                                        <h5>{{ $paket->nama_paket }}</h5>
                                        <div class="package-tags">
                                            <span>
                                                <i class="fas fa-hotel"></i>
                                                {{ $paket->hotelMakkah->nama ?? 'Hotel Makkah' }}
                                            </span>
                                            <br>
                                            <span>
                                                <i class="fas fa-hotel"></i>
                                                {{ $paket->hotelMadinah->nama ?? 'Hotel Madinah' }}
                                            </span>
                                        </div>
                                        <small>Mulai dari</small>
                                        <div class="price">Rp
                                            {{ number_format($paket->harga, 0, ',', '.') }}<small>/pax</small></div>
                                        <button class="btn-gold w-100 show-detail" data-id="{{ $paket->id }}"
                                            data-durasi="{{ $paket->durasi }}">Lihat Detail <i
                                                class="fas fa-arrow-right ml-2"></i></button>
                                    </div>
                                </article>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="empty-state"><i class="fas fa-search"></i>
                                    <h5>Paket tidak ditemukan</h5><a href="/paket-umrah-jemaah">Reset filter</a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <div class="d-flex justify-content-center">{{ $pakets->links() }}</div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-plane-departure"></i>
                        <h5>Belum ada pengajuan paket umrah</h5>
                        <p class="text-muted mb-4">Ajukan paket umrah terlebih dahulu agar detail keberangkatan Anda muncul
                            di halaman ini.</p>
                        <a href="/paket-umrah-jemaah" class="btn-gold d-inline-block">Ajukan Paket Umrah</a>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <div class="modal fade" id="packageModal">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content modern-modal">
                <div class="modal-header">
                    <div><small>DETAIL PAKET</small>
                        <h4 id="detailName"></h4>
                    </div><button class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="detail-summary">
                                <div><small>Durasi</small><b id="detailDuration"></b></div>
                                <div><small>Hotel Makkah</small><b id="detailMakkah"></b></div>
                                <div><small>Hotel Madinah</small><b id="detailMadinah"></b></div>
                            </div>
                            <h6>Deskripsi</h6>
                            <p id="detailDescription" class="text-muted"></p>
                            <h6>Fasilitas</h6>
                            <div id="detailFacilities" class="chip-list"></div>
                            <h6 class="mt-4">Program Perjalanan</h6>
                            <div id="detailPrograms"></div>
                            <h6 class="mt-4">Keberangkatan Tersedia</h6>
                            <div id="detailSchedules"></div>
                        </div>
                        <div class="col-lg-5">
                            <div class="apply-box"><small>Harga per jemaah</small>
                                <h3 id="detailPrice"></h3>
                                <p class="text-muted">Jadwal keberangkatan dipilih pada langkah berikutnya.</p><button
                                    id="openApply" class="btn-gold w-100">Daftar Paket</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="applyModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modern-modal">
                <form id="applyForm">@csrf<input type="hidden" name="paket_umrah_id" id="applyPackageId">
                    <div class="modal-header">
                        <div><small>Pengajuan Keberangkatan</small>
                            <h4>Pilih Jadwal & Pembayaran</h4>
                        </div><button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group"><label>Jadwal Keberangkatan</label><select name="keberangkatan_id"
                                id="scheduleSelect" class="form-control" required>
                                <option value="">Memuat jadwal...</option>
                            </select></div>
                        <div id="scheduleInfo" class="info-callout d-none"></div>
                        <div class="form-group"><label>Skema Pembayaran</label><select name="jenis_pembayaran"
                                id="schemeSelect" class="form-control" required disabled>
                                <option value="">Pilih jadwal terlebih dahulu</option>
                            </select></div>
                        <div class="form-group d-none" id="dpGroup"><label>DP (Pembayaran Pertama)</label>
                            <div class="dp-options"><label><input type="radio" name="dp_persen" value="15">
                                    <span>15% dari harga paket</span></label><label><input type="radio" name="dp_persen"
                                        value="30"> <span>30% dari harga paket</span></label></div>
                        </div>
                        <div class="info-callout"><i class="fas fa-info-circle"></i> Cicilan 3 bulan memerlukan minimal
                            H-60, 6 bulan H-150, dan 12 bulan H-330. Pelunasan paling lambat H-30.</div>
                        <div class="payment-preview d-none" id="paymentPreview">
                            <h6>Rincian Rencana Pembayaran</h6>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Tahap</th>
                                            <th>Persentase</th>
                                            <th>Jatuh Tempo</th>
                                            <th>Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentPreviewRows"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light"
                            data-dismiss="modal">Batal</button><button class="btn-gold" id="submitApply">Ajukan & Buat
                            Rencana Pembayaran</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rescheduleModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modern-modal">
                <form id="rescheduleForm">@csrf
                    <div class="modal-header">
                        <div><small>Pengajuan Perubahan</small>
                            <h4>Pilih Jadwal Alternatif</h4>
                        </div>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div id="rescheduleOptions" class="mb-3"></div>
                        <input type="hidden" name="keberangkatan_tujuan_id" id="rescheduleTarget">
                        <div class="form-group">
                            <label>Alasan Pengajuan</label>
                            <textarea name="alasan_pengajuan" class="form-control" rows="3"
                                placeholder="Tuliskan alasan perubahan bila ada"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-warning" id="submitReschedule" disabled>Ajukan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .trip-page {
            background: #faf9f7 !important
        }

        .filter-card,
        .selected-trip,
        .package-card,
        .modern-modal,
        .empty-state {
            background: #fff;
            border: 1px solid #ece9e4;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(61, 43, 22, .07)
        }

        .filter-card {
            padding: 20px
        }

        .filter-card label {
            font-size: 12px;
            font-weight: 700
        }

        .input-icon {
            position: relative
        }

        .input-icon i {
            position: absolute;
            left: 14px;
            top: 14px;
            color: #999
        }

        .input-icon input {
            padding-left: 38px
        }

        .form-control {
            height: 44px;
            border-radius: 7px;
            border-color: #e3dfd9
        }

        .btn-filter,
        .btn-gold {
            border: 1px solid #c78b25;
            background: #c78b25;
            color: #fff;
            border-radius: 7px;
            padding: 11px 18px;
            font-weight: 600
        }

        .btn-filter {
            background: #fff;
            color: #9b6817
        }

        .btn-gold:hover {
            background: #a97219;
            color: #fff;
            text-decoration: none
        }

        .package-card {
            overflow: hidden;
            height: 100%;
            transition: .2s
        }

        .package-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 28px rgba(61, 43, 22, .13)
        }

        .package-cover {
            height: 185px;
            background-size: cover;
            background-position: center;
            position: relative
        }

        .package-cover:after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(transparent 60%, rgba(0, 0, 0, .4))
        }

        .package-cover span {
            position: absolute;
            bottom: 10px;
            left: 12px;
            background: #c78b25;
            color: #fff;
            padding: 5px 10px;
            border-radius: 6px;
            z-index: 1
        }

        .package-cover>i {
            position: absolute;
            right: 13px;
            top: 13px;
            color: #fff;
            font-size: 22px;
            z-index: 1
        }

        .package-body {
            padding: 17px
        }

        .package-body h5 {
            font-weight: 700;
            min-height: 46px
        }

        .package-tags {
            display: flex;
            gap: 5px;
            margin: 8px 0 20px
        }

        .package-tags span {
            background: #faf8f4;
            padding: 6px;
            font-size: 10px;
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .package-tags i {
            color: #bd8120
        }

        .price {
            font-weight: 800;
            font-size: 21px;
            margin-bottom: 13px
        }

        .price small {
            font-weight: 400;
            color: #777
        }

        .modern-modal {
            border: 0
        }

        .modern-modal .modal-header {
            background: #fbf8f1;
            border: 0
        }

        .detail-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 25px
        }

        .detail-summary>div {
            background: #fbf8f1;
            padding: 13px;
            border-radius: 8px
        }

        .detail-summary small,
        .detail-summary b {
            display: block
        }

        .chip-list span {
            display: inline-block;
            background: #f5efe4;
            color: #76501d;
            border-radius: 20px;
            padding: 7px 12px;
            margin: 3px
        }

        .program-row {
            border-left: 3px solid #c78b25;
            padding: 7px 12px;
            margin-bottom: 8px
        }

        .schedule-card {
            border: 1px solid #e4d7c6;
            border-radius: 9px;
            padding: 15px;
            margin-bottom: 12px;
            background: #fffdf9
        }

        .schedule-card-header {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            border-bottom: 1px solid #eee5d9;
            padding-bottom: 9px;
            margin-bottom: 10px
        }

        .schedule-card-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 9px 18px;
            font-size: 13px
        }

        .schedule-empty {
            background: #fff7e8;
            color: #79551e;
            padding: 14px;
            border-radius: 8px
        }

        .apply-box {
            background: #fbf7ef;
            border: 1px solid #eadbc3;
            border-radius: 10px;
            padding: 25px;
            position: sticky;
            top: 10px
        }

        .dp-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px
        }

        .dp-options label {
            border: 1px solid #ddd4c6;
            border-radius: 8px;
            padding: 14px;
            cursor: pointer
        }

        .info-callout {
            background: #fff7e8;
            color: #79551e;
            padding: 13px;
            border-radius: 8px;
            margin: 12px 0
        }

        .payment-preview {
            border: 1px solid #eadbc3;
            border-radius: 8px;
            overflow: hidden
        }

        .payment-preview h6 {
            background: #fbf7ef;
            font-weight: 700;
            margin: 0;
            padding: 13px 15px
        }

        .payment-preview th {
            border-top: 0;
            font-size: 12px;
            color: #79551e
        }

        .selected-trip {
            display: flex;
            overflow: hidden
        }

        .selected-cover {
            width: 32%;
            min-height: 290px;
            background-size: cover;
            background-position: center;
            position: relative
        }

        .selected-duration {
            position: absolute;
            left: 15px;
            bottom: 15px;
            color: #fff;
            background: #bd8120;
            padding: 7px 12px;
            border-radius: 7px;
            font-weight: 700
        }

        .selected-body {
            padding: 30px;
            flex: 1
        }

        .soft-badge {
            background: #eaf7ee;
            color: #278245;
            padding: 6px 10px;
            border-radius: 20px
        }

        .trip-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 25px
        }

        .trip-grid small,
        .trip-grid b {
            display: block
        }

        .selected-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px
        }

        .selected-detail-card {
            background: #fff;
            border: 1px solid #ece9e4;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 4px 18px rgba(61, 43, 22, .06)
        }

        .selected-detail-card h5 {
            font-weight: 700;
            margin-bottom: 16px
        }

        .selected-detail-card h5 i {
            color: #bd8120;
            margin-right: 7px
        }

        .schedule-response-card {
            background: #fff;
            border: 1px solid #ece9e4;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 4px 18px rgba(61, 43, 22, .06)
        }

        .schedule-response-card small {
            display: block;
            color: #777;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 5px
        }

        .schedule-response-card h5 {
            font-weight: 800;
            margin-bottom: 5px
        }

        .approval-options {
            display: grid;
            gap: 8px;
            color: #303030
        }

        .notes-card {
            background: #fff8e9;
            border: 1px solid #f0d9aa;
            border-radius: 10px;
            padding: 18px 20px;
            color: #493417
        }

        .notes-card h5 {
            font-weight: 800;
            margin-bottom: 10px
        }

        .notes-card ul {
            padding-left: 20px
        }

        .reschedule-option {
            border: 1px solid #e5e0d7;
            border-radius: 10px;
            padding: 13px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: .15s ease;
        }

        .reschedule-option.active,
        .reschedule-option:hover {
            border-color: #d99a2b;
            background: #fff9ef
        }

        .reschedule-option b,
        .reschedule-option small {
            display: block
        }

        .facility-chip {
            display: inline-block;
            background: #f7f0e5;
            color: #76501d;
            padding: 7px 11px;
            border-radius: 18px;
            margin: 3px
        }

        .selected-program {
            display: grid;
            grid-template-columns: 75px 1fr;
            border-left: 3px solid #bd8120;
            padding: 7px 10px;
            margin-bottom: 8px
        }

        .empty-state {
            text-align: center;
            padding: 70px
        }

        .empty-state i {
            font-size: 40px;
            color: #c78b25;
            margin-bottom: 15px
        }

        @media(max-width:768px) {
            .selected-trip {
                display: block
            }

            .selected-cover {
                width: 100%;
                min-height: 190px
            }

            .trip-grid,
            .detail-summary {
                grid-template-columns: 1fr 1fr
            }

            .selected-detail-grid {
                grid-template-columns: 1fr
            }

            .dp-options {
                grid-template-columns: 1fr
            }
        }
    </style>
    @push('scripts')
        <script>
            let selectedPackage = null,
                schedules = [];
            const rupiah = n => 'Rp ' + Number(n).toLocaleString('id-ID');
            const dateId = value => new Date(value).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
            const safe = value => $('<div>').text(value || '-').html();
            const schemeSteps = {
                sekali_bayar: 1,
                cicilan_3_bulan: 3,
                cicilan_6_bulan: 6,
                cicilan_12_bulan: 12
            };
            const resetPaymentPreview = () => {
                $('#paymentPreview').addClass('d-none');
                $('#paymentPreviewRows').empty();
            };
            const addDays = (date, days) => {
                const copy = new Date(date);
                copy.setDate(copy.getDate() + days);
                return copy;
            };
            const updatePaymentPreview = () => {
                const schedule = schedules.find(x => String(x.id) === String($('#scheduleSelect').val()));
                const scheme = $('#schemeSelect').val();
                const steps = schemeSteps[scheme];
                const total = Number(selectedPackage?.price || 0);
                if (!schedule || !scheme || !steps || !total) {
                    resetPaymentPreview();
                    return;
                }

                const dp = steps === 1 ? 100 : Number($('input[name="dp_persen"]:checked').val());
                if (steps > 1 && !dp) {
                    resetPaymentPreview();
                    return;
                }

                const departure = new Date(schedule.tanggal_keberangkatan);
                const finalDue = addDays(departure, -30);
                const start = new Date();
                start.setHours(0, 0, 0, 0);
                finalDue.setHours(0, 0, 0, 0);
                const totalDueDays = Math.max(0, Math.round((finalDue - start) / 86400000));
                const remainingPercent = 100 - dp;
                const basePercent = steps === 1 ? 100 : Math.round((remainingPercent / (steps - 1)) * 10000) / 10000;
                let usedPercent = 0;
                let usedNominal = 0;
                let rows = '';

                for (let i = 1; i <= steps; i++) {
                    const percent = i === 1 ? dp : (i === steps ? 100 - usedPercent : basePercent);
                    const nominal = i === steps ? total - usedNominal : Math.round(total * percent / 100);
                    const ratio = steps === 1 ? 0 : (i - 1) / (steps - 1);
                    const due = steps === 1 ? start : addDays(start, Math.round(totalDueDays * ratio));
                    const name = steps === 1 ? 'Pembayaran Penuh' : (i === 1 ? 'DP (Pembayaran Pertama)' : (i === steps ?
                        'Pelunasan' : `Pembayaran ke-${i}`));
                    rows += `<tr>
                        <td><b>${name}</b></td>
                        <td>${Number(percent).toLocaleString('id-ID', { maximumFractionDigits: 2 })}%</td>
                        <td>${dateId(due)}</td>
                        <td>${rupiah(nominal)}</td>
                    </tr>`;
                    usedPercent += percent;
                    usedNominal += nominal;
                }

                $('#paymentPreviewRows').html(rows);
                $('#paymentPreview').removeClass('d-none');
            };
            $(document).on('click', '.show-detail', function() {
                selectedPackage = {
                    id: $(this).data('id'),
                    durasi: $(this).data('durasi')
                };
                $.get('/paket-umrah-jemaah/paket/' + selectedPackage.id, res => {
                    const p = res.paket;
                    selectedPackage.price = Number(p.harga);
                    $('#detailName').text(p.nama_paket);
                    $('#detailDuration').text(p.durasi + ' Hari');
                    $('#detailMakkah').text(p.hotel_makkah?.nama || '-');
                    $('#detailMadinah').text(p.hotel_madinah?.nama || '-');
                    $('#detailDescription').text(p.deskripsi ||
                        'Paket perjalanan umrah dengan layanan terbaik dari Sawdeera Tour.');
                    $('#detailPrice').text(rupiah(p.harga));
                    $('#detailFacilities').html((p.fasilitas || []).map(f =>
                        `<span>${$('<div>').text(f.nama).html()}</span>`).join('') || '-');
                    $('#detailPrograms').html((p.program || []).map(x =>
                        `<div class="program-row"><b>Hari ${x.hari}</b><div>${$('<div>').text(x.deskripsi).html()}</div></div>`
                    ).join('') || '-');
                    const available = res.keberangkatan || [];
                    $('#detailSchedules').html(available.length ? available.map(k => `
                        <div class="schedule-card">
                            <div class="schedule-card-header">
                                <b>${dateId(k.tanggal_keberangkatan)} – ${dateId(k.tanggal_pulang)}</b>
                                <span class="soft-badge">${p.durasi} Hari</span>
                            </div>
                            <div class="schedule-card-grid">
                                <div><small>Maskapai Berangkat</small><br><b>${safe(k.maskapai_berangkat?.nama)}</b></div>
                                <div><small>Maskapai Pulang</small><br><b>${safe(k.maskapai_pulang?.nama)}</b></div>
                                <div><small>Jam Berangkat</small><br><b>${safe(k.jam_berangkat)}</b></div>
                                <div><small>Jam Tiba</small><br><b>${safe(k.jam_tiba)}</b></div>
                                <div><small>Tour Leader</small><br><b>${safe(k.leader?.nama)}</b></div>
                                <div><small>Kontak Tour Leader</small><br><b>${safe(k.leader ? ((k.leader.no_telepon || '-') + ' / ' + (k.leader.email || '-')) : '-')}</b></div>
                            </div>
                        </div>`).join('') :
                        '<div class="schedule-empty"><i class="fas fa-info-circle mr-2"></i>Belum ada jadwal keberangkatan yang tersedia untuk durasi paket ini.</div>'
                        );
                    if (!res.can_apply) {
                        $('#openApply').addClass('d-none');
                        $('.apply-box p').text(
                            'Anda sudah memiliki pengajuan aktif. Pengajuan paket baru dinonaktifkan.');
                    } else {
                        $('#openApply').removeClass('d-none').prop('disabled', !available.length)
                            .text(available.length ? 'Ajukan Keberangkatan untuk Paket Ini' :
                                'Jadwal Belum Tersedia');
                        $('.apply-box p').text('Jadwal keberangkatan dipilih pada langkah berikutnya.');
                    }
                    $('#packageModal').modal('show');
                });
            });
            $('#openApply').click(function() {
                $('#packageModal').modal('hide');
                $('#applyPackageId').val(selectedPackage.id);
                $('#scheduleSelect').html('<option value="">Memuat jadwal...</option>');
                $('#schemeSelect').html('<option value="">Pilih jadwal terlebih dahulu</option>').prop('disabled',
                true);
                $('#dpGroup').addClass('d-none').find('input').prop('checked', false).prop('required', false);
                resetPaymentPreview();
                $.get(`/paket-umrah-jemaah/jadwal-paket/${selectedPackage.id}/${selectedPackage.durasi}`, res => {
                    schedules = res;
                    let html = '<option value="">Pilih jadwal tersedia</option>';
                    res.forEach(x => html +=
                        `<option value="${x.id}">${dateId(x.tanggal_keberangkatan)} — ${x.maskapai_berangkat?.nama||'-'}</option>`
                    );
                    if (!res.length) html = '<option value="">Belum ada jadwal tersedia</option>';
                    $('#scheduleSelect').html(html);
                    updatePaymentPreview();
                });
                setTimeout(() => $('#applyModal').modal('show'), 300);
            });
            $('#scheduleSelect').change(function() {
                const s = schedules.find(x => String(x.id) === String(this.value));
                $('#schemeSelect').prop('disabled', !s);
                resetPaymentPreview();
                if (!s) {
                    $('#scheduleInfo').addClass('d-none').empty();
                    return;
                }
                $('#scheduleInfo').removeClass('d-none').html(
                    `<b>${s.days_remaining} hari menuju keberangkatan.</b> Pilihan cicilan otomatis disesuaikan dengan waktu yang tersedia.`
                );
                $('#schemeSelect').html('<option value="">Pilih skema pembayaran</option>' + s.available_schemes.map(
                    x => `<option value="${x.value}">${x.label}</option>`).join(''));
            });
            $('#schemeSelect').change(function() {
                const installment = this.value !== 'sekali_bayar' && this.value;
                $('#dpGroup').toggleClass('d-none', !installment);
                $('#dpGroup input').prop('required', !!installment);
                if (!installment) $('#dpGroup input').prop('checked', false);
                updatePaymentPreview();
            });
            $(document).on('change', 'input[name="dp_persen"]', updatePaymentPreview);
            $('#applyForm').submit(function(e) {
                e.preventDefault();
                const btn = $('#submitApply').prop('disabled', true).text('Memproses...');
                $.post('/keberangkatan-jemaah/store', $(this).serialize()).done(r => Swal.fire('Berhasil', r.message,
                    'success').then(() => location.href = r.redirect)).fail(x => Swal.fire('Pengajuan gagal', x
                    .responseJSON?.message || 'Periksa kembali pilihan Anda.', 'error')).always(() => btn.prop(
                    'disabled', false).text('Ajukan & Buat Rencana Pembayaran'));
            });

            $('#btnOpenReschedule').click(function() {
                $('#rescheduleOptions').html('<div class="text-muted">Memuat jadwal alternatif...</div>');
                $('#rescheduleTarget').val('');
                $('#submitReschedule').prop('disabled', true);
                $.get('/keberangkatan-jemaah/reschedule-options', res => {
                    if (!res.length) {
                        $('#rescheduleOptions').html(
                            '<div class="alert alert-info mb-0">Belum ada jadwal alternatif yang memenuhi syarat.</div>'
                            );
                        return;
                    }
                    $('#rescheduleOptions').html(res.map(item => `
                        <div class="reschedule-option" data-id="${item.id}">
                            <div class="d-flex justify-content-between flex-wrap">
                                <b>${safe(item.kode)} - ${safe(item.paket)}</b>
                                <span class="badge badge-light">${item.sisa_kuota} sisa kuota</span>
                            </div>
                            <small>${dateId(item.tanggal_keberangkatan)} - ${dateId(item.tanggal_pulang)}</small>
                            <small>Maskapai: ${safe(item.maskapai)}</small>
                        </div>
                    `).join(''));
                }).fail(x => {
                    $('#rescheduleOptions').html(
                        `<div class="alert alert-warning mb-0">${x.responseJSON?.message || 'Gagal memuat jadwal alternatif.'}</div>`
                        );
                });
                $('#rescheduleModal').modal('show');
            });

            $(document).on('click', '.reschedule-option', function() {
                $('.reschedule-option').removeClass('active');
                $(this).addClass('active');
                $('#rescheduleTarget').val($(this).data('id'));
                $('#submitReschedule').prop('disabled', false);
            });

            $('#rescheduleForm').submit(function(e) {
                e.preventDefault();
                const btn = $('#submitReschedule').prop('disabled', true).text('Mengirim...');
                $.post('/keberangkatan-jemaah/reschedule', $(this).serialize())
                    .done(r => Swal.fire('Berhasil', r.message, 'success').then(() => location.reload()))
                    .fail(x => Swal.fire('Gagal', x.responseJSON?.message ||
                        'Pengajuan perubahan tidak dapat diproses.', 'error'))
                    .always(() => btn.prop('disabled', false).text('Ajukan Perubahan'));
            });
        </script>
    @endpush
@endsection
