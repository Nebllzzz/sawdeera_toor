<?php

namespace Database\Seeders;

use App\Models\Keberangkatan;
use App\Models\Maskapai;
use App\Models\PaketUmrah;
use App\Models\TourLeader;
use App\Models\User;
use Illuminate\Database\Seeder;

class KeberangkatanSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('email', 'admin@sawdeera.co.id')->value('id');

        $schedules = [
            [
                'paket' => 'Umrah Reguler 9 Hari',
                'berangkat' => now()->addDays(15),
                'kuota' => 40,
                'status' => Keberangkatan::STATUS_AKTIF,
                'maskapai_berangkat' => 'Garuda Indonesia',
                'maskapai_pulang' => 'Saudia Airlines',
                'leader' => 'Ustadz Ahmad Fauzi',
                'keterangan' => 'Jadwal dekat, cocok untuk testing batas reschedule H-45.',
            ],
            [
                'paket' => 'Umrah Plus Thaif 10 Hari',
                'berangkat' => now()->addDays(40),
                'kuota' => 45,
                'status' => Keberangkatan::STATUS_AKTIF,
                'maskapai_berangkat' => 'Saudia Airlines',
                'maskapai_pulang' => 'Garuda Indonesia',
                'leader' => 'Ustadzah Nur Aisyah',
                'keterangan' => 'Jadwal tinggal 40 hari lagi.',
            ],
            [
                'paket' => 'Umrah VIP 12 Hari',
                'berangkat' => now()->addMonthsNoOverflow(3),
                'kuota' => 30,
                'status' => Keberangkatan::STATUS_DISETUJUI,
                'maskapai_berangkat' => 'Qatar Airways',
                'maskapai_pulang' => 'Qatar Airways',
                'leader' => 'Ustadz Ridwan Hakim',
                'keterangan' => 'Jadwal sekitar 3 bulan lagi.',
            ],
            [
                'paket' => 'Umrah Keluarga 14 Hari',
                'berangkat' => now()->addMonthsNoOverflow(6),
                'kuota' => 35,
                'status' => Keberangkatan::STATUS_PENGAJUAN,
                'maskapai_berangkat' => 'Emirates',
                'maskapai_pulang' => 'Emirates',
                'leader' => 'Ustadzah Salma Zahra',
                'keterangan' => 'Jadwal 6 bulan lagi, contoh status menunggu approval.',
            ],
            [
                'paket' => 'Umrah Reguler 9 Hari',
                'berangkat' => now()->addMonthsNoOverflow(12),
                'kuota' => 50,
                'status' => Keberangkatan::STATUS_DRAFT,
                'maskapai_berangkat' => 'Etihad Airways',
                'maskapai_pulang' => 'Etihad Airways',
                'leader' => 'Ustadz Ahmad Fauzi',
                'keterangan' => 'Jadwal 12 bulan lagi, masih draft.',
            ],
            [
                'paket' => 'Umrah Plus Thaif 10 Hari',
                'berangkat' => now()->addMonthsNoOverflow(12)->addDays(14),
                'kuota' => 45,
                'status' => Keberangkatan::STATUS_AKTIF,
                'maskapai_berangkat' => 'Garuda Indonesia',
                'maskapai_pulang' => 'Saudia Airlines',
                'leader' => 'Ustadzah Nur Aisyah',
                'keterangan' => 'Jadwal alternatif untuk uji reschedule paket yang sama.',
            ],
        ];

        foreach ($schedules as $schedule) {
            $paket = PaketUmrah::where('nama_paket', $schedule['paket'])->firstOrFail();
            $berangkat = $schedule['berangkat']->copy()->startOfDay();
            $pulang = $berangkat->copy()->addDays($paket->durasi - 1);

            Keberangkatan::updateOrCreate(
                [
                    'paket_id' => $paket->id,
                    'tanggal_keberangkatan' => $berangkat->toDateString(),
                ],
                [
                    'maskapai_berangkat_id' => Maskapai::where('nama', $schedule['maskapai_berangkat'])->value('id'),
                    'maskapai_pulang_id' => Maskapai::where('nama', $schedule['maskapai_pulang'])->value('id'),
                    'tour_leader_id' => TourLeader::where('nama', $schedule['leader'])->value('id'),
                    'kuota' => $schedule['kuota'],
                    'tanggal_keberangkatan' => $berangkat->toDateString(),
                    'jam_berangkat' => '08:30:00',
                    'jam_tiba' => '14:15:00',
                    'tanggal_pulang' => $pulang->toDateString(),
                    'jam_pulang' => '17:45:00',
                    'jam_tiba_pulang' => '07:10:00',
                    'status' => $schedule['status'],
                    'alasan_revisi' => null,
                    'keterangan' => $schedule['keterangan'],
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );
        }
    }
}
