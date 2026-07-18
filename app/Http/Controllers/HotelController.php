<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class HotelController extends Controller
{

public function index()
{
    return view('home.hotel.index');
}

public function data(Request $request)
{
    if($request->ajax()){

        $query = Hotel::query()->orderby('created_at', 'desc');

        return DataTables::of($query)

        ->addIndexColumn()

        ->addColumn('lokasiLabel', function ($row){

            if($row->lokasi == 'mekkah'){
                return "<span class='badge badge-danger'>Mekkah</span>";
            }

            return "<span class='badge badge-success'>Madinah</span>";
        })

        ->addColumn('bintangLabel', function ($row){

            return str_repeat("⭐",$row->bintang);
        })

        ->addColumn('tipeLabel', function ($row){

            if($row->tipe_kamar == 'double'){
                return "<span class='badge badge-primary'>Double</span>";
            }

            if($row->tipe_kamar == 'triple'){
                return "<span class='badge badge-info'>Triple</span>";
            }

            return "<span class='badge badge-dark'>Quad</span>";
        })

        ->addColumn('action', function ($row){

            $edit = "
            <a href='javascript:void(0)'
            class='btn btn-warning btn-sm editHotel'
            data-id='{$row->id}'
            data-nama='{$row->nama}'
            data-lokasi='{$row->lokasi}'
            data-bintang='{$row->bintang}'
            data-tipe='{$row->tipe_kamar}'
            style='background:#FF9F43'>
            <i class='bi bi-pencil-square text-white'></i>
            </a>";

            $delete = "
            <a href='javascript:void(0)'
            class='btn btn-danger btn-sm deleteHotel'
            data-id='{$row->id}'>
            <i class='bi bi-trash text-white'></i>
            </a>";

            return $edit.$delete;
        })

        ->rawColumns([
            'lokasiLabel',
            'tipeLabel',
            'action'
        ])

        ->make(true);
    }
}

public function store(Request $request)
{
    $data = $request->validate([
        'nama'=>'required',
        'lokasi'=>'required',
        'bintang'=>'required|integer',
        'tipe_kamar'=>'required'
    ]);

    Hotel::create($data);

    return response()->json([
        'success'=>true,
        'message'=>'Hotel berhasil ditambahkan'
    ]);
}

public function update(Request $request,$id)
{
    $hotel = Hotel::findOrFail($id);

    $data = $request->validate([
        'nama'=>'required',
        'lokasi'=>'required',
        'bintang'=>'required|integer',
        'tipe_kamar'=>'required'
    ]);

    $hotel->update($data);

    return response()->json([
        'success'=>true,
        'message'=>'Hotel berhasil diupdate'
    ]);
}

public function destroy($id)
{
    Hotel::findOrFail($id)->delete();

    return response()->json([
        'success'=>true,
        'message'=>'Hotel berhasil dihapus'
    ]);
}

}
