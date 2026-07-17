<?php

namespace Database\Seeders;

use App\Models\Maskapai;
use Illuminate\Database\Seeder;

class MaskapaiSeeder extends Seeder
{
    public function run(): void
    {
        $maskapais = [
            ['airline_code' => 'SV', 'airline_icao_code' => 'SVA', 'nama' => 'Saudia Airlines', 'asal_negara' => 'Saudi Arabia', 'is_active' => true],
            ['airline_code' => 'GA', 'airline_icao_code' => 'GIA', 'nama' => 'Garuda Indonesia', 'asal_negara' => 'Indonesia', 'is_active' => true],
            ['airline_code' => 'QR', 'airline_icao_code' => 'QTR', 'nama' => 'Qatar Airways', 'asal_negara' => 'Qatar', 'is_active' => true],
            ['airline_code' => 'EK', 'airline_icao_code' => 'UAE', 'nama' => 'Emirates', 'asal_negara' => 'United Arab Emirates', 'is_active' => true],
            ['airline_code' => 'EY', 'airline_icao_code' => 'ETD', 'nama' => 'Etihad Airways', 'asal_negara' => 'United Arab Emirates', 'is_active' => true],
        ];

        foreach ($maskapais as $maskapai) {
            Maskapai::updateOrCreate(
                ['airline_code' => $maskapai['airline_code']],
                $maskapai
            );
        }
    }
}
