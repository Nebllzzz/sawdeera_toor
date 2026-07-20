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

class AdminOperatorUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_master_data_pages_use_shared_heading_and_javascript_add_modal_trigger(): void
    {
        $operator = User::factory()->create(['role' => 'operator', 'status' => 'aktif']);

        $pages = [
            '/paket-umrah' => ['Paket Umrah', 'btnAddPaket', 'showAppModal("modalPaket")'],
            '/hotel' => ['Data Hotel', 'btnAddHotel', 'showAppModal("modalHotel")'],
            '/maskapai' => ['Data Maskapai', 'btnAddMaskapai', 'showAppModal("modalMaskapai")'],
            '/tour-leader' => ['Data Tour Leader', 'btnAddLeader', 'showAppModal("modalLeader")'],
        ];

        foreach ($pages as $url => [$title, $buttonId, $modalCall]) {
            $this->actingAs($operator)->get($url)
                ->assertSuccessful()
                ->assertSee('class="recap-heading"', false)
                ->assertSee($title)
                ->assertSee('id="'.$buttonId.'"', false)
                ->assertSee($modalCall, false);
        }
    }

    public function test_departure_jemaah_detail_action_opens_html_detail_page_with_the_correct_user_id(): void
    {
        $operator = User::factory()->create(['role' => 'operator', 'status' => 'aktif']);
        $jemaahUser = User::factory()->create(['role' => 'jemaah', 'status' => 'aktif']);
        $jemaah = DataJemaah::create([
            'user_id' => $jemaahUser->id,
            'no_telepon' => '08123456789',
            'status_data' => 'terverifikasi',
            'status_pernikahan' => 'belum_menikah',
        ]);
        [$packageId, $scheduleId] = $this->createPackageAndSchedule();
        $application = KeberangkatanJemaah::create([
            'keberangkatan_id' => $scheduleId,
            'jemaah_id' => $jemaah->id,
            'paket_umrah_id' => $packageId,
            'status' => KeberangkatanJemaah::STATUS_PENDAFTARAN,
        ]);
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
            'keberangkatan_id' => $scheduleId,
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

        $response = $this->actingAs($operator)->postJson('/keberangkatan/jemaah/data', [
            'keberangkatan_id' => $scheduleId,
        ])->assertSuccessful();

        $action = $response->json('data.0.action');
        $this->assertStringContainsString('/jemaah/data-verifikasi/'.$jemaahUser->id, $action);
        $this->assertStringNotContainsString('/jemaah/detail/', $action);

        $this->actingAs($operator)->get('/jemaah/data-verifikasi/'.$jemaahUser->id)
            ->assertSuccessful()
            ->assertSee('Detail Data Jemaah')
            ->assertSee($jemaahUser->name);
    }

    private function createPackageAndSchedule(): array
    {
        $hotel = DB::table('hotels')->insertGetId([
            'nama' => 'Hotel UI', 'lokasi' => 'mekkah', 'bintang' => 4, 'tipe_kamar' => 'double',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $airline = DB::table('maskapai')->insertGetId([
            'airline_code' => 'UI', 'nama' => 'Air UI', 'asal_negara' => 'Indonesia', 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $package = DB::table('paket_umrah')->insertGetId([
            'nama_paket' => 'Paket UI', 'durasi' => 9,
            'hotel_makkah_id' => $hotel, 'hotel_madinah_id' => $hotel,
            'harga' => 25000000, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $schedule = DB::table('keberangkatan')->insertGetId([
            'paket_id' => $package,
            'maskapai_berangkat_id' => $airline,
            'maskapai_pulang_id' => $airline,
            'kuota' => 40,
            'tanggal_keberangkatan' => today()->addMonths(3),
            'tanggal_pulang' => today()->addMonths(3)->addDays(8),
            'jam_berangkat' => '08:00', 'jam_tiba' => '16:00',
            'jam_pulang' => '10:00', 'jam_tiba_pulang' => '18:00',
            'status' => Keberangkatan::STATUS_AKTIF,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return [$package, $schedule];
    }
}
