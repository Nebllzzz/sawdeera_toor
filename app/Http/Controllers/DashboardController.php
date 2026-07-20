<?php

namespace App\Http\Controllers;

use App\Models\DataJemaah;
use App\Models\DokumenJemaah;
use App\Models\Keberangkatan;
use App\Models\KeberangkatanJemaah;
use App\Models\Maskapai;
use App\Models\PaketUmrah;
use App\Models\Pembayaran;
use App\Models\PembayaranTahapan;
use App\Models\TourLeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // role detection (admin/operator/jemaah)
        $role = $user->role ?? 'jemaah';

        if (in_array($role, ['admin', 'operator'])) {
            // ADMIN / OPERATOR DASHBOARD DATA
            $totalJemaah = DataJemaah::count();
            $totalPaket = PaketUmrah::count();
            $totalHotel = \App\Models\Hotel::count();
            $totalTourLeader = TourLeader::count();
            $totalMaskapai = Maskapai::count();

            $pembayaranTotal = PembayaranTahapan::where('status', 'diverifikasi')->sum('nominal');
            $pembayaranPending = PembayaranTahapan::where('status', 'diproses')->count();
            $dokumenPending = DokumenJemaah::where('status', 'diproses')->count();

            // charts: pembayaran per month (last 6 months)
            $pembayaranPerMonth = PembayaranTahapan::selectRaw("DATE_FORMAT(verified_at, '%Y-%m') as ym, SUM(nominal) as total")
                ->where('status', 'diverifikasi')
                ->where('verified_at', '>=', now()->subMonths(6))
                ->groupBy('ym')
                ->orderBy('ym')
                ->get()
                ->mapWithKeys(fn ($r) => [$r->ym => (float) $r->total]);

            // status dokumen breakdown
            $dokumenStatus = DokumenJemaah::selectRaw('status, count(*) as c')
                ->groupBy('status')
                ->pluck('c', 'status')
                ->toArray();

            // top paket (count jemaah registrations via keberangkatan_jemaah)
            $topPaket = PaketUmrah::withCount(['keberangkatanJemaah as jemaah_count'])
                ->orderByDesc('jemaah_count')
                ->limit(5)->get();

            // nearest keberangkatan
            $nearKeberangkatan = Keberangkatan::with('leader', 'maskapaiBerangkat', 'paket')
                ->where('tanggal_keberangkatan', '>=', now())
                ->orderBy('tanggal_keberangkatan')
                ->limit(5)->get();

            // statistik data jemaah
            $akunAktif = DataJemaah::whereHas('user', function ($q) {
                $q->where('status', 'aktif');
            })->count();

            $akunTidakAktif = DataJemaah::whereHas('user', function ($q) {
                $q->where('status', 'tidak_aktif');
            })->count();

            $akunProsesVerifikasi = DataJemaah::whereHas('user', function ($q) {
                $q->where('status', 'proses');
            })->count();

            return view('dashboard.admin', compact(
                'totalJemaah',
                'totalPaket',
                'totalHotel',
                'totalTourLeader',
                'totalMaskapai',
                'pembayaranTotal',
                'pembayaranPending',
                'dokumenPending',
                'pembayaranPerMonth',
                'dokumenStatus',
                'topPaket',
                'nearKeberangkatan',
                'akunAktif',
                'akunTidakAktif',
                'akunProsesVerifikasi',
            ));
        }

        // JEMAAH DASHBOARD
        $jemaah = $user->jemaah;

        // handle user without jemaah record gracefully
        if (! $jemaah) {
            return view('dashboard.jemaah-empty');
        }

        $required = $jemaah->requiredDocumentTypes();
        $docs = DokumenJemaah::where('jemaah_id', $jemaah->id)->get();

        $docStatus = collect($required)->mapWithKeys(function ($k) use ($docs) {
            $found = $docs->firstWhere('jenis_dokumen', $k);
            if (! $found) {
                return [$k => 'missing'];
            }

            return [$k => $found->status];
        })->toArray();

        $uploadedCount = collect($docStatus)->filter(fn ($s) => in_array($s, ['diproses', 'diverifikasi', 'ditolak'], true))->count();
        $missingCount = collect($docStatus)->filter(fn ($s) => $s === 'missing')->count();
        $rejectedCount = collect($docStatus)->filter(fn ($s) => $s === 'ditolak')->count();
        $completeCount = collect($docStatus)->filter(fn ($s) => $s === 'diverifikasi')->count();

        // latest payment
        $latestPayment = Pembayaran::with('tahapan')->where('jemaah_id', $jemaah->id)->orderByDesc('created_at')->first();
        $paymentComplete = $latestPayment?->isFullyVerified() ?? false;

        // keberangkatan info
        $kJ = KeberangkatanJemaah::with('keberangkatan', 'paketUmrah', 'jemaah')
            ->where('jemaah_id', $jemaah->id)
            ->orderByDesc('id')
            ->first();

        $countdown = null;

        if (
            $kJ &&
            $kJ->keberangkatan &&
            $kJ->keberangkatan->tanggal_keberangkatan
        ) {

            $timezone = 'Asia/Jakarta';

            $tanggalBerangkat = Carbon::parse(
                $kJ->keberangkatan->tanggal_keberangkatan, $timezone)->startOfDay();

            $hariIni = Carbon::now($timezone)->startOfDay();

            // kalau tanggal sudah lewat
            if ($tanggalBerangkat->lessThan($hariIni)) {

                $countdown = 0;

            } else {

                $countdown = $hariIni->diff($tanggalBerangkat)->days;

            }

        }

        // data diri + dokumen wajib + pembayaran
        $points = count($required) + 2;
        $score = 0;
        // 1. data diri hanya selesai setelah diverifikasi admin
        if ($jemaah->status_data === 'terverifikasi') {
            $score++;
        }
        // dokumen hanya dihitung selesai satu per satu setelah diverifikasi
        $score += $completeCount;
        // pembayaran selesai setelah seluruh tahap diverifikasi
        if ($paymentComplete) {
            $score++;
        }

        $percent = round(($score / $points) * 100);

        // recent activity (combine docs and payments)
        $recentDocs = DokumenJemaah::where('jemaah_id', $jemaah->id)->orderByDesc('updated_at')->limit(6)->get();
        $recentPayments = Pembayaran::where('jemaah_id', $jemaah->id)->orderByDesc('updated_at')->limit(6)->get();

        return view('dashboard.jemaah', compact(
            'user',
            'jemaah',
            'docStatus',
            'missingCount',
            'rejectedCount',
            'completeCount',
            'uploadedCount',
            'latestPayment',
            'paymentComplete',
            'kJ',
            'countdown',
            'percent',
            'recentDocs',
            'recentPayments'
        ));
    }
}
