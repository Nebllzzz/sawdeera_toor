<?php

namespace Tests\Feature;

use App\Models\DataJemaah;
use App\Models\DokumenJemaah;
use App\Models\Keberangkatan;
use App\Models\KeberangkatanJemaah;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RequestedUpdatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_shows_forgot_password_help_and_rejected_account_message(): void
    {
        $this->get('/login')
            ->assertSuccessful()
            ->assertSee('Lupa Password?')
            ->assertSee('Hubungi admin untuk reset password');

        User::factory()->create([
            'email' => 'ditolak@example.com',
            'password' => bcrypt('rahasia123'),
            'role' => 'jemaah',
            'status' => 'tidak_aktif',
        ]);

        $this->post('/actionlogin', [
            'email' => 'ditolak@example.com',
            'password' => 'rahasia123',
        ])->assertSessionHas('gagal', 'akun anda ditolak, silahkan hubungi admin untuk info selanjutnya');
    }

    public function test_admin_can_reject_reset_and_delete_an_inactive_registration(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
        $jemaah = User::factory()->create(['role' => 'jemaah', 'status' => 'proses']);
        DataJemaah::create(['user_id' => $jemaah->id, 'no_telepon' => '08123']);

        $this->actingAs($admin)->get("/jemaah/registrasi/{$jemaah->id}")
            ->assertSuccessful()
            ->assertSee('Aktifkan')
            ->assertSee('Tolak');

        $this->actingAs($admin)->postJson("/jemaah/toggle/{$jemaah->id}", [
            'status' => 'tidak_aktif',
        ])->assertSuccessful();
        $this->assertDatabaseHas('users', ['id' => $jemaah->id, 'status' => 'tidak_aktif']);

        $list = $this->actingAs($admin)->postJson('/jemaah/registrasi/data')->assertSuccessful();
        $this->assertStringContainsString('Delete', $list->json('data.0.action'));
        $this->assertStringContainsString('Reset Password', $list->json('data.0.action'));

        $reset = $this->actingAs($admin)->postJson("/jemaah/reset-password/{$jemaah->id}")
            ->assertSuccessful();
        $this->assertTrue(Hash::check($reset->json('temporary_password'), $jemaah->fresh()->password));

        $this->actingAs($admin)->deleteJson("/jemaah/delete/{$jemaah->id}")
            ->assertSuccessful();
        $this->assertDatabaseMissing('users', ['id' => $jemaah->id]);
    }

    public function test_data_verification_list_only_contains_active_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
        foreach (['aktif', 'tidak_aktif'] as $status) {
            $user = User::factory()->create([
                'name' => "Jemaah {$status}",
                'role' => 'jemaah',
                'status' => $status,
            ]);
            DataJemaah::create([
                'user_id' => $user->id,
                'no_telepon' => '08123',
                'status_data' => 'menunggu_verifikasi',
            ]);
        }

        $response = $this->actingAs($admin)->postJson('/jemaah/data')->assertSuccessful();
        $this->assertCount(1, $response->json('data'));
        $this->assertSame('Jemaah aktif', $response->json('data.0.name'));
        $this->assertArrayNotHasKey('statusActivity', $response->json('data.0'));
    }

    public function test_only_active_schedule_can_be_selected_and_new_application_is_automatically_accepted(): void
    {
        $user = User::factory()->create(['role' => 'jemaah', 'status' => 'aktif']);
        $jemaah = DataJemaah::create(['user_id' => $user->id, 'no_telepon' => '08123']);
        [$package, $active] = $this->createPackageAndSchedule(Keberangkatan::STATUS_AKTIF);
        [, $approved] = $this->createPackageAndSchedule(Keberangkatan::STATUS_DISETUJUI, $package);

        $this->actingAs($user)->getJson("/keberangkatan-jemaah/paket/{$package}")
            ->assertSuccessful()
            ->assertJsonCount(1, 'keberangkatan')
            ->assertJsonPath('keberangkatan.0.id', $active);

        $this->actingAs($user)->postJson('/keberangkatan-jemaah/store', [
            'paket_umrah_id' => $package,
            'keberangkatan_id' => $approved,
            'jenis_pembayaran' => 'sekali_bayar',
        ])->assertNotFound();

        $this->actingAs($user)->postJson('/keberangkatan-jemaah/store', [
            'paket_umrah_id' => $package,
            'keberangkatan_id' => $active,
            'jenis_pembayaran' => 'sekali_bayar',
        ])->assertSuccessful()->assertJsonPath('redirect', '/pendaftaran-saya');

        $this->assertDatabaseHas('keberangkatan_jemaah', [
            'jemaah_id' => $jemaah->id,
            'status' => KeberangkatanJemaah::STATUS_SETUJU,
        ]);
    }

    public function test_approval_list_and_submission_use_only_fully_verified_jemaah(): void
    {
        $operator = User::factory()->create(['role' => 'operator', 'status' => 'aktif']);
        [$package, $schedule] = $this->createPackageAndSchedule(Keberangkatan::STATUS_AKTIF, null, 2);

        $ready = $this->createJemaahApplication('Siap Approval', $package, $schedule, true);
        $this->createJemaahApplication('Belum Siap', $package, $schedule, false);

        $list = $this->actingAs($operator)->postJson('/keberangkatan/jemaah/data', [
            'keberangkatan_id' => $schedule,
        ])->assertSuccessful();
        $this->assertCount(1, $list->json('data'));
        $this->assertSame($ready->jemaah->user->name, $list->json('data.0.nama'));

        $this->actingAs($operator)->postJson('/keberangkatan/update-status', [
            'id' => $schedule,
            'action' => 'submit',
        ])->assertUnprocessable();

        Keberangkatan::whereKey($schedule)->update(['kuota' => 1]);
        $this->actingAs($operator)->postJson('/keberangkatan/update-status', [
            'id' => $schedule,
            'action' => 'submit',
        ])->assertSuccessful();
        $this->assertDatabaseHas('keberangkatan', ['id' => $schedule, 'status' => Keberangkatan::STATUS_PENGAJUAN]);
    }

    public function test_jemaah_schedule_page_only_offers_reschedule_and_clean_itinerary_pdf(): void
    {
        $application = $this->createApplicationWithNewSchedule('Jemaah Itinerary');
        $user = $application->jemaah->user;

        $this->actingAs($user)->get('/keberangkatan-jemaah')
            ->assertSuccessful()
            ->assertDontSee('Apakah Anda menyetujui jadwal keberangkatan ini?')
            ->assertSee('Reschedule')
            ->assertSee('Unduh Itinerary')
            ->assertSee('Batas pengajuan perubahan minimal H-45 sebelum berangkat');

        $this->actingAs($user)->get('/keberangkatan-jemaah/itinerary')
            ->assertSuccessful()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_completed_personal_data_redirects_to_document_upload(): void
    {
        $application = $this->createApplicationWithNewSchedule('Jemaah Data Diri');
        $user = $application->jemaah->user;

        $this->actingAs($user)->post('/pendaftaran-saya', [
            'nik' => '3201010101010001',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'laki_laki',
            'status_pernikahan' => 'belum_menikah',
            'pekerjaan' => 'Wiraswasta',
            'alamat' => 'Jakarta',
            'kontak_darurat' => '081234567890',
            'hubungan_kontak_darurat' => 'Keluarga',
            'nomor_paspor' => 'A1234567',
            'tanggal_terbit_paspor' => '2025-01-01',
            'tanggal_kedaluwarsa_paspor' => '2030-01-01',
            'tempat_penerbitan_paspor' => 'Jakarta',
            'golongan_darah' => 'O',
        ])->assertRedirect('/dokumen');

        $this->assertDatabaseHas('data_jemaah', [
            'id' => $application->jemaah_id,
            'status_data' => 'menunggu_verifikasi',
        ]);
    }

    public function test_progress_steps_wait_for_full_admin_verification(): void
    {
        [$package, $schedule] = $this->createPackageAndSchedule(Keberangkatan::STATUS_AKTIF);
        $application = $this->createJemaahApplication('Jemaah Progress', $package, $schedule, false);
        $jemaah = $application->jemaah;

        foreach ($jemaah->requiredDocumentTypes() as $index => $type) {
            DokumenJemaah::create([
                'jemaah_id' => $jemaah->id,
                'jenis_dokumen' => $type,
                'file_path' => "dokumen/{$type}.pdf",
                'status' => $index === 0 ? 'diproses' : 'diverifikasi',
            ]);
        }
        $payment = Pembayaran::create([
            'keberangkatan_jemaah_id' => $application->id,
            'jemaah_id' => $jemaah->id,
            'keberangkatan_id' => $schedule,
            'total_tagihan' => 25000000,
            'jenis_pembayaran' => 'cicilan_3_bulan',
            'jumlah_tahap' => 2,
            'status_rencana' => 'aktif',
            'status' => 'diproses',
        ]);
        foreach ([1 => 'diverifikasi', 2 => 'belum_bayar'] as $order => $status) {
            $payment->tahapan()->create([
                'urutan' => $order,
                'nama_tahap' => "Tahap {$order}",
                'persentase' => 50,
                'nominal' => 12500000,
                'jatuh_tempo' => today()->addMonth($order - 1),
                'status' => $status,
            ]);
        }

        $html = $this->actingAs($jemaah->user)->get('/status-verifikasi')
            ->assertSuccessful()
            ->getContent();

        $this->assertMatchesRegularExpression('/verify-node processing.*?Lengkapi Data Diri.*?Sedang Diverifikasi/s', $html);
        $this->assertMatchesRegularExpression('/verify-node processing.*?Upload Dokumen Pendukung.*?Sedang Diverifikasi/s', $html);
        $this->assertMatchesRegularExpression('/verify-node processing.*?Upload Bukti Pembayaran.*?Sedang Diverifikasi/s', $html);
    }

    private function createApplicationWithNewSchedule(string $name): KeberangkatanJemaah
    {
        [$package, $schedule] = $this->createPackageAndSchedule(Keberangkatan::STATUS_AKTIF);

        return $this->createJemaahApplication($name, $package, $schedule, false);
    }

    private function createJemaahApplication(string $name, int $package, int $schedule, bool $ready): KeberangkatanJemaah
    {
        $user = User::factory()->create(['name' => $name, 'role' => 'jemaah', 'status' => 'aktif']);
        $jemaah = DataJemaah::create([
            'user_id' => $user->id,
            'no_telepon' => '08123',
            'status_data' => $ready ? 'terverifikasi' : 'menunggu_verifikasi',
            'status_pernikahan' => 'belum_menikah',
        ]);
        $application = KeberangkatanJemaah::create([
            'jemaah_id' => $jemaah->id,
            'paket_umrah_id' => $package,
            'keberangkatan_id' => $schedule,
            'status' => KeberangkatanJemaah::STATUS_SETUJU,
        ]);

        if ($ready) {
            foreach ($jemaah->requiredDocumentTypes() as $type) {
                DokumenJemaah::create([
                    'jemaah_id' => $jemaah->id,
                    'jenis_dokumen' => $type,
                    'file_path' => "dokumen/{$type}.pdf",
                    'status' => 'diverifikasi',
                ]);
            }
            $payment = Pembayaran::create([
                'keberangkatan_jemaah_id' => $application->id,
                'jemaah_id' => $jemaah->id,
                'keberangkatan_id' => $schedule,
                'total_tagihan' => 25000000,
                'jenis_pembayaran' => 'sekali_bayar',
                'jumlah_tahap' => 1,
                'status_rencana' => 'aktif',
                'status' => 'diverifikasi',
            ]);
            $payment->tahapan()->create([
                'urutan' => 1,
                'nama_tahap' => 'Pembayaran Penuh',
                'persentase' => 100,
                'nominal' => 25000000,
                'jatuh_tempo' => today(),
                'status' => 'diverifikasi',
            ]);
        }

        return $application->load('jemaah.user');
    }

    private function createPackageAndSchedule(string $status, ?int $package = null, int $quota = 40): array
    {
        $hotel = DB::table('hotels')->insertGetId([
            'nama' => 'Hotel Test', 'lokasi' => 'Makkah', 'bintang' => 4, 'tipe_kamar' => 'double',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $airline = DB::table('maskapai')->insertGetId([
            'airline_code' => 'TS'.random_int(10, 99), 'nama' => 'Air Test', 'asal_negara' => 'Indonesia',
            'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $package ??= DB::table('paket_umrah')->insertGetId([
            'nama_paket' => 'Paket Test', 'durasi' => 9,
            'hotel_makkah_id' => $hotel, 'hotel_madinah_id' => $hotel,
            'harga' => 25000000, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $schedule = DB::table('keberangkatan')->insertGetId([
            'paket_id' => $package, 'kuota' => $quota,
            'maskapai_berangkat_id' => $airline, 'maskapai_pulang_id' => $airline,
            'tanggal_keberangkatan' => today()->addDays(90), 'tanggal_pulang' => today()->addDays(98),
            'jam_berangkat' => '08:00', 'jam_tiba' => '16:00', 'jam_pulang' => '10:00',
            'jam_tiba_pulang' => '18:00', 'status' => $status,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return [$package, $schedule];
    }
}
