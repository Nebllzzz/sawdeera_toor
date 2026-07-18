<?php

namespace Tests\Feature;

use App\Models\DataJemaah;
use App\Models\Keberangkatan;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentInstallmentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_three_installment_plan_and_step_verification_flow(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
        $user = User::factory()->create(['role' => 'jemaah', 'status' => 'aktif']);
        $jemaah = DataJemaah::create(['user_id' => $user->id, 'no_telepon' => '08123']);

        $hotelMakkah = DB::table('hotels')->insertGetId([
            'nama' => 'Hotel Makkah', 'lokasi' => 'Makkah', 'bintang' => 4,
            'tipe_kamar' => 'double', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $hotelMadinah = DB::table('hotels')->insertGetId([
            'nama' => 'Hotel Madinah', 'lokasi' => 'Madinah', 'bintang' => 4,
            'tipe_kamar' => 'double', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $maskapai = DB::table('maskapai')->insertGetId([
            'airline_code' => 'SV', 'nama' => 'Saudia', 'asal_negara' => 'Arab Saudi',
            'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $paket = DB::table('paket_umrah')->insertGetId([
            'nama_paket' => 'Umrah Gold', 'durasi' => 9,
            'hotel_makkah_id' => $hotelMakkah, 'hotel_madinah_id' => $hotelMadinah,
            'harga' => 30000000, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $jadwal = DB::table('keberangkatan')->insertGetId([
            'paket_id' => $paket, 'kuota' => 40,
            'maskapai_berangkat_id' => $maskapai, 'maskapai_pulang_id' => $maskapai,
            'tanggal_keberangkatan' => today()->addDays(90), 'tanggal_pulang' => today()->addDays(98),
            'jam_berangkat' => '08:00', 'jam_tiba' => '16:00', 'jam_pulang' => '10:00',
            'jam_tiba_pulang' => '18:00', 'status' => Keberangkatan::STATUS_AKTIF,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->getJson("/keberangkatan-jemaah/paket/{$paket}")
            ->assertSuccessful()
            ->assertJsonCount(1, 'keberangkatan')
            ->assertJsonPath('keberangkatan.0.id', $jadwal);
        $this->actingAs($user)
            ->getJson("/keberangkatan-jemaah/jadwal-paket/{$paket}/9")
            ->assertSuccessful()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $jadwal);

        $this->actingAs($user)->postJson('/keberangkatan-jemaah/store', [
            'paket_umrah_id' => $paket,
            'keberangkatan_id' => $jadwal,
            'jenis_pembayaran' => 'cicilan_3_bulan',
            'dp_persen' => 30,
        ])->assertSuccessful()->assertJsonPath('redirect', '/pemabayan');

        $payment = Pembayaran::with('tahapan')->firstOrFail();
        $this->assertCount(3, $payment->tahapan);
        $this->assertEquals(9000000, (float) $payment->tahapan[0]->nominal);
        $this->assertEquals(10500000, (float) $payment->tahapan[1]->nominal);
        $this->assertEquals(10500000, (float) $payment->tahapan[2]->nominal);
        $this->actingAs($user)->get('/pemabayan')->assertSuccessful()
            ->assertSee('Rencana Pembayaran (3 Tahap)');
        $this->actingAs($admin)->get("/admin/pemabayan/{$payment->id}/detail")
            ->assertSuccessful()->assertSee('Detail Pembayaran');

        $first = $payment->tahapan[0];
        $this->actingAs($user)->post('/pemabayan/upload', [
            'tahap_id' => $first->id,
            'metode_pembayaran' => 'Transfer BCA',
            'bukti_pembayaran' => UploadedFile::fake()->image('bukti.jpg'),
        ])->assertRedirect();
        $this->assertDatabaseHas('pembayaran_tahapan', ['id' => $first->id, 'status' => 'diproses']);

        $this->actingAs($admin)->postJson("/admin/pemabayan/{$first->id}/approve")
            ->assertSuccessful();
        $this->assertDatabaseHas('pembayaran_tahapan', ['id' => $first->id, 'status' => 'diverifikasi']);
    }
}
