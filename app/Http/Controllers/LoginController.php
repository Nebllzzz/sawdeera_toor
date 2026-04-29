<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function showlogin()
    {
        if(Auth::check()){
            return redirect('/dashboard');
        }else{
            return view('login');
        }
    }

    public function actionlogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 1. CEK USER ADA ATAU TIDAK
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('gagal', 'Email tidak ditemukan');
        }

        // 2. CEK STATUS
        if ($user->status === 'proses') {
            return back()->with('gagal', 'Akun kamu masih menunggu verifikasi admin');
        }

        if ($user->status === 'tidak_aktif') {
            return back()->with('gagal', 'Akun kamu tidak aktif');
        }

        // 3. CEK PASSWORD + LOGIN
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            return redirect('/dashboard');
        }

        // 4. PASSWORD SALAH
        return back()->with('gagal', 'Password salah');
    }
    
    public function register(Request $request)
    {
        try {
            // VALIDASI MANUAL (BIAR MASUK KE CATCH)
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|confirmed',

                'nik' => 'required|unique:data_jemaah,nik',
                'jenis_kelamin' => 'required',
                'no_telepon' => 'required',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required|date',
                'alamat' => 'required',
                'status_pernikahan' => 'required',
            ], [
                'email.unique' => 'Email sudah terdaftar',
                'nik.unique' => 'NIK sudah digunakan',
                'password.confirmed' => 'Konfirmasi password tidak cocok'
            ]);

            if ($validator->fails()) {
                return back()
                    ->withInput()
                    ->with('gagal', $validator->errors()->first());
            }

            DB::beginTransaction();

            // CREATE USER
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'jemaah',
                'status' => 'proses',
            ]);

            // CREATE JEMAAH
            DB::table('data_jemaah')->insert([
                'user_id' => $user->id,
                'nik' => $request->nik,
                'jenis_kelamin' => $request->jenis_kelamin,
                'no_telepon' => $request->no_telepon,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'pekerjaan' => $request->pekerjaan,
                'status_pernikahan' => $request->status_pernikahan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect('/login')
                ->with('berhasil', 'Registrasi berhasil, tunggu verifikasi admin untuk melanjutkan login!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('gagal', 'Terjadi kesalahan, coba lagi');
        }
    }

    public function actionlogout()
    {
        Auth::logout();
        return redirect('/login')->with('berhasil', 'Anda Berhasil Logout');
    }
}
