<?php

namespace Database\Seeders;

use App\Models\Hotel;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $hotels = [
            ['nama' => 'Makkah Towers', 'lokasi' => 'makkah', 'bintang' => 5, 'tipe_kamar' => 'quad'],
            ['nama' => 'Swissotel Al Maqam Makkah', 'lokasi' => 'makkah', 'bintang' => 5, 'tipe_kamar' => 'triple'],
            ['nama' => 'Anjum Hotel Makkah', 'lokasi' => 'makkah', 'bintang' => 5, 'tipe_kamar' => 'double'],
            ['nama' => 'Emaar Grand Makkah', 'lokasi' => 'makkah', 'bintang' => 4, 'tipe_kamar' => 'quad'],
            ['nama' => 'Pullman Zamzam Madinah', 'lokasi' => 'madinah', 'bintang' => 5, 'tipe_kamar' => 'quad'],
            ['nama' => 'Anwar Al Madinah Movenpick', 'lokasi' => 'madinah', 'bintang' => 5, 'tipe_kamar' => 'triple'],
            ['nama' => 'Madinah Hilton', 'lokasi' => 'madinah', 'bintang' => 5, 'tipe_kamar' => 'double'],
            ['nama' => 'Dallah Taibah Hotel', 'lokasi' => 'madinah', 'bintang' => 4, 'tipe_kamar' => 'quad'],
        ];

        foreach ($hotels as $hotel) {
            Hotel::updateOrCreate(
                ['nama' => $hotel['nama'], 'lokasi' => $hotel['lokasi']],
                $hotel
            );
        }
    }
}
