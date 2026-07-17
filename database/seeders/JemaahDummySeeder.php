<?php

namespace Database\Seeders;

use App\Models\DataJemaah;
use App\Models\DokumenJemaah;
use App\Models\Keberangkatan;
use App\Models\KeberangkatanJemaah;
use App\Models\KeberangkatanJemaahReschedule;
use App\Models\Pembayaran;
use App\Models\PembayaranTahapan;
use App\Models\PaketUmrah;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class JemaahDummySeeder extends Seeder
{
    private const DOCUMENT_TYPES = ['ktp', 'paspor', 'visa', 'vaksin', 'kartu_keluarga', 'foto_4x6'];

    public function run(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $operator = User::updateOrCreate(
            ['email' => 'operator@sawdeera.co.id'],
            [
                'name' => 'Operator Sawdeera',
                'password' => Hash::make('operator123'),
                'role' => 'operator',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]
        );

        $pakets = PaketUmrah::where('is_active', true)->orderBy('id')->get();
        $jadwals = Keberangkatan::whereIn('status', [
            Keberangkatan::STATUS_AKTIF,
            Keberangkatan::STATUS_DISETUJUI,
            Keberangkatan::STATUS_PENGAJUAN,
        ])->orderBy('tanggal_keberangkatan')->get();

        if ($pakets->isEmpty() || $jadwals->isEmpty()) {
            $this->command?->warn('Lewati JemaahDummySeeder: paket atau jadwal keberangkatan belum tersedia.');
            return;
        }

        $names = [
            'Ahmad Fauzan', 'Siti Aisyah', 'Budi Santoso', 'Dewi Lestari', 'Rizky Maulana',
            'Nurul Hidayah', 'Rina Kartika', 'Muhammad Iqbal', 'Halimah Tusadiah', 'Agus Pratama',
            'Yuni Marlina', 'Fajar Nugraha', 'Nadia Putri', 'Hendra Wijaya', 'Fitri Amalia',
            'Wahyu Triyono', 'Rani Aulia', 'Bagus Saputra', 'Maya Salsabila', 'Dian Permata',
        ];

        $scenarios = [
            ['user' => 'proses', 'data' => 'belum_lengkap', 'docs' => 0, 'pay' => 'none', 'kj' => null],
            ['user' => 'aktif', 'data' => 'menunggu_verifikasi', 'docs' => 1, 'pay' => 'none', 'kj' => 'pendaftaran'],
            ['user' => 'aktif', 'data' => 'terverifikasi', 'docs' => 3, 'pay' => 'belum_bayar', 'kj' => 'pendaftaran'],
            ['user' => 'aktif', 'data' => 'terverifikasi', 'docs' => 6, 'pay' => 'diproses', 'kj' => 'pendaftaran'],
            ['user' => 'aktif', 'data' => 'terverifikasi', 'docs' => 6, 'pay' => 'diverifikasi', 'kj' => 'setuju'],
            ['user' => 'aktif', 'data' => 'perlu_perbaikan', 'docs' => 2, 'pay' => 'none', 'kj' => 'pendaftaran'],
            ['user' => 'aktif', 'data' => 'terverifikasi', 'docs' => 6, 'pay' => 'ditolak', 'kj' => 'pendaftaran'],
            ['user' => 'aktif', 'data' => 'terverifikasi', 'docs' => 7, 'pay' => 'diverifikasi', 'kj' => 'setuju', 'married' => true],
            ['user' => 'aktif', 'data' => 'terverifikasi', 'docs' => 5, 'pay' => 'diproses', 'kj' => 'reschedule', 'reschedule' => true],
            ['user' => 'tidak_aktif', 'data' => 'belum_lengkap', 'docs' => 0, 'pay' => 'none', 'kj' => null],
            ['user' => 'aktif', 'data' => 'menunggu_verifikasi', 'docs' => 0, 'pay' => 'none', 'kj' => 'pendaftaran'],
            ['user' => 'aktif', 'data' => 'terverifikasi', 'docs' => 4, 'pay' => 'belum_bayar', 'kj' => 'pendaftaran'],
            ['user' => 'aktif', 'data' => 'terverifikasi', 'docs' => 6, 'pay' => 'diproses', 'kj' => 'pendaftaran'],
            ['user' => 'aktif', 'data' => 'terverifikasi', 'docs' => 6, 'pay' => 'diverifikasi', 'kj' => 'setuju'],
            ['user' => 'proses', 'data' => 'belum_lengkap', 'docs' => 0, 'pay' => 'none', 'kj' => null],
            ['user' => 'aktif', 'data' => 'perlu_perbaikan', 'docs' => 3, 'pay' => 'none', 'kj' => 'pendaftaran'],
            ['user' => 'aktif', 'data' => 'terverifikasi', 'docs' => 6, 'pay' => 'diverifikasi', 'kj' => 'setuju'],
            ['user' => 'aktif', 'data' => 'terverifikasi', 'docs' => 6, 'pay' => 'ditolak', 'kj' => 'pendaftaran'],
            ['user' => 'aktif', 'data' => 'menunggu_verifikasi', 'docs' => 2, 'pay' => 'belum_bayar', 'kj' => 'pendaftaran'],
            ['user' => 'aktif', 'data' => 'terverifikasi', 'docs' => 6, 'pay' => 'diverifikasi', 'kj' => 'setuju'],
        ];

        foreach ($names as $index => $name) {
            $number = $index + 1;
            $scenario = $scenarios[$index];
            $email = 'jemaah'.str_pad((string) $number, 2, '0', STR_PAD_LEFT).'@sawdeera.test';
            $married = $scenario['married'] ?? ($number % 3 === 0);

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('jemaah123'),
                    'role' => 'jemaah',
                    'status' => $scenario['user'],
                    'email_verified_at' => now(),
                ]
            );

            $jemaah = DataJemaah::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'operator_id' => $operator->id,
                    'nik' => '3273'.str_pad((string) $number, 12, '0', STR_PAD_LEFT),
                    'jenis_kelamin' => $number % 2 === 0 ? 'perempuan' : 'laki_laki',
                    'no_telepon' => '0812'.str_pad((string) (34560000 + $number), 8, '0', STR_PAD_LEFT),
                    'kontak_darurat' => 'Kontak '.$name,
                    'hubungan_kontak_darurat' => $married ? 'Pasangan' : 'Keluarga',
                    'tempat_lahir' => ['Bandung', 'Jakarta', 'Bekasi', 'Bogor'][$index % 4],
                    'tanggal_lahir' => now()->subYears(24 + ($index % 18))->subDays($index)->toDateString(),
                    'alamat' => 'Jl. Dummy Umrah No. '.$number.', Jawa Barat',
                    'pekerjaan' => ['Wiraswasta', 'Karyawan', 'Guru', 'Mahasiswa', 'Dokter'][$index % 5],
                    'status_pernikahan' => $married ? 'menikah' : 'belum_menikah',
                    'nomor_paspor' => 'P'.str_pad((string) $number, 8, '0', STR_PAD_LEFT),
                    'tanggal_terbit_paspor' => now()->subYears(1)->subDays($index)->toDateString(),
                    'tanggal_kedaluwarsa_paspor' => now()->addYears(4)->addDays($index)->toDateString(),
                    'tempat_penerbitan_paspor' => 'Imigrasi Bandung',
                    'scan_paspor' => $scenario['docs'] > 0 ? "dummy/jemaah/{$number}/scan_paspor.pdf" : null,
                    'golongan_darah' => ['A', 'B', 'AB', 'O'][$index % 4],
                    'riwayat_penyakit' => $index % 5 === 0 ? 'Asma ringan' : null,
                    'alergi' => $index % 4 === 0 ? 'Seafood' : null,
                    'foto_profil' => "dummy/jemaah/{$number}/foto_profil.png",
                    'status_data' => $scenario['data'],
                    'catatan_admin' => $scenario['data'] === 'perlu_perbaikan' ? 'Mohon perbaiki data paspor dan kontak darurat.' : null,
                    'diverifikasi_pada' => in_array($scenario['data'], ['terverifikasi', 'perlu_perbaikan'], true) ? now()->subDays(20 - min($index, 19)) : null,
                ]
            );

            $this->seedDocuments($jemaah, (int) $scenario['docs'], $married, $admin);

            if ($scenario['kj']) {
                $paket = $pakets[$index % $pakets->count()];
                $jadwal = $jadwals->firstWhere('paket_id', $paket->id) ?: $jadwals[$index % $jadwals->count()];
                $pengajuan = KeberangkatanJemaah::updateOrCreate(
                    ['jemaah_id' => $jemaah->id],
                    [
                        'keberangkatan_id' => $jadwal->id,
                        'paket_umrah_id' => $paket->id,
                        'status' => $scenario['kj'],
                    ]
                );

                if ($scenario['pay'] !== 'none') {
                    $this->seedPayment($pengajuan, $jemaah, $jadwal, $paket, $scenario['pay'], $admin, $index);
                }

                if (!empty($scenario['reschedule'])) {
                    $target = $jadwals->where('id', '!=', $jadwal->id)->firstWhere('paket_id', $paket->id) ?: $jadwals->where('id', '!=', $jadwal->id)->first();
                    if ($target) {
                        KeberangkatanJemaahReschedule::updateOrCreate(
                            ['keberangkatan_jemaah_id' => $pengajuan->id, 'status' => KeberangkatanJemaahReschedule::STATUS_MENUNGGU],
                            [
                                'jemaah_id' => $jemaah->id,
                                'keberangkatan_asal_id' => $jadwal->id,
                                'keberangkatan_tujuan_id' => $target->id,
                                'alasan_pengajuan' => 'Ada kendala keluarga, ingin pindah jadwal.',
                                'diajukan_pada' => now()->subDays(2),
                            ]
                        );
                    }
                }
            }
        }
    }

    private function seedDocuments(DataJemaah $jemaah, int $count, bool $married, User $admin): void
    {
        $types = self::DOCUMENT_TYPES;
        if ($married) {
            $types[] = 'buku_nikah';
        }

        foreach ($types as $index => $type) {
            if ($index >= $count) {
                DokumenJemaah::where('jemaah_id', $jemaah->id)->where('jenis_dokumen', $type)->delete();
                continue;
            }

            $status = match (true) {
                $index === 0 && $count <= 3 => 'diproses',
                $index === $count - 1 && $count >= 3 && $count < count($types) => 'diproses',
                $index === 1 && $count === 3 => 'ditolak',
                default => 'diverifikasi',
            };

            DokumenJemaah::updateOrCreate(
                ['jemaah_id' => $jemaah->id, 'jenis_dokumen' => $type],
                [
                    'file_path' => "dummy/dokumen/{$jemaah->id}/{$type}.pdf",
                    'status' => $status,
                    'keterangan_penolakan' => $status === 'ditolak' ? 'File kurang jelas, mohon upload ulang.' : null,
                    'verified_by' => $status === 'diproses' ? null : $admin->id,
                    'verified_at' => $status === 'diproses' ? null : now()->subDays(8),
                ]
            );
        }
    }

    private function seedPayment(KeberangkatanJemaah $pengajuan, DataJemaah $jemaah, Keberangkatan $jadwal, PaketUmrah $paket, string $status, User $admin, int $index): void
    {
        $steps = $index % 2 === 0 ? 3 : 1;
        $dpPercent = $steps === 1 ? null : 30;
        $payment = Pembayaran::updateOrCreate(
            ['keberangkatan_jemaah_id' => $pengajuan->id],
            [
                'jemaah_id' => $jemaah->id,
                'keberangkatan_id' => $jadwal->id,
                'total_tagihan' => $paket->harga,
                'jumlah' => null,
                'jenis_pembayaran' => $steps === 1 ? 'sekali_bayar' : 'cicilan_3x',
                'dp_persen' => $dpPercent,
                'jumlah_tahap' => $steps,
                'status' => $status,
                'status_rencana' => 'aktif',
                'keterangan_penolakan' => $status === 'ditolak' ? 'Bukti pembayaran tidak terbaca.' : null,
                'verified_by' => in_array($status, ['diverifikasi', 'ditolak'], true) ? $admin->id : null,
                'verified_at' => in_array($status, ['diverifikasi', 'ditolak'], true) ? now()->subDays(3) : null,
            ]
        );

        PembayaranTahapan::where('pembayaran_id', $payment->id)->delete();
        $percentages = $steps === 1 ? [100] : [$dpPercent, 35, 35];
        foreach ($percentages as $stepIndex => $percentage) {
            $stepStatus = match ($status) {
                'belum_bayar' => 'belum_bayar',
                'diproses' => $stepIndex === 0 ? 'diproses' : 'belum_bayar',
                'ditolak' => $stepIndex === 0 ? 'ditolak' : 'belum_bayar',
                'diverifikasi' => $steps === 1 || $stepIndex <= 1 ? 'diverifikasi' : 'belum_bayar',
                default => 'belum_bayar',
            };

            PembayaranTahapan::create([
                'pembayaran_id' => $payment->id,
                'urutan' => $stepIndex + 1,
                'nama_tahap' => $steps === 1 ? 'Pembayaran Penuh' : 'Tahap '.($stepIndex + 1),
                'persentase' => $percentage,
                'nominal' => round(((float) $paket->harga * $percentage) / 100),
                'jatuh_tempo' => now()->addDays(7 + ($stepIndex * 14))->toDateString(),
                'metode_pembayaran' => in_array($stepStatus, ['diproses', 'diverifikasi', 'ditolak'], true) ? 'Transfer Bank' : null,
                'bukti_pembayaran' => in_array($stepStatus, ['diproses', 'diverifikasi', 'ditolak'], true) ? "dummy/pembayaran/{$payment->id}/tahap-".($stepIndex + 1).'.jpg' : null,
                'catatan_jemaah' => in_array($stepStatus, ['diproses', 'diverifikasi', 'ditolak'], true) ? 'Pembayaran dummy untuk testing.' : null,
                'status' => $stepStatus,
                'keterangan_penolakan' => $stepStatus === 'ditolak' ? 'Nominal tidak sesuai.' : null,
                'verified_by' => in_array($stepStatus, ['diverifikasi', 'ditolak'], true) ? $admin->id : null,
                'uploaded_at' => in_array($stepStatus, ['diproses', 'diverifikasi', 'ditolak'], true) ? now()->subDays(4) : null,
                'verified_at' => in_array($stepStatus, ['diverifikasi', 'ditolak'], true) ? now()->subDays(2) : null,
            ]);
        }
    }
}
