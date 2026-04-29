<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DataJemaah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class JemaahController extends Controller
{

    public function index()
    {
        return view('home.jemaah.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {

            $query = User::leftJoin('data_jemaah', 'users.id', '=', 'data_jemaah.user_id')
                ->leftJoin('users as operator', 'data_jemaah.operator_id', '=', 'operator.id')
                ->where('users.role', 'jemaah')
                ->select(
                    'users.*',
                    'data_jemaah.nik',
                    'data_jemaah.no_telepon',
                    'operator.name as operator_name'
                );

            return DataTables::of($query)

                ->addIndexColumn()

                ->addColumn('nama', fn($r) => $r->name)
                ->addColumn('email', fn($r) => $r->email)
                ->addColumn('nik', fn($r) => $r->jemaah->nik ?? '-')
                ->addColumn('telepon', fn($r) => $r->jemaah->no_telepon ?? '-')

                ->addColumn('statusActivity', function ($r) {
                    return $r->status == 'aktif'
                        ? "<span class='badge badge-success'>Aktif</span>"
                        : ($r->status == 'proses'
                            ? "<span class='badge badge-warning'>Proses</span>"
                            : "<span class='badge badge-danger'>Tidak Aktif</span>");
                })

                ->addColumn('operator', function ($r) {
                    return $r->operator_name ?? 'Belum dihandle';
                })

                ->addColumn('action', function ($row) {

                    return "
                    <div class='row g-1'>
                        <div class='col-6 p-1'>
                            <button class='btn btn-warning btn-sm editJemaah w-100'
                                data-id='{$row->id}'>
                                <i class='bi bi-pencil-square text-white'></i>
                            </button>
                        </div>

                        <div class='col-6 p-1'>
                            <button class='btn btn-danger btn-sm deleteJemaah w-100'
                                data-id='{$row->id}'>
                                <i class='bi bi-trash text-white'></i>
                            </button>
                        </div>

                        <div class='col-6 p-1'>
                            <button class='btn btn-success btn-sm toggleStatus w-100'
                                data-id='{$row->id}'>
                                <i class='bi bi-toggle-on text-white'></i>
                            </button>
                        </div>

                        <div class='col-6 p-1'>
                            <button class='btn btn-info btn-sm detailJemaah w-100'
                                data-id='{$row->id}'>
                                <i class='bi bi-eye text-white'></i>
                            </button>
                        </div>
                    </div>";
                })

                ->rawColumns(['statusActivity', 'action'])
                ->make(true);
        }
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',

            'nik' => 'required|unique:data_jemaah,nik',
            'jenis_kelamin' => 'required',
            'no_telepon' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'alamat' => 'required',
            'status_pernikahan' => 'required'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'jemaah',
            'status' => 'proses'
        ]);

        DataJemaah::create([
            'user_id' => $user->id,
            'operator_id' => auth()->id(),

            'nik' => $data['nik'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'no_telepon' => $data['no_telepon'],
            'tempat_lahir' => $data['tempat_lahir'],
            'tanggal_lahir' => $data['tanggal_lahir'],
            'alamat' => $data['alamat'],
            'pekerjaan' => $r->pekerjaan,
            'status_pernikahan' => $data['status_pernikahan']
        ]);

        return response()->json(['message' => 'Berhasil tambah jemaah']);
    }

    public function update(Request $r, $id)
    {
        $user = User::findOrFail($id);

        $data = $r->validate([
            'name' => 'required',
            'email' => 'required|email',

            'nik' => 'required',
            'jenis_kelamin' => 'required',
            'no_telepon' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'alamat' => 'required',
            'status_pernikahan' => 'required'
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email']
        ]);

        if ($r->password) {
            $user->update([
                'password' => Hash::make($r->password)
            ]);
        }

        $user->jemaah()->update([
            'nik' => $data['nik'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'no_telepon' => $data['no_telepon'],
            'tempat_lahir' => $data['tempat_lahir'],
            'tanggal_lahir' => $data['tanggal_lahir'],
            'alamat' => $data['alamat'],
            'pekerjaan' => $r->pekerjaan,
            'status_pernikahan' => $data['status_pernikahan']
        ]);

        return response()->json(['message' => 'Berhasil update']);
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'Berhasil hapus']);
    }

    public function toggleStatus($id)
    {
        $u = User::with('jemaah')->findOrFail($id);

        // Toggle status
        $u->status = match ($u->status) {
            'aktif' => 'tidak_aktif',
            'tidak_aktif' => 'aktif',
            default => 'aktif'
        };

        $u->save();

        // 🔥 Isi operator_id (user login)
        if ($u->jemaah) {
            $u->jemaah->update([
                'operator_id' => auth()->id()
            ]);
        }

        return response()->json(['message' => 'Status diubah & operator tercatat']);
    }

    public function detail($id)
    {
        return User::with('jemaah')->findOrFail($id);
    }

    public function updateProfile(Request $r)
    {
        $user = auth()->user();

        $data = $r->validate([
            'name' => 'required',
            'email' => 'required|email',

            'nik' => 'required',
            'no_telepon' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'status_pernikahan' => 'required',
            'alamat' => 'required'
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email']
        ]);

        if ($r->password) {
            $user->update([
                'password' => bcrypt($r->password)
            ]);
        }

        $user->jemaah()->update($data);

        return back()->with('berhasil', 'Profil berhasil diupdate');
    }
}
