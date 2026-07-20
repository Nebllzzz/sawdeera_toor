<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Itinerary {{ $pengajuan->keberangkatan->kode_keberangkatan }}</title>
    <style>
        @page { margin: 28px 34px; }
        body { color: #2d241b; font-family: DejaVu Sans, sans-serif; font-size: 11px; line-height: 1.5; }
        .header { border-bottom: 3px solid #9a671d; margin-bottom: 20px; padding-bottom: 14px; }
        .header table, .facts, .program { border-collapse: collapse; width: 100%; }
        .logo { height: 54px; }
        h1 { color: #5c3a16; font-size: 22px; margin: 0; text-align: right; }
        h2 { border-bottom: 1px solid #d8c8b0; color: #5c3a16; font-size: 14px; margin: 20px 0 10px; padding-bottom: 5px; }
        .subtitle { color: #7b6a5c; margin: 3px 0 0; text-align: right; }
        .identity { background: #fbf5eb; border: 1px solid #eadbc3; padding: 12px; }
        .identity strong { color: #5c3a16; font-size: 16px; }
        .facts td { border: 1px solid #e6ddd2; padding: 8px; vertical-align: top; width: 50%; }
        .facts small { color: #7d7167; display: block; }
        .facts b { display: block; margin-top: 2px; }
        .chips span { background: #f5ead8; border: 1px solid #e4cfac; border-radius: 10px; display: inline-block; margin: 2px; padding: 4px 8px; }
        .program th, .program td { border: 1px solid #e6ddd2; padding: 8px; text-align: left; vertical-align: top; }
        .program th { background: #f7f1e7; color: #5c3a16; }
        .notes { background: #fff8e9; border: 1px solid #eed7aa; margin-top: 20px; padding: 12px; }
        .notes h2 { border: 0; margin: 0 0 6px; padding: 0; }
        .notes ul { margin: 0; padding-left: 18px; }
        .footer { color: #8a7c70; font-size: 9px; margin-top: 24px; text-align: center; }
    </style>
</head>
<body>
    @php
        $schedule = $pengajuan->keberangkatan;
        $package = $pengajuan->paketUmrah;
    @endphp
    <div class="header">
        <table>
            <tr>
                <td><img class="logo" src="{{ public_path('img/logo.png') }}" alt="Sawdeera Toor"></td>
                <td>
                    <h1>ITINERARY UMRAH</h1>
                    <p class="subtitle">{{ $schedule->kode_keberangkatan }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="identity">
        <small>Jemaah</small><br>
        <strong>{{ $user->name }}</strong><br>
        {{ $user->email }} · {{ $user->jemaah?->no_telepon ?? '-' }}
    </div>

    <h2>Informasi Paket dan Jadwal</h2>
    <table class="facts">
        <tr><td><small>Nama Paket</small><b>{{ $package->nama_paket }}</b></td><td><small>Durasi</small><b>{{ $package->durasi }} Hari</b></td></tr>
        <tr><td><small>Tanggal Berangkat</small><b>{{ $schedule->tanggal_keberangkatan->translatedFormat('d F Y') }}</b></td><td><small>Tanggal Pulang</small><b>{{ $schedule->tanggal_pulang->translatedFormat('d F Y') }}</b></td></tr>
        <tr><td><small>Jam Berangkat / Tiba</small><b>{{ substr((string) $schedule->jam_berangkat, 0, 5) }} / {{ substr((string) $schedule->jam_tiba, 0, 5) }}</b></td><td><small>Jam Pulang / Tiba</small><b>{{ substr((string) $schedule->jam_pulang, 0, 5) }} / {{ substr((string) $schedule->jam_tiba_pulang, 0, 5) }}</b></td></tr>
        <tr><td><small>Maskapai Berangkat</small><b>{{ $schedule->maskapaiBerangkat?->nama ?? '-' }}</b></td><td><small>Maskapai Pulang</small><b>{{ $schedule->maskapaiPulang?->nama ?? '-' }}</b></td></tr>
        <tr><td><small>Hotel Makkah</small><b>{{ $package->hotelMakkah?->nama ?? '-' }}</b></td><td><small>Hotel Madinah</small><b>{{ $package->hotelMadinah?->nama ?? '-' }}</b></td></tr>
        <tr><td><small>Tour Leader</small><b>{{ $schedule->leader?->nama ?? 'Belum ditentukan' }}</b></td><td><small>Total Paket</small><b>Rp {{ number_format($package->harga, 0, ',', '.') }}</b></td></tr>
    </table>

    <h2>Fasilitas Paket</h2>
    <div class="chips">
        @forelse($package->fasilitas as $facility)
            <span>{{ $facility->nama }}</span>
        @empty
            <span>Rincian fasilitas belum tersedia.</span>
        @endforelse
    </div>

    <h2>Program Perjalanan</h2>
    <table class="program">
        <thead><tr><th style="width:75px">Hari</th><th>Kegiatan</th></tr></thead>
        <tbody>
            @forelse($package->program->sortBy('hari') as $program)
                <tr><td>Hari {{ $program->hari }}</td><td>{{ $program->deskripsi }}</td></tr>
            @empty
                <tr><td colspan="2">Rincian program perjalanan belum tersedia.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="notes">
        <h2>Notes</h2>
        <ul>
            <li>Batas pengajuan perubahan minimal H-45 sebelum berangkat.</li>
            <li>Harap hadir di titik keberangkatan minimal 3 jam sebelum jadwal.</li>
            <li>Pastikan identitas, dokumen perjalanan, dan pembayaran telah terverifikasi.</li>
        </ul>
    </div>

    <div class="footer">Dokumen dibuat oleh sistem Sawdeera Toor pada {{ now()->translatedFormat('d F Y H:i') }} WIB.</div>
</body>
</html>
