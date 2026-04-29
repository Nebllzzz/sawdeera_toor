<?php

namespace App\Http\Controllers;

use App\Models\DataJemaah;
use App\Models\DokumenJemaah;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DokumenJemaahController extends Controller
{
    public function indexDokumen()
    {
        $jemaah = auth()->user()->jemaah;

        $docs = DokumenJemaah::where('jemaah_id', $jemaah->id)->get()
            ->keyBy('jenis_dokumen');

        return view('home.dokumen.index', [
            'data' => $docs
        ]);
    }

    public function uploadDokumen(Request $r)
    {
        $r->validate([
            'jenis_dokumen' => 'required',
            'file' => 'required|file|mimes:png,jpg,jpeg,pdf|max:2048'
        ]);

        $jemaah = auth()->user()->jemaah;

        $path = $r->file('file')->store('dokumen', 'public');

        DokumenJemaah::updateOrCreate(
            [
                'jemaah_id' => $jemaah->id,
                'jenis_dokumen' => $r->jenis_dokumen
            ],
            [
                'file_path' => $path,
                'status' => 'diproses',
                'keterangan_penolakan' => null
            ]
        );

        return back()->with('success', 'Dokumen berhasil diupload');
    }

    public function index()
    {
        return view('home.dokumen.admin');
    }

    public function data(Request $request)
    {
        if($request->ajax()){

            $query = DataJemaah::with(['user','dokumen']);

            return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('nama', fn($r)=>$r->user->name)
            ->addColumn('nik', fn($r)=>$r->nik)

            ->addColumn('ktp', fn($r)=>$this->btnDok($r,'ktp'))
            ->addColumn('paspor', fn($r)=>$this->btnDok($r,'paspor'))
            ->addColumn('visa', fn($r)=>$this->btnDok($r,'visa'))
            ->addColumn('vaksin', fn($r)=>$this->btnDok($r,'vaksin'))

            ->rawColumns(['ktp','paspor','visa','vaksin'])
            ->make(true);
        }
    }

    public function show($id)
    {
        return DokumenJemaah::findOrFail($id);
    }

    public function approve($id)
    {
        DokumenJemaah::findOrFail($id)->update([
            'status'=>'diverifikasi',
            'verified_by'=>auth()->id(),
            'verified_at'=>now(),
            'keterangan_penolakan'=>null
        ]);

        return response()->json(['success'=>true]);
    }

    public function reject(Request $r,$id)
    {
        DokumenJemaah::findOrFail($id)->update([
            'status'=>'ditolak',
            'keterangan_penolakan'=>$r->alasan
        ]);

        return response()->json(['success'=>true]);
    }

    private function btnDok($row,$type)
    {
        $doc = $row->dokumen->where('jenis_dokumen',$type)->first();

        if(!$doc){
            return '-';
        }

        return "<a href='javascript:void(0)' onclick='openModal({$doc->id})'>Lihat Detail</a>";
    }
}
