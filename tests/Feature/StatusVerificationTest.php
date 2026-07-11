<?php

namespace Tests\Feature;

use App\Models\DataJemaah;
use App\Models\KeberangkatanJemaah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StatusVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_package_is_verified_when_jemaah_has_departure_application(): void
    {
        $user = User::factory()->create(['role' => 'jemaah', 'status' => 'aktif']);
        $jemaah = DataJemaah::create([
            'user_id' => $user->id, 'no_telepon' => '08123', 'status_data' => 'terverifikasi',
        ]);
        $hotel = DB::table('hotels')->insertGetId([
            'nama' => 'Hotel', 'lokasi' => 'Makkah', 'bintang' => 4, 'tipe_kamar' => 'double',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $maskapai = DB::table('maskapai')->insertGetId([
            'airline_code' => 'SV', 'nama' => 'Saudia', 'asal_negara' => 'Saudi',
            'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $paket = DB::table('paket_umrah')->insertGetId([
            'nama_paket' => 'Umrah Reguler', 'durasi' => 9, 'hotel_makkah_id' => $hotel,
            'hotel_madinah_id' => $hotel, 'harga' => 25000000, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $jadwal = DB::table('keberangkatan')->insertGetId([
            'maskapai_berangkat_id' => $maskapai, 'maskapai_pulang_id' => $maskapai,
            'tanggal_keberangkatan' => today()->addDays(90), 'tanggal_pulang' => today()->addDays(98),
            'jam_berangkat' => '08:00', 'jam_tiba' => '16:00', 'jam_pulang' => '10:00',
            'jam_tiba_pulang' => '18:00', 'status' => 'pendaftaran',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        KeberangkatanJemaah::create([
            'jemaah_id' => $jemaah->id, 'paket_umrah_id' => $paket,
            'keberangkatan_id' => $jadwal, 'status' => 'aktif',
        ]);

        $this->actingAs($user)->get('/status-verifikasi')
            ->assertSuccessful()
            ->assertSee('Status Verifikasi Pendaftaran')
            ->assertSee('Umrah Reguler')
            ->assertSee('Status Keberangkatan Jemaah')
            ->assertSee('Aktif');
    }
}
