<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\RegisterToAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function showlogin()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        } else {
            return view('login');
        }
    }

    public function actionlogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 1. CEK USER ADA ATAU TIDAK
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->with('gagal', 'Email tidak ditemukan');
        }

        // 2. CEK STATUS
        if ($user->status === 'proses') {
            return back()->with('gagal', 'Akun kamu masih menunggu verifikasi admin');
        }

        if ($user->status === 'tidak_aktif') {
            return back()->with('gagal', 'akun anda ditolak, silahkan hubungi admin untuk info selanjutnya');
        }

        // 3. CEK PASSWORD + LOGIN
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
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
                'no_telepon' => 'required|string|max:20',
                'password' => 'required|min:6|confirmed',
            ], [
                'email.unique' => 'Email sudah terdaftar',
                'password.confirmed' => 'Konfirmasi password tidak cocok',
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
                'no_telepon' => $request->no_telepon,
                'status_data' => 'belum_lengkap',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // notify all admins & operators about new registration
            $recipients = User::whereIn('role', ['admin', 'operator'])->get();
            $data = [
                'title' => 'Registrasi Baru',
                'message' => "Pengguna {$user->name} ({$user->email}) mendaftar dan menunggu verifikasi",
                'user_id' => $user->id,
            ];

            foreach ($recipients as $recipient) {
                $recipient->notify(new RegisterToAdmin($data));
            }

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
