<?php

namespace App\Http\Controllers;

use App\Models\TourLeader;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TourLeaderController extends Controller
{

    public function index()
    {
        return view('home.tour_leader.index');
    }

    public function data(Request $request)
    {

        if($request->ajax()){

            $query = TourLeader::query()->orderby('created_at', 'desc');

            return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('jenisKelaminLabel',function($row){

                if($row->jenis_kelamin == 'laki_laki'){
                    return "Laki-laki";
                }

                return "Perempuan";

            })

            ->addColumn('action',function($row){

                $edit = "
                <button
                class='btn btn-warning btn-sm editLeader'
                data-id='{$row->id}'
                data-nama='{$row->nama}'
                data-telepon='{$row->no_telepon}'
                data-email='{$row->email}'
                data-alamat='{$row->alamat}'
                data-jk='{$row->jenis_kelamin}'
                >
                <i class='bi bi-pencil-square'></i>
                </button>";

                $delete = "
                <button
                class='btn btn-danger btn-sm deleteLeader'
                data-id='{$row->id}'
                >
                <i class='bi bi-trash'></i>
                </button>";

                return $edit.$delete;

            })

            ->rawColumns(['action'])

            ->make(true);

        }

    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'nama'=>'required',
            'no_telepon'=>'required',
            'email'=>'nullable|email',
            'alamat'=>'required',
            'jenis_kelamin'=>'required'
        ]);

        TourLeader::create($data);

        return response()->json([
            'success'=>true,
            'message'=>'Tour Leader berhasil ditambahkan'
        ]);

    }

    public function update(Request $request,$id)
    {

        $leader = TourLeader::findOrFail($id);

        $data = $request->validate([
            'nama'=>'required',
            'no_telepon'=>'required',
            'email'=>'nullable|email',
            'alamat'=>'required',
            'jenis_kelamin'=>'required'
        ]);

        $leader->update($data);

        return response()->json([
            'success'=>true,
            'message'=>'Tour Leader berhasil diupdate'
        ]);

    }

    public function destroy($id)
    {

        TourLeader::findOrFail($id)->delete();

        return response()->json([
            'success'=>true,
            'message'=>'Tour Leader berhasil dihapus'
        ]);

    }

}
