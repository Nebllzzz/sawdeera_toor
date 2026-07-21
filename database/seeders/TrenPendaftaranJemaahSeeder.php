<?php

namespace Database\Seeders;

use App\Models\DataJemaah;
use App\Models\Hotel;
use App\Models\Keberangkatan;
use App\Models\KeberangkatanJemaah;
use App\Models\Maskapai;
use App\Models\PaketFasilitas;
use App\Models\PaketProgram;
use App\Models\PaketUmrah;
use App\Models\TourLeader;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TrenPendaftaranJemaahSeeder extends Seeder
{
    private const START_MONTH = '2026-02-01';

    private const PACKAGE_NAME = '[Demo Tren] Umrah Harmoni 11 Hari';

    private const SCHEDULE_MARKER = 'Jadwal khusus data demo Tren Pendaftaran Jemaah.';

    /** @var array<int, int> */
    private const MONTHLY_TOTAL_PATTERN = [8, 12, 10, 16, 14, 18];

    public function run(): void
    {
        $summary = DB::transaction(function (): array {
            $package = $this->createPackage();
            $schedule = $this->createSchedule($package);
            $password = Hash::make('trend2026');
            $totalJemaah = 0;
            $totalMonths = 0;

            foreach ($this->registrationMonths() as $month) {
                $totalMonths++;
                $monthlyTotal = $this->monthlyTotal($month);

                for ($sequence = 1; $sequence <= $monthlyTotal; $sequence++) {
                    $this->createRegistration(
                        $schedule,
                        $package,
                        $month,
                        $sequence,
                        $totalJemaah + $sequence,
                        $password,
                    );
                }

                $totalJemaah += $monthlyTotal;
            }

            return [
                'package' => $package->nama_paket,
                'schedule' => $schedule->tanggal_keberangkatan->format('d-m-Y'),
                'months' => $totalMonths,
                'jemaah' => $totalJemaah,
            ];
        });

        $this->command?->info(sprintf(
            'Seeder tren selesai: %d jemaah dalam %d bulan, paket "%s", keberangkatan %s.',
            $summary['jemaah'],
            $summary['months'],
            $summary['package'],
            $summary['schedule'],
        ));
    }

    private function createPackage(): PaketUmrah
    {
        $makkahHotel = Hotel::updateOrCreate(
            [
                'nama' => '[Demo Tren] Safwah Makkah Hotel',
                'lokasi' => 'makkah',
            ],
            [
                'bintang' => 4,
                'tipe_kamar' => 'quad',
            ],
        );

        $madinahHotel = Hotel::updateOrCreate(
            [
                'nama' => '[Demo Tren] Andalus Madinah Hotel',
                'lokasi' => 'madinah',
            ],
            [
                'bintang' => 4,
                'tipe_kamar' => 'quad',
            ],
        );

        $package = PaketUmrah::updateOrCreate(
            ['nama_paket' => self::PACKAGE_NAME],
            [
                'durasi' => 11,
                'hotel_makkah_id' => $makkahHotel->id,
                'hotel_madinah_id' => $madinahHotel->id,
                'harga' => 32500000,
                'deskripsi' => 'Paket mandiri untuk mendukung data demo chart Tren Pendaftaran Jemaah.',
                'is_active' => true,
            ],
        );

        foreach ([
            'Visa umrah',
            'Tiket pesawat pulang-pergi',
            'Hotel Makkah dan Madinah',
            'Makan tiga kali sehari',
            'Bus AC dan perlengkapan umrah',
            'Pembimbing ibadah',
        ] as $facility) {
            PaketFasilitas::updateOrCreate([
                'paket_id' => $package->id,
                'nama' => $facility,
            ]);
        }

        for ($day = 1; $day <= $package->durasi; $day++) {
            PaketProgram::updateOrCreate(
                [
                    'paket_id' => $package->id,
                    'hari' => $day,
                ],
                ['deskripsi' => $this->programDescription($day, $package->durasi)],
            );
        }

        return $package;
    }

    private function createSchedule(PaketUmrah $package): Keberangkatan
    {
        $airline = Maskapai::updateOrCreate(
            ['airline_code' => 'TR26'],
            [
                'airline_icao_code' => 'TRD',
                'nama' => '[Demo Tren] Nusantara Air',
                'asal_negara' => 'Indonesia',
                'is_active' => true,
            ],
        );

        $leader = TourLeader::updateOrCreate(
            ['email' => 'tour.leader.trend.2026@sawdeera.test'],
            [
                'nama' => 'Ustadz Rahman Hadi (Demo Tren)',
                'no_telepon' => '081390002026',
                'alamat' => 'Jakarta',
                'jenis_kelamin' => 'laki_laki',
            ],
        );

        $departureDate = now()
            ->addMonthsNoOverflow(4)
            ->startOfMonth()
            ->addDays(7)
            ->startOfDay();
        $returnDate = $departureDate->copy()->addDays($package->durasi - 1);
        $adminId = User::whereIn('role', ['admin', 'operator'])->oldest('id')->value('id');

        return Keberangkatan::updateOrCreate(
            [
                'paket_id' => $package->id,
                'keterangan' => self::SCHEDULE_MARKER,
            ],
            [
                'maskapai_berangkat_id' => $airline->id,
                'maskapai_pulang_id' => $airline->id,
                'tour_leader_id' => $leader->id,
                'kuota' => 150,
                'tanggal_keberangkatan' => $departureDate->toDateString(),
                'jam_berangkat' => '08:00:00',
                'jam_tiba' => '15:30:00',
                'tanggal_pulang' => $returnDate->toDateString(),
                'jam_pulang' => '18:30:00',
                'jam_tiba_pulang' => '08:00:00',
                'status' => Keberangkatan::STATUS_AKTIF,
                'alasan_revisi' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
        );
    }

    private function createRegistration(
        Keberangkatan $schedule,
        PaketUmrah $package,
        Carbon $month,
        int $sequence,
        int $globalSequence,
        string $password,
    ): void {
        $monthKey = $month->format('Ym');
        $createdAt = $month->copy()
            ->day(4 + (($sequence * 3) % 22))
            ->setTime(8 + ($sequence % 9), ($sequence * 7) % 60);
        $statusData = $this->jemaahStatus($globalSequence);
        $name = $this->jemaahName($globalSequence);
        $email = sprintf(
            'trend.pendaftaran.%s.%02d@sawdeera.test',
            $monthKey,
            $sequence,
        );

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'role' => 'jemaah',
                'status' => in_array($statusData, ['terverifikasi', 'perlu_perbaikan'], true)
                    ? 'aktif'
                    : 'proses',
                'email_verified_at' => $statusData === 'terverifikasi' ? $createdAt->copy() : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDay(),
            ],
        );

        $jemaah = DataJemaah::updateOrCreate(
            ['user_id' => $user->id],
            [
                'operator_id' => null,
                'nik' => sprintf('990001%s%06d', $month->format('ym'), $sequence),
                'jenis_kelamin' => $globalSequence % 2 === 0 ? 'perempuan' : 'laki_laki',
                'no_telepon' => sprintf('08139%s%04d', $month->format('ym'), $sequence),
                'kontak_darurat' => sprintf('08219%s%04d', $month->format('ym'), $sequence),
                'hubungan_kontak_darurat' => 'Keluarga',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => Carbon::create(1975 + ($globalSequence % 25), 1 + ($globalSequence % 12), 10)
                    ->toDateString(),
                'alamat' => 'Alamat khusus data demo tren pendaftaran',
                'pekerjaan' => 'Wiraswasta',
                'status_pernikahan' => $globalSequence % 3 === 0 ? 'menikah' : 'belum_menikah',
                'nomor_paspor' => sprintf('TR%s%05d', $month->format('ym'), $sequence),
                'tanggal_terbit_paspor' => $createdAt->copy()->subYears(2)->toDateString(),
                'tanggal_kedaluwarsa_paspor' => $createdAt->copy()->addYears(3)->toDateString(),
                'tempat_penerbitan_paspor' => 'Bandung',
                'golongan_darah' => ['A', 'B', 'AB', 'O'][$globalSequence % 4],
                'status_data' => $statusData,
                'catatan_admin' => 'Data demo khusus chart Tren Pendaftaran Jemaah.',
                'diverifikasi_pada' => $statusData === 'terverifikasi'
                    ? $createdAt->copy()->addDays(2)
                    : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(2),
            ],
        );

        KeberangkatanJemaah::updateOrCreate(
            [
                'keberangkatan_id' => $schedule->id,
                'jemaah_id' => $jemaah->id,
            ],
            [
                'paket_umrah_id' => $package->id,
                'status' => KeberangkatanJemaah::STATUS_PENDAFTARAN,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDay(),
            ],
        );
    }

    /** @return iterable<int, Carbon> */
    private function registrationMonths(): iterable
    {
        $month = Carbon::parse(self::START_MONTH)->startOfMonth();
        $lastMonth = now()->startOfMonth();

        while ($month->lessThanOrEqualTo($lastMonth)) {
            yield $month->copy();
            $month->addMonth();
        }
    }

    private function monthlyTotal(Carbon $month): int
    {
        $offset = (int) Carbon::parse(self::START_MONTH)->diffInMonths($month);

        return self::MONTHLY_TOTAL_PATTERN[$offset % count(self::MONTHLY_TOTAL_PATTERN)];
    }

    private function jemaahStatus(int $sequence): string
    {
        return match ($sequence % 5) {
            0, 1 => 'terverifikasi',
            2 => 'menunggu_verifikasi',
            3 => 'perlu_perbaikan',
            default => 'belum_lengkap',
        };
    }

    private function jemaahName(int $sequence): string
    {
        $firstNames = ['Ahmad', 'Siti', 'Muhammad', 'Aisyah', 'Rizki', 'Nurul', 'Fajar', 'Dewi'];
        $lastNames = ['Hidayat', 'Rahmawati', 'Pratama', 'Lestari', 'Maulana', 'Kurnia', 'Hakim', 'Zahra'];

        return sprintf(
            '%s %s (Tren %03d)',
            $firstNames[($sequence - 1) % count($firstNames)],
            $lastNames[(int) floor(($sequence - 1) / count($firstNames)) % count($lastNames)],
            $sequence,
        );
    }

    private function programDescription(int $day, int $duration): string
    {
        return match (true) {
            $day === 1 => 'Keberangkatan dari Indonesia menuju Arab Saudi.',
            $day === 2 => 'Tiba di Madinah dan persiapan ibadah.',
            $day === 3 => 'Ziarah dan ibadah di Masjid Nabawi.',
            $day === 5 => 'Perjalanan menuju Makkah dan pelaksanaan umrah.',
            $day === $duration - 1 => 'Persiapan kepulangan jemaah.',
            $day === $duration => 'Kepulangan menuju Indonesia.',
            default => 'Ibadah, kajian, dan ziarah sesuai jadwal perjalanan.',
        };
    }
}
