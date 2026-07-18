<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\JemaahRecapService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class JemaahRecapTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_open_jemaah_recap_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
        $operator = User::factory()->create(['role' => 'operator', 'status' => 'aktif']);

        $this->actingAs($admin)->get('/admin/rekapitulasi-jemaah')
            ->assertSuccessful()
            ->assertSee('Rekapitulasi Data Jemaah')
            ->assertSee('Rekapitulasi Dokumen');

        $this->actingAs($operator)->get('/admin/rekapitulasi-jemaah')->assertForbidden();
    }

    public function test_all_recap_types_return_package_month_rows(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
        $packageId = $this->createPackage();
        $year = now()->year;

        foreach (array_keys(JemaahRecapService::reportTypes()) as $type) {
            $response = $this->actingAs($admin)->getJson('/admin/rekapitulasi-jemaah/data?'.http_build_query([
                'type' => $type,
                'package_id' => $packageId,
                'start_date' => "{$year}-01-01",
                'end_date' => "{$year}-12-31",
            ]));

            $response->assertSuccessful()
                ->assertJsonPath('type', $type)
                ->assertJsonPath('columns.0.key', 'package_name')
                ->assertJsonPath('columns.1.key', 'month_label')
                ->assertJsonCount(12, 'rows')
                ->assertJsonPath('rows.0.package_id', $packageId);
        }
    }

    public function test_exports_use_the_requested_filters(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
        $packageId = $this->createPackage();
        $year = now()->year;
        $query = http_build_query([
            'type' => 'pendaftaran',
            'package_id' => $packageId,
            'start_date' => "{$year}-01-01",
            'end_date' => "{$year}-12-31",
        ]);

        $excel = $this->actingAs($admin)->get('/admin/rekapitulasi-jemaah/export/excel?'.$query);
        $excel->assertSuccessful();
        $this->assertStringContainsString('.xlsx', (string) $excel->headers->get('content-disposition'));

        $pdf = $this->actingAs($admin)->get('/admin/rekapitulasi-jemaah/export/pdf?'.$query);
        $pdf->assertSuccessful();
        $this->assertStringContainsString('.pdf', (string) $pdf->headers->get('content-disposition'));
    }

    private function createPackage(): int
    {
        $hotel = DB::table('hotels')->insertGetId([
            'nama' => 'Hotel Rekap',
            'lokasi' => 'Makkah',
            'bintang' => 4,
            'tipe_kamar' => 'double',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('paket_umrah')->insertGetId([
            'nama_paket' => 'Paket Rekap',
            'durasi' => 9,
            'hotel_makkah_id' => $hotel,
            'hotel_madinah_id' => $hotel,
            'harga' => 25000000,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
