<div class="jadwal-detail">
    <h5>{{ $jadwal->keberangkatan->tanggal_keberangkatan->format('d F Y') }}</h5>
    <p><strong>Durasi:</strong>
        {{ $jadwal->keberangkatan->tanggal_pulang->diffInDays($jadwal->keberangkatan->tanggal_keberangkatan) + 1 }} Hari
    </p>

    <h6>Maskapai Berangkat</h6>
    <p>{{ $jadwal->keberangkatan->maskapaiBerangkat->nama ?? '-' }}<br>
        {{ $jadwal->keberangkatan->jam_berangkat }} - {{ $jadwal->keberangkatan->jam_tiba }}</p>

    <h6>Maskapai Pulang</h6>
    <p>{{ $jadwal->keberangkatan->maskapaiPulang->nama ?? '-' }}<br>
        {{ $jadwal->keberangkatan->jam_pulang }} - {{ $jadwal->keberangkatan->jam_tiba_pulang }}</p>

    <h6>Paket Umrah</h6>
    <p>{{ $jadwal->paketUmrah->nama_paket }}</p>

    <h6>Tour Leader</h6>
    <p>{{ $jadwal->keberangkatan->leader->nama ?? '-' }}</p>

    <span class="badge badge-success">{{ $jadwal->status }}</span>
</div>
