<?php

namespace Database\Seeders;

use App\Models\TourLeader;
use Illuminate\Database\Seeder;

class TourLeaderSeeder extends Seeder
{
    public function run(): void
    {
        $leaders = [
            [
                'nama' => 'Ustadz Ahmad Fauzi',
                'no_telepon' => '081234567801',
                'email' => 'ahmad.fauzi@sawdeera.co.id',
                'alamat' => 'Jakarta Selatan',
                'jenis_kelamin' => 'laki_laki',
            ],
            [
                'nama' => 'Ustadzah Nur Aisyah',
                'no_telepon' => '081234567802',
                'email' => 'nur.aisyah@sawdeera.co.id',
                'alamat' => 'Depok',
                'jenis_kelamin' => 'perempuan',
            ],
            [
                'nama' => 'Ustadz Ridwan Hakim',
                'no_telepon' => '081234567803',
                'email' => 'ridwan.hakim@sawdeera.co.id',
                'alamat' => 'Bekasi',
                'jenis_kelamin' => 'laki_laki',
            ],
            [
                'nama' => 'Ustadzah Salma Zahra',
                'no_telepon' => '081234567804',
                'email' => 'salma.zahra@sawdeera.co.id',
                'alamat' => 'Tangerang',
                'jenis_kelamin' => 'perempuan',
            ],
        ];

        foreach ($leaders as $leader) {
            TourLeader::updateOrCreate(
                ['email' => $leader['email']],
                $leader
            );
        }
    }
}
