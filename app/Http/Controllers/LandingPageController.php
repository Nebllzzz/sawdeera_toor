<?php

namespace App\Http\Controllers;

use App\Models\PaketUmrah;
use App\Models\Keberangkatan;

class LandingPageController extends Controller
{
    public function getHomePaket()
    {
        $paket = PaketUmrah::with([
            'hotelMakkah',
            'hotelMadinah'
        ])
        ->where('is_active', true)
        ->limit(3)
        ->get();

        return response()->json($paket);
    }

    public function detail($id)
    {
        $paket = PaketUmrah::with([
            'hotelMakkah',
            'hotelMadinah',
            'fasilitas',
            'program'
        ])->findOrFail($id);

        $keberangkatan = Keberangkatan::with([
            'maskapaiBerangkat',
            'maskapaiPulang',
            'leader'
        ])
            ->where('paket_id', $paket->id)
            ->whereIn('status', [Keberangkatan::STATUS_AKTIF, Keberangkatan::STATUS_DISETUJUI])
            ->whereDate('tanggal_keberangkatan', '>', today())
            ->orderBy('tanggal_keberangkatan')
            ->get();

        return response()->json([
            'paket' => $paket,
            'keberangkatan' => $keberangkatan
        ]);
    }

    public function getAllPaket()
    {
        $paket = PaketUmrah::with([
            'hotelMakkah',
            'hotelMadinah'
        ])
        ->where('is_active', true)
        ->get();

        return response()->json($paket);
    }
}
