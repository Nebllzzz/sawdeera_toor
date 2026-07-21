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
use Tests\TestCase;

class LeadershipInvoiceAndRescheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_uses_leadership_dashboard_and_operator_keeps_operational_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
        $operator = User::factory()->create(['role' => 'operator', 'status' => 'aktif']);

        $this->actingAs($admin)->get('/dashboard')
            ->assertSuccessful()
            ->assertViewIs('dashboard.pimpinan')
            ->assertSee('Pimpinan / Owner')
            ->assertSee('leaderRegistrationChart')
            ->assertSee('Approval Jadwal Keberangkatan Terbaru');

        $this->actingAs($operator)->get('/dashboard')
            ->assertSuccessful()
            ->assertViewIs('dashboard.admin')
            ->assertSee('Total Pembayaran')
            ->assertDontSee('leaderRegistrationChart');
    }

    public function test_invoice_button_and_pdf_are_only_available_after_every_uploaded_step_is_verified(): void
    {
        [$user, $jemaah, $application, $schedule] = $this->createJemaahApplication('Jemaah Invoice');
        $payment = $this->createPayment($jemaah, $application, $schedule, 'diproses', 2);

        $first = $payment->tahapan()->create([
            'urutan' => 1,
            'nama_tahap' => 'DP',
            'persentase' => 50,
            'nominal' => 12500000,
            'jatuh_tempo' => today(),
            'bukti_pembayaran' => 'bukti_pembayaran/termin/dp.jpg',
            'uploaded_at' => now()->subDay(),
            'verified_at' => now()->subHours(12),
            'status' => 'diverifikasi',
        ]);
        $second = $payment->tahapan()->create([
            'urutan' => 2,
            'nama_tahap' => 'Pelunasan',
            'persentase' => 50,
            'nominal' => 12500000,
            'jatuh_tempo' => today()->addMonth(),
            'bukti_pembayaran' => 'bukti_pembayaran/termin/pelunasan.jpg',
            'uploaded_at' => now(),
            'status' => 'diproses',
        ]);

        $this->actingAs($user)->get('/pemabayan')
            ->assertSuccessful()
            ->assertDontSee('Download Invoice');
        $this->actingAs($user)->get('/pemabayan/invoice')->assertForbidden();

        $second->update(['status' => 'diverifikasi', 'verified_at' => now()]);
        $payment->update(['status' => 'diverifikasi']);

        $this->actingAs($user)->get('/pemabayan')
            ->assertSuccessful()
            ->assertSee('Download Invoice')
            ->assertSee(route('jemaah.invoice'));

        $this->actingAs($user)->get('/pemabayan/invoice')
            ->assertSuccessful()
            ->assertHeader('content-type', 'application/pdf')
            ->assertDownload('invoice-sawdeera-'.str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT).'.pdf');

        $otherUser = User::factory()->create(['role' => 'jemaah', 'status' => 'aktif']);
        DataJemaah::create(['user_id' => $otherUser->id, 'no_telepon' => '0800000002']);
        $this->actingAs($otherUser)->get('/pemabayan/invoice')->assertForbidden();

        $this->assertSame('diverifikasi', $first->fresh()->status);
    }

    public function test_reschedule_requires_verified_data_documents_and_all_payment_steps(): void
    {
        [$user, $jemaah, $application, $schedule, $package] = $this->createJemaahApplication('Jemaah Reschedule');
        $this->createSchedule($package, today()->addDays(140));

        $this->actingAs($user)->get('/keberangkatan-jemaah')
            ->assertSuccessful()
            ->assertDontSee('id="btnOpenReschedule"', false);
        $this->actingAs($user)->getJson('/keberangkatan-jemaah/reschedule-options')
            ->assertUnprocessable()
            ->assertSee('Data diri harus terverifikasi');

        $jemaah->update(['status_data' => 'terverifikasi']);
        $this->actingAs($user)->getJson('/keberangkatan-jemaah/reschedule-options')
            ->assertUnprocessable()
            ->assertSee('Seluruh dokumen wajib harus terverifikasi');

        foreach ($jemaah->requiredDocumentTypes() as $type) {
            DokumenJemaah::create([
                'jemaah_id' => $jemaah->id,
                'jenis_dokumen' => $type,
                'file_path' => "dokumen/{$type}.pdf",
                'status' => 'diverifikasi',
            ]);
        }
        $this->actingAs($user)->getJson('/keberangkatan-jemaah/reschedule-options')
            ->assertUnprocessable()
            ->assertSee('Seluruh tahap pembayaran harus terverifikasi');

        $payment = $this->createPayment($jemaah, $application, $schedule, 'diverifikasi');
        $payment->tahapan()->create([
            'urutan' => 1,
            'nama_tahap' => 'Pembayaran Penuh',
            'persentase' => 100,
            'nominal' => 25000000,
            'jatuh_tempo' => today(),
            'status' => 'diverifikasi',
        ]);

        $this->actingAs($user)->get('/keberangkatan-jemaah')
            ->assertSuccessful()
            ->assertSee('id="btnOpenReschedule"', false);
        $this->actingAs($user)->getJson('/keberangkatan-jemaah/reschedule-options')
            ->assertSuccessful()
            ->assertJsonCount(1);
    }

    private function createJemaahApplication(string $name): array
    {
        $user = User::factory()->create(['name' => $name, 'role' => 'jemaah', 'status' => 'aktif']);
        $jemaah = DataJemaah::create([
            'user_id' => $user->id,
            'no_telepon' => '081234567890',
            'alamat' => 'Tangerang, Banten',
            'status_pernikahan' => 'belum_menikah',
            'status_data' => 'menunggu_verifikasi',
        ]);

        $hotel = DB::table('hotels')->insertGetId([
            'nama' => 'Hotel Test', 'lokasi' => 'Makkah', 'bintang' => 4, 'tipe_kamar' => 'double',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $airline = DB::table('maskapai')->insertGetId([
            'airline_code' => 'IN'.random_int(10, 99), 'nama' => 'Air Invoice', 'asal_negara' => 'Indonesia',
            'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $package = DB::table('paket_umrah')->insertGetId([
            'nama_paket' => 'Umrah Reguler', 'durasi' => 9,
            'hotel_makkah_id' => $hotel, 'hotel_madinah_id' => $hotel,
            'harga' => 25000000, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $schedule = $this->createSchedule($package, today()->addDays(90), $airline);
        $application = KeberangkatanJemaah::create([
            'jemaah_id' => $jemaah->id,
            'paket_umrah_id' => $package,
            'keberangkatan_id' => $schedule,
            'status' => KeberangkatanJemaah::STATUS_SETUJU,
        ]);

        return [$user, $jemaah, $application, $schedule, $package];
    }

    private function createSchedule(int $package, $departure, ?int $airline = null): int
    {
        $airline ??= (int) DB::table('maskapai')->value('id');

        return DB::table('keberangkatan')->insertGetId([
            'paket_id' => $package,
            'maskapai_berangkat_id' => $airline,
            'maskapai_pulang_id' => $airline,
            'kuota' => 40,
            'tanggal_keberangkatan' => $departure,
            'tanggal_pulang' => $departure->copy()->addDays(8),
            'jam_berangkat' => '08:00',
            'jam_tiba' => '16:00',
            'jam_pulang' => '10:00',
            'jam_tiba_pulang' => '18:00',
            'status' => Keberangkatan::STATUS_AKTIF,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createPayment(
        DataJemaah $jemaah,
        KeberangkatanJemaah $application,
        int $schedule,
        string $status,
        int $steps = 1
    ): Pembayaran {
        return Pembayaran::create([
            'keberangkatan_jemaah_id' => $application->id,
            'jemaah_id' => $jemaah->id,
            'keberangkatan_id' => $schedule,
            'total_tagihan' => 25000000,
            'jenis_pembayaran' => $steps === 1 ? 'sekali_bayar' : 'cicilan_3_bulan',
            'jumlah_tahap' => $steps,
            'status_rencana' => 'aktif',
            'status' => $status,
        ]);
    }
}
