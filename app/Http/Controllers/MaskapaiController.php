<?php

namespace App\Http\Controllers;

use App\Models\Maskapai;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MaskapaiController extends Controller
{

    public function index()
    {
        return view('home.maskapai.index');
    }

    public function data(Request $request)
    {

        if($request->ajax()){

            $query = Maskapai::query();

            return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('statusLabel',function($row){

                if($row->is_active){

                    return "<span class='badge badge-success'>Aktif</span>";

                }

                return "<span class='badge badge-secondary'>Nonaktif</span>";

            })

            ->addColumn('action',function($row){

                $edit = "
                <button
                class='btn btn-warning btn-sm editMaskapai'
                data-id='{$row->id}'
                data-code='{$row->airline_code}'
                data-icao='{$row->airline_icao_code}'
                data-nama='{$row->nama}'
                data-negara='{$row->asal_negara}'
                data-status='{$row->is_active}'
                >
                <i class='bi bi-pencil-square'></i>
                </button>
                ";

                $delete = "
                <button
                class='btn btn-danger btn-sm deleteMaskapai'
                data-id='{$row->id}'
                >
                <i class='bi bi-trash'></i>
                </button>
                ";

                return $edit.$delete;

            })

            ->rawColumns(['statusLabel','action'])

            ->make(true);

        }

    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'airline_code'=>'required',
            'airline_icao_code'=>'nullable',
            'nama'=>'required',
            'asal_negara'=>'required',
            'is_active'=>'required'
        ]);

        Maskapai::create($data);

        return response()->json([
            'success'=>true,
            'message'=>'Maskapai berhasil ditambahkan'
        ]);

    }

    public function update(Request $request,$id)
    {

        $maskapai = Maskapai::findOrFail($id);

        $data = $request->validate([
            'airline_code'=>'required',
            'airline_icao_code'=>'nullable',
            'nama'=>'required',
            'asal_negara'=>'required',
            'is_active'=>'required'
        ]);

        $maskapai->update($data);

        return response()->json([
            'success'=>true,
            'message'=>'Maskapai berhasil diupdate'
        ]);

    }

    public function destroy($id)
    {

        Maskapai::findOrFail($id)->delete();

        return response()->json([
            'success'=>true,
            'message'=>'Maskapai berhasil dihapus'
        ]);

    }

}
