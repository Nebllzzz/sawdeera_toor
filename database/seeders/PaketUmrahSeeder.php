<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\PaketFasilitas;
use App\Models\PaketProgram;
use App\Models\PaketUmrah;
use Illuminate\Database\Seeder;

class PaketUmrahSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'nama_paket' => 'Umrah Reguler 9 Hari',
                'durasi' => 9,
                'makkah' => 'Emaar Grand Makkah',
                'madinah' => 'Dallah Taibah Hotel',
                'harga' => 28500000,
                'deskripsi' => 'Paket umrah reguler hemat dengan hotel nyaman dan itinerary ibadah utama.',
            ],
            [
                'nama_paket' => 'Umrah Plus Thaif 10 Hari',
                'durasi' => 10,
                'makkah' => 'Anjum Hotel Makkah',
                'madinah' => 'Anwar Al Madinah Movenpick',
                'harga' => 33500000,
                'deskripsi' => 'Paket umrah dengan tambahan city tour Thaif dan pendampingan pembimbing berpengalaman.',
            ],
            [
                'nama_paket' => 'Umrah VIP 12 Hari',
                'durasi' => 12,
                'makkah' => 'Swissotel Al Maqam Makkah',
                'madinah' => 'Madinah Hilton',
                'harga' => 44500000,
                'deskripsi' => 'Paket VIP dengan hotel dekat Masjidil Haram dan Masjid Nabawi.',
            ],
            [
                'nama_paket' => 'Umrah Keluarga 14 Hari',
                'durasi' => 14,
                'makkah' => 'Makkah Towers',
                'madinah' => 'Pullman Zamzam Madinah',
                'harga' => 51500000,
                'deskripsi' => 'Paket keluarga dengan durasi lebih panjang dan ritme perjalanan lebih santai.',
            ],
        ];

        foreach ($packages as $package) {
            $makkah = Hotel::where('nama', $package['makkah'])->firstOrFail();
            $madinah = Hotel::where('nama', $package['madinah'])->firstOrFail();

            $paket = PaketUmrah::updateOrCreate(
                ['nama_paket' => $package['nama_paket']],
                [
                    'durasi' => $package['durasi'],
                    'hotel_makkah_id' => $makkah->id,
                    'hotel_madinah_id' => $madinah->id,
                    'harga' => $package['harga'],
                    'deskripsi' => $package['deskripsi'],
                    'is_active' => true,
                ]
            );

            $this->syncFacilities($paket);
            $this->syncPrograms($paket);
        }
    }

    private function syncFacilities(PaketUmrah $paket): void
    {
        $facilities = [
            'Visa umrah',
            'Tiket pesawat PP',
            'Hotel Makkah dan Madinah',
            'Makan 3 kali sehari',
            'Bus AC selama perjalanan',
            'Air zamzam 5 liter',
            'Perlengkapan umrah',
            'Pembimbing ibadah',
        ];

        PaketFasilitas::where('paket_id', $paket->id)->delete();
        foreach ($facilities as $facility) {
            PaketFasilitas::create([
                'paket_id' => $paket->id,
                'nama' => $facility,
            ]);
        }
    }

    private function syncPrograms(PaketUmrah $paket): void
    {
        PaketProgram::where('paket_id', $paket->id)->delete();

        for ($day = 1; $day <= $paket->durasi; $day++) {
            $description = match (true) {
                $day === 1 => 'Keberangkatan dari Indonesia menuju Arab Saudi.',
                $day === 2 => 'Tiba dan persiapan pelaksanaan umrah.',
                $day === 3 => 'Pelaksanaan umrah wajib dan ibadah di Masjidil Haram.',
                $day === $paket->durasi - 1 => 'Persiapan kepulangan dan ziarah sekitar kota.',
                $day === $paket->durasi => 'Kepulangan menuju Indonesia.',
                default => 'Ibadah mandiri, kajian, dan ziarah sesuai jadwal perjalanan.',
            };

            PaketProgram::create([
                'paket_id' => $paket->id,
                'hari' => $day,
                'deskripsi' => $description,
            ]);
        }
    }
}
