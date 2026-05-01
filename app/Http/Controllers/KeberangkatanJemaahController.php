<?php

namespace App\Http\Controllers;

use App\Models\KeberangkatanJemaah;
use App\Models\PaketUmrah;
use App\Models\Keberangkatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeberangkatanJemaahController extends Controller
{
    public function index()
    {
        $jemaah = auth()->user()->jemaah;
        $keberangkatanJemaah = KeberangkatanJemaah::with(['keberangkatan.maskapaiBerangkat', 'keberangkatan.maskapaiPulang', 'keberangkatan.leader', 'paketUmrah'])
            ->where('jemaah_id', $jemaah->id)->get();
        return view('home.keberangkatan-jemaah.index', compact('keberangkatanJemaah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'keberangkatan_id' => 'required|exists:keberangkatan,id',
            'paket_umrah_id' => 'required|exists:paket_umrah,id'
        ]);

        $jemaahId = auth()->user()->jemaah->id;

        // Prevent duplicate
        $existing = KeberangkatanJemaah::where([
            'jemaah_id' => $jemaahId,
            'keberangkatan_id' => $request->keberangkatan_id,
            'paket_umrah_id' => $request->paket_umrah_id
        ])->first();

        if ($existing) {
            return response()->json(['message' => 'Anda sudah terdaftar pada jadwal ini'], 400);
        }

        KeberangkatanJemaah::create([
            'jemaah_id' => $jemaahId,
            'keberangkatan_id' => $request->keberangkatan_id,
            'paket_umrah_id' => $request->paket_umrah_id,
            'status' => 'aktif'
        ]);

        return response()->json(['message' => 'Jadwal berhasil ditambahkan!']);
    }

    public function jadwalByPaket($paketId, $durasi)
    {
        $jadwal = Keberangkatan::with(['maskapaiBerangkat', 'maskapaiPulang'])
            ->whereIn('status', ['pendaftaran', 'persiapan'])
            ->whereRaw('DATEDIFF(tanggal_pulang, tanggal_keberangkatan) + 1 BETWEEN ? - 1 AND ? + 1', [$durasi, $durasi])
            ->get(['id', 'tanggal_keberangkatan', 'tanggal_pulang', 'maskapai_berangkat_id', 'maskapai_pulang_id']);
        return response()->json($jadwal);
    }

    public function edit($id)
    {
        $jadwal = KeberangkatanJemaah::with(['keberangkatan', 'paketUmrah'])->findOrFail($id);
        if ($jadwal->keberangkatan->status != 'pendaftaran') {
            abort(403);
        }
        $html = view('home.keberangkatan-jemaah._edit_form', compact('jadwal'))->render();
        return response()->json(['html' => $html]);
    }

    public function update(Request $request, $id)
    {
        $jadwal = KeberangkatanJemaah::findOrFail($id);
        if ($jadwal->keberangkatan->status != 'pendaftaran') {
            return response()->json(['message' => 'Tidak bisa edit'], 403);
        }

        $request->validate([
            'paket_umrah_id' => 'required|exists:paket_umrah,id',
            'keberangkatan_id' => 'required|exists:keberangkatan,id'
        ]);

        $jadwal->update($request->only(['paket_umrah_id', 'keberangkatan_id']));
        return response()->json(['message' => 'Jadwal berhasil diupdate']);
    }

    public function destroy($id)
    {
        $jadwal = KeberangkatanJemaah::findOrFail($id);
        if ($jadwal->keberangkatan->status != 'pendaftaran') {
            return response()->json(['message' => 'Tidak bisa hapus'], 403);
        }
        $jadwal->delete();
        return response()->json(['message' => 'Jadwal dihapus']);
    }
}
