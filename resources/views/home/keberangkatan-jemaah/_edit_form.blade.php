<div class="mb-3">
    <label>Paket Umrah</label>
    <select name="paket_umrah_id" id="editPaketSelect" class="form-control" required>
        <option value="">Pilih Paket</option>
        @foreach (\App\Models\PaketUmrah::all() as $paket)
            <option value="{{ $paket->id }}" data-durasi="{{ $paket->durasi }}"
                {{ $jadwal->paket_umrah_id == $paket->id ? 'selected' : '' }}>
                {{ $paket->nama_paket }} ({{ $paket->durasi }} Hari)
            </option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label>Jadwal Keberangkatan</label>
    <select name="keberangkatan_id" id="editJadwalSelect" class="form-control" required>
        <option value="">Pilih paket dulu untuk melihat jadwal</option>
    </select>
</div>

<script>
    $(document).ready(function() {
        // Load jadwal saat halaman pertama kali dimuat
        const initialPaketId = $('#editPaketSelect').val();
        if (initialPaketId) {
            const initialDurasi = $('#editPaketSelect').find('option:selected').data('durasi');
            loadEditJadwal(initialPaketId, initialDurasi);
        }

        // Saat paket berubah
        $('#editPaketSelect').on('change', function() {
            const paketId = $(this).val();
            const durasi = $(this).find('option:selected').data('durasi');

            if (paketId && durasi) {
                loadEditJadwal(paketId, durasi);
            } else {
                $('#editJadwalSelect').html(
                    '<option value="">Pilih paket dulu untuk melihat jadwal</option>');
            }
        });

        function loadEditJadwal(paketId, durasi) {
            $.ajax({
                url: `/keberangkatan-jemaah/jadwal-paket/${paketId}/${durasi}`,
                type: 'GET',
                success: function(jadwals) {
                    let options = '<option value="">Pilih Jadwal</option>';
                    const currentJadwalId = '{{ $jadwal->keberangkatan_id }}';

                    jadwals.forEach(function(jadwal) {
                        let berangkat = new Date(jadwal.tanggal_keberangkatan)
                            .toLocaleDateString('id-ID');
                        let pulang = new Date(jadwal.tanggal_pulang).toLocaleDateString(
                            'id-ID');
                        let maskapaiBer = jadwal.maskapai_berangkat ? jadwal
                            .maskapai_berangkat.nama : 'N/A';
                        let maskapaiPul = jadwal.maskapai_pulang ? jadwal.maskapai_pulang
                            .nama : 'N/A';

                        let selected = (jadwal.id == currentJadwalId) ? 'selected' : '';

                        options += `<option value="${jadwal.id}" ${selected}>
                            (${berangkat} - ${maskapaiBer}) - (${pulang} - ${maskapaiPul})
                        </option>`;
                    });

                    if (jadwals.length === 0) {
                        options = '<option value="">Tidak ada jadwal tersedia</option>';
                    }

                    $('#editJadwalSelect').html(options);
                },
                error: function() {
                    Swal.fire('Error', 'Gagal memuat jadwal', 'error');
                }
            });
        }
    });
</script>
