<?php

namespace App\Http\Controllers;

use App\Models\Keberangkatan;
use App\Models\KeberangkatanJemaah;
use App\Models\Maskapai;
use App\Models\TourLeader;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class KeberangkatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('home.keberangkatan.index');
    }

    public function detail($id)
    {
        return view('home.keberangkatan.detail',compact('id'));
    }

    public function detail_data($id)
    {

        $data = Keberangkatan::with([
            'maskapaiBerangkat',
            'maskapaiPulang',
            'leader'
        ])
            ->withCount('jemaah')
            ->findOrFail($id);

        return response()->json($data);
    }

    public function list(Request $request)
    {

        $q = Keberangkatan::with([
            'maskapaiBerangkat',
            'maskapaiPulang',
            'leader'
        ])
            ->withCount(['jemaah as total_jemaah' => function ($q) {
                $q->where('status', 'aktif');
            }]);

        if ($request->search) {
            $q->whereDate('tanggal_keberangkatan', 'like', '%' . $request->search . '%');
        }

        $data = $q->orderBy('tanggal_keberangkatan', 'desc')->get();

        return response()->json($data);
    }

    public function jemaah(Request $request)
    {

        $data = KeberangkatanJemaah::with([
            'jemaah',
            'paketUmrah'
        ])
            ->where('keberangkatan_id', $request->keberangkatan_id);

        return DataTables::of($data)

            ->addIndexColumn()

            ->addColumn('nama', function ($row) {
                return $row->jemaah->user->name ?? '-';
            })

            ->addColumn('nik', function ($row) {
                return $row->jemaah->nik ?? '-';
            })

            ->addColumn('paket', function ($row) {
                return $row->paketUmrah->nama_paket ?? '-';
            })

            ->addColumn('status', function ($row) {

                if ($row->status == 'aktif') {
                    return '<span class="badge badge-success">Aktif</span>';
                }

                if ($row->status == 'cancel') {
                    return '<span class="badge badge-danger">Cancel</span>';
                }

                if ($row->status == 'reschedule') {
                    return '<span class="badge badge-warning">Reschedule</span>';
                }
            })

            ->rawColumns(['status'])

            ->make(true);
    }

    public function getFormData()
    {

        $maskapai = Maskapai::where('is_active', 1)->get();

        $leader = TourLeader::get();

        return response()->json([
            "maskapai" => $maskapai,
            "leader" => $leader
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([

            'maskapai_berangkat_id' => 'required',
            'maskapai_pulang_id' => 'required',

            'tanggal_keberangkatan' => 'required',
            'jam_berangkat' => 'required',
            'jam_tiba' => 'required',

            'tanggal_pulang' => 'required',
            'jam_pulang' => 'required',
            'jam_tiba_pulang' => 'required'

        ]);

        $data = Keberangkatan::create([

            'maskapai_berangkat_id' => $request->maskapai_berangkat_id,
            'maskapai_pulang_id' => $request->maskapai_pulang_id,

            'tour_leader_id' => $request->tour_leader_id,

            'tanggal_keberangkatan' => $request->tanggal_keberangkatan,
            'jam_berangkat' => $request->jam_berangkat,
            'jam_tiba' => $request->jam_tiba,

            'tanggal_pulang' => $request->tanggal_pulang,
            'jam_pulang' => $request->jam_pulang,
            'jam_tiba_pulang' => $request->jam_tiba_pulang

        ]);

        return response()->json([
            "success" => true,
            "message" => "Keberangkatan berhasil dibuat",
            "data" => $data
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Keberangkatan $keberangkatan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Keberangkatan $keberangkatan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Keberangkatan $keberangkatan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {

        Keberangkatan::findOrFail($id)->delete();

        return response()->json([
            "success" => true
        ]);
    }
}
