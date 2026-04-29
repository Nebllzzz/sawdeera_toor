<?php

namespace App\Http\Controllers;

use App\Models\PaketUmrah;
use App\Models\Hotel;
use App\Models\PaketFasilitas;
use App\Models\PaketProgram;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PaketUmrahController extends Controller
{

    public function index()
    {
        $hotelMakkah = Hotel::where('lokasi', 'mekkah')->get();
        $hotelMadinah = Hotel::where('lokasi', 'madinah')->get();

        return view('home.paket_umrah.index', compact(
            'hotelMakkah',
            'hotelMadinah'
        ));
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {

            $query = PaketUmrah::with([
                'hotelMakkah',
                'hotelMadinah'
            ]);

            return DataTables::of($query)

                ->addIndexColumn()

                ->addColumn('durasiLabel', function ($row) {
                    return $row->durasi . " Hari";
                })

                ->addColumn('hotelMakkah', function ($row) {
                    return $row->hotelMakkah->nama ?? '-';
                })

                ->addColumn('hotelMadinah', function ($row) {
                    return $row->hotelMadinah->nama ?? '-';
                })

                ->addColumn('hargaLabel', function ($row) {
                    return "Rp " . number_format($row->harga, 0, ',', '.');
                })

                ->addColumn('statusLabel', function ($row) {

                    if ($row->is_active) {
                        return "<span class='badge badge-success'>Aktif</span>";
                    }

                    return "<span class='badge badge-secondary'>Nonaktif</span>";
                })

                ->addColumn('action', function ($row) {

                    $edit = "
                    <a href='javascript:void(0)'
                    class='btn btn-warning btn-sm editPaket w-100'
                    data-id='{$row->id}'
                    data-nama='{$row->nama_paket}'
                    data-durasi='{$row->durasi}'
                    data-makkah='{$row->hotel_makkah_id}'
                    data-madinah='{$row->hotel_madinah_id}'
                    data-harga='{$row->harga}'
                    data-deskripsi='{$row->deskripsi}'
                    data-status='{$row->is_active}'
                    style='background:#FF9F43'>
                    <i class='bi bi-pencil-square text-white'></i>
                    </a>";

                    $delete = "
                    <a href='javascript:void(0)'
                    class='btn btn-danger btn-sm deletePaket w-100'
                    data-id='{$row->id}'>
                    <i class='bi bi-trash text-white'></i>
                    </a>";

                    $fasilitas = "
                    <a href='javascript:void(0)'
                    class='btn btn-info btn-sm fasilitasPaket w-100'
                    data-id='{$row->id}'>
                    <i class='bi bi-list'></i>
                    </a>";

                    $program = "
                    <a href='javascript:void(0)'
                    class='btn btn-primary btn-sm programPaket w-100'
                    data-id='{$row->id}'>
                    <i class='bi bi-calendar'></i>
                    </a>";

                    return "

                        <div class='row g-1' style='width:100%'>

                        <div class='col-6 p-1'>$edit</div>
                        <div class='col-6 p-1'>$delete</div>

                        <div class='col-6 p-1'>$fasilitas</div>
                        <div class='col-6 p-1'>$program</div>

                        </div>

                    ";

                })

                ->rawColumns([
                    'statusLabel',
                    'action'
                ])

                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_paket' => 'required',
            'durasi' => 'required|integer',
            'hotel_makkah_id' => 'required',
            'hotel_madinah_id' => 'required',
            'harga' => 'required',
            'deskripsi' => 'nullable',
            'is_active' => 'required'
        ]);

        PaketUmrah::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Paket berhasil ditambahkan'
        ]);
    }

    public function update(Request $request, $id)
    {
        $paket = PaketUmrah::findOrFail($id);

        $data = $request->validate([
            'nama_paket' => 'required',
            'durasi' => 'required|integer',
            'hotel_makkah_id' => 'required',
            'hotel_madinah_id' => 'required',
            'harga' => 'required',
            'deskripsi' => 'nullable',
            'is_active' => 'required'
        ]);

        $paket->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Paket berhasil diupdate'
        ]);
    }

    public function destroy($id)
    {
        PaketUmrah::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paket berhasil dihapus'
        ]);
    }

    public function getFasilitas($paket_id)
    {
        $data = PaketFasilitas::where('paket_id', $paket_id)->get();

        return response()->json($data);
    }

    public function getProgram($paket_id)
    {
        $data = PaketProgram::where('paket_id', $paket_id)
            ->orderBy('hari')
            ->get();

        return response()->json($data);
    }

    public function storeFasilitas(Request $request)
    {
        PaketFasilitas::where('paket_id',$request->paket_id)->delete();

        foreach($request->nama as $nama){

            PaketFasilitas::create([
                'paket_id'=>$request->paket_id,
                'nama'=>$nama
            ]);
        }

        return response()->json([
            'success'=>true,
            'message'=>'Fasilitas berhasil disimpan'
        ]);
    }

    public function storeProgram(Request $request)
    {
        PaketProgram::where('paket_id',$request->paket_id)->delete();

        foreach($request->hari as $i=>$hari){

            PaketProgram::create([
                'paket_id'=>$request->paket_id,
                'hari'=>$hari,
                'deskripsi'=>$request->deskripsi[$i]
            ]);
        }

        return response()->json([
            'success'=>true,
            'message'=>'Program berhasil disimpan'
        ]);
    }
}
