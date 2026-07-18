<?php

namespace Tests\Feature;

use App\Models\DataJemaah;
use App\Models\DokumenJemaah;
use App\Models\KeberangkatanJemaah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentVerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_jemaah_uploads_new_document_types_and_admin_verifies_from_detail(): void
    {
        Storage::fake('public');
        $jemaahUser = User::factory()->create(['role' => 'jemaah', 'status' => 'aktif']);
        $jemaah = DataJemaah::create([
            'user_id' => $jemaahUser->id,
            'no_telepon' => '08123',
            'status_data' => 'menunggu_verifikasi',
            'status_pernikahan' => 'menikah',
        ]);
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
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
            'keberangkatan_id' => $jadwal, 'status' => KeberangkatanJemaah::STATUS_PENDAFTARAN,
        ]);

        $this->actingAs($jemaahUser)->get('/dokumen')
            ->assertSuccessful()
            ->assertSee('Buku Nikah');

        $this->actingAs($jemaahUser)->post('/dokumen/upload', [
            'jenis_dokumen' => 'kartu_keluarga',
            'file' => UploadedFile::fake()->create('kk.pdf', 300, 'application/pdf'),
        ])->assertRedirect();
        $this->actingAs($jemaahUser)->post('/dokumen/upload', [
            'jenis_dokumen' => 'foto_4x6',
            'file' => UploadedFile::fake()->image('foto-4x6.jpg', 400, 600),
        ])->assertRedirect();
        $this->actingAs($jemaahUser)->post('/dokumen/upload', [
            'jenis_dokumen' => 'buku_nikah',
            'file' => UploadedFile::fake()->create('buku-nikah.pdf', 300, 'application/pdf'),
        ])->assertRedirect();

        $this->assertDatabaseHas('dokumen_jemaah', [
            'jemaah_id' => $jemaah->id, 'jenis_dokumen' => 'kartu_keluarga', 'status' => 'diproses',
        ]);
        $this->assertDatabaseHas('dokumen_jemaah', [
            'jemaah_id' => $jemaah->id, 'jenis_dokumen' => 'foto_4x6', 'status' => 'diproses',
        ]);
        $this->assertDatabaseHas('dokumen_jemaah', [
            'jemaah_id' => $jemaah->id, 'jenis_dokumen' => 'buku_nikah', 'status' => 'diproses',
        ]);

        $this->actingAs($admin)->get("/admin/dokumen/{$jemaah->id}")
            ->assertSuccessful()->assertSee('Kartu Keluarga')->assertSee('Buku Nikah')->assertSee('Foto Jemaah 4×6');

        $kk = DokumenJemaah::where('jenis_dokumen', 'kartu_keluarga')->firstOrFail();
        $this->actingAs($admin)->postJson("/admin/dokumen/{$kk->id}/approve")
            ->assertSuccessful();
        $this->assertDatabaseHas('dokumen_jemaah', ['id' => $kk->id, 'status' => 'diverifikasi']);
    }

    public function test_document_upload_requires_departure_and_completed_registration(): void
    {
        Storage::fake('public');
        $jemaahUser = User::factory()->create(['role' => 'jemaah', 'status' => 'aktif']);
        $jemaah = DataJemaah::create(['user_id' => $jemaahUser->id, 'no_telepon' => '08123']);

        $this->actingAs($jemaahUser)->post('/dokumen/upload', [
            'jenis_dokumen' => 'buku_nikah',
            'file' => UploadedFile::fake()->create('buku-nikah.pdf', 300, 'application/pdf'),
        ])->assertStatus(422);

        $this->assertDatabaseMissing('dokumen_jemaah', [
            'jemaah_id' => $jemaah->id,
            'jenis_dokumen' => 'buku_nikah',
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
            'keberangkatan_id' => $jadwal, 'status' => KeberangkatanJemaah::STATUS_PENDAFTARAN,
        ]);

        $this->actingAs($jemaahUser)->post('/dokumen/upload', [
            'jenis_dokumen' => 'buku_nikah',
            'file' => UploadedFile::fake()->create('buku-nikah.pdf', 300, 'application/pdf'),
        ])->assertStatus(422);

        $this->assertDatabaseMissing('dokumen_jemaah', [
            'jemaah_id' => $jemaah->id,
            'jenis_dokumen' => 'buku_nikah',
        ]);

        $jemaah->update(['status_data' => 'menunggu_verifikasi', 'status_pernikahan' => 'belum_menikah']);

        $this->actingAs($jemaahUser)->get('/dokumen')
            ->assertSuccessful()
            ->assertDontSee('Buku Nikah');

        $this->actingAs($jemaahUser)->post('/dokumen/upload', [
            'jenis_dokumen' => 'buku_nikah',
            'file' => UploadedFile::fake()->create('buku-nikah.pdf', 300, 'application/pdf'),
        ])->assertStatus(422);

        $this->assertDatabaseMissing('dokumen_jemaah', [
            'jemaah_id' => $jemaah->id,
            'jenis_dokumen' => 'buku_nikah',
        ]);
    }
}
