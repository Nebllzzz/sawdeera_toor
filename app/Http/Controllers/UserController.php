<?php

namespace App\Http\Controllers;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{

public function index()
{
    return view('home.user.index');
}

public function data(Request $request)
{
    if(request()->ajax()){

        $auth = auth()->id();

        $query = User::query()
            ->where('id','!=',1)
            ->where('id','!=',$auth)
            ->where('role', 'operator')
            ->orderby('created_at', 'desc');

        return DataTables::of($query)

        ->addIndexColumn()

        ->addColumn('roles', function ($row) {

            if($row->role == 'admin'){
                return "<span class='badge badge-danger'>Pimpinan</span>";
            }

            if($row->role == 'operator'){
                return "<span class='badge badge-info'>Admin</span>";
            }
        })

        ->addColumn('statusActivity', function ($row) {

            if($row->status == 'aktif'){
                return "<span class='badge badge-success'>Aktif</span>";
            }

            if($row->status == 'proses'){
                return "<span class='badge badge-warning'>Proses</span>";
            }

            return "<span class='badge badge-danger'>Tidak Aktif</span>";
        })

        ->addColumn('action', function ($row) {

            $updateButton = "
            <a href='javascript:void(0)'
            class='btn btn-icon btn-bg-warning btn-active-color-light btn-sm me-1 editUser'
            data-id='{$row->id}'
            data-name='{$row->name}'
            data-email='{$row->email}'
            data-role='{$row->role}'
            data-status='{$row->status}'
            style='background:#FF9F43'>
                <i class='bi bi-pencil-square text-white'></i>
            </a>
            ";

            $deleteButton = "
            <a href='javascript:void(0)'
            class='btn btn-icon btn-danger btn-active-color-light btn-sm deleteUser'
            data-id='{$row->id}'>
                <i class='bi bi-trash text-white'></i>
            </a>
            ";

            return $updateButton.$deleteButton;
        })

        ->rawColumns([
            'roles',
            'statusActivity',
            'action'
        ])

        ->make(true);
    }

    return view('home.user.index');
}

public function store(Request $request)
{
    $validateData = $request->validate([
        'name'=>'required|min:3',
        'email'=>'required|email|unique:users,email',
        'password'=>'required|min:6|confirmed',
        'role'=>'required',
        'status'=>'required'
    ]);

    $validateData['password'] = Hash::make($validateData['password']);

    $user = User::create($validateData);

    return response()->json([
        'success'=>true,
        'message'=>'User berhasil ditambahkan',
        'data'=>$user
    ]);
}

public function update(Request $request,$id)
{
    $user = User::findOrFail($id);

    $validateData = $request->validate([
        'name'=>'required|min:3',
        'email'=>'required|email',
        'role'=>'required',
        'status'=>'required'
    ]);

    if($request->password){
        $request->validate([
            'password'=>'min:6|confirmed'
        ]);

        $validateData['password'] = Hash::make($request->password);
    }

    $user->update($validateData);

    return response()->json([
        'success'=>true,
        'message'=>'User berhasil diupdate'
    ]);
}

public function destroy($id)
{
    User::find($id)->delete();

    return response()->json([
        'success'=>true,
        'message'=>'User berhasil dihapus'
    ]);
}

}
