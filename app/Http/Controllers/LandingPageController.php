<?php

namespace App\Http\Controllers;

use App\Models\PaketUmrah;
use App\Models\Keberangkatan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;


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
        ->get()
        ->filter(function ($item) use ($paket) {

            if (!$item->tanggal_keberangkatan || !$item->tanggal_pulang) {
                return false;
            }

            $start = Carbon::parse($item->tanggal_keberangkatan);
            $end   = Carbon::parse($item->tanggal_pulang);

            $selisih = $start->diffInDays($end) + 1;

            return $selisih == $paket->durasi;
        })
        ->values();

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
