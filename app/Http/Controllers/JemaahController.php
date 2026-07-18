<?php

namespace App\Http\Controllers;

use App\Models\DataJemaah;
use App\Models\JemaahVerificationLog;
use App\Models\KeberangkatanJemaah;
use App\Models\User;
use App\Notifications\DocumentStatusUpdatedToJemaah;
use App\Notifications\RegisterToAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class JemaahController extends Controller
{
    public function index()
    {
        return redirect('/jemaah/data-verifikasi');
    }

    public function accountVerification()
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator'], true), 403);
        $stats = [
            'menunggu' => User::where('role', 'jemaah')->where('status', 'proses')->count(),
            'aktif' => User::where('role', 'jemaah')->where('status', 'aktif')->count(),
            'tidak_aktif' => User::where('role', 'jemaah')->where('status', 'tidak_aktif')->count(),
        ];

        return view('home.jemaah.account-verification', compact('stats'));
    }

    public function accountVerificationData(Request $request)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator'], true), 403);
        $query = User::with('jemaah')
            ->where('role', 'jemaah')
            ->select('users.*')
            ->orderBy('users.created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama', fn ($row) => $row->name ? e($row->name) : '-')
            ->addColumn('telepon', fn ($row) => e($row->jemaah?->no_telepon ?? '-'))
            ->addColumn('tanggal_registrasi', fn ($row) => $row->created_at?->translatedFormat('d M Y H:i') ?? '-')
            ->addColumn('status_badge', fn ($row) => $this->accountStatusBadge($row->status))
            ->addColumn('action', fn ($row) => '<a href="/jemaah/registrasi/'.$row->id.'" class="btn btn-sm btn-light-primary"><i class="far fa-eye mr-1"></i> Lihat Detail</a>')
            ->rawColumns(['nama', 'status_badge', 'action'])
            ->make(true);
    }

    public function accountVerificationShow($id)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator'], true), 403);
        $user = User::with(['jemaah', 'jemaahVerificationLogs.actor'])
            ->where('role', 'jemaah')
            ->findOrFail($id);
        $logs = $user->jemaahVerificationLogs()
            ->where('type', JemaahVerificationLog::TYPE_ACCOUNT)
            ->with('actor')
            ->latest()
            ->get();

        return view('home.jemaah.account-detail', compact('user', 'logs'));
    }

    public function dataVerification()
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator'], true), 403);
        $stats = [
            'menunggu' => DataJemaah::where('status_data', 'menunggu_verifikasi')->count(),
            'terverifikasi' => DataJemaah::where('status_data', 'terverifikasi')->count(),
            'perlu_perbaikan' => DataJemaah::where('status_data', 'perlu_perbaikan')->count(),
        ];

        return view('home.jemaah.data-verification', compact('stats'));
    }

    public function dataVerificationShow($id)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator'], true), 403);
        $user = User::with(['jemaah.verificationLogs.actor'])
            ->where('role', 'jemaah')
            ->findOrFail($id);
        abort_unless($user->jemaah, 404, 'Data jemaah belum tersedia.');
        $logs = $user->jemaah->verificationLogs()
            ->where('type', JemaahVerificationLog::TYPE_DATA)
            ->with('actor')
            ->latest()
            ->get();

        return view('home.jemaah.data-detail', compact('user', 'logs'));
    }

    public function data(Request $request)
    {
        $user = auth()->user();
        $query = User::query()
            ->leftJoin('data_jemaah', 'users.id', '=', 'data_jemaah.user_id')
            ->leftJoin('users as operator', 'data_jemaah.operator_id', '=', 'operator.id')
            ->where('users.role', 'jemaah')
            ->when($user->role !== 'admin', fn ($q) => $q->where(
                fn ($q) => $q->where('data_jemaah.operator_id', $user->id)
                    ->orWhereNull('data_jemaah.operator_id')
            ))
            ->select([
                'users.id', 'users.name', 'users.email', 'users.status',
                'data_jemaah.nik', 'data_jemaah.no_telepon', 'data_jemaah.status_data',
                'data_jemaah.operator_id', 'operator.name as operator_name',
            ])
            ->orderBy('users.created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama', fn ($r) => e($r->name ?: '-'))
            ->addColumn('email', fn ($r) => e($r->email ?: '-'))
            ->addColumn('nik', fn ($r) => e($r->nik ?: '-'))
            ->addColumn('telepon', fn ($r) => e($r->no_telepon ?: '-'))
            ->addColumn('statusActivity', fn ($r) => $this->accountStatusBadge($r->status))
            ->addColumn('statusData', fn ($r) => $this->dataStatusBadge($r->status_data))
            ->addColumn('operator', fn ($r) => e($r->operator_name ?: 'Belum ditangani'))
            ->addColumn('action', fn ($row) => view('home.jemaah.partials.actions', ['row' => $row])->render())
            ->rawColumns(['statusActivity', 'statusData', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $data = $this->validateJemaah($request);

        DB::transaction(function () use ($request, $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'jemaah',
                'status' => 'proses',
            ]);
            $user->jemaah()->create($this->jemaahPayload($request) + [
                'operator_id' => auth()->id(),
                'status_data' => 'menunggu_verifikasi',
            ]);
            $this->storeUploads($request, $user->jemaah);
        });

        return response()->json(['message' => 'Data jemaah berhasil ditambahkan.']);
    }

    public function update(Request $request, $id)
    {
        $user = User::where('role', 'jemaah')->findOrFail($id);
        $data = $this->validateJemaah($request, $user);

        DB::transaction(function () use ($request, $data, $user) {
            $user->update(['name' => $data['name'], 'email' => $data['email']]);
            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }
            $jemaah = $user->jemaah()->updateOrCreate(
                ['user_id' => $user->id],
                $this->jemaahPayload($request) + ['operator_id' => auth()->id()]
            );
            $this->storeUploads($request, $jemaah);
        });

        return response()->json(['message' => 'Data jemaah berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        User::where('role', 'jemaah')->findOrFail($id)->delete();
        return response()->json(['message' => 'Data jemaah berhasil dihapus.']);
    }

    public function toggleStatus($id)
    {
        $user = User::with('jemaah')->where('role', 'jemaah')->findOrFail($id);
        $before = $user->status;
        $user->status = $user->status === 'aktif' ? 'tidak_aktif' : 'aktif';
        $user->save();
        $user->jemaah?->update(['operator_id' => auth()->id()]);
        $this->writeVerificationLog($user, JemaahVerificationLog::TYPE_ACCOUNT, $before, $user->status, $user->status === 'aktif' ? 'Akun jemaah diaktifkan.' : 'Akun jemaah dinonaktifkan.');

        return response()->json(['message' => "Status akun diubah menjadi {$user->status}."]);
    }

    public function toggleDataStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status_data' => ['required', Rule::in(['menunggu_verifikasi', 'terverifikasi', 'perlu_perbaikan'])],
            'catatan_admin' => 'nullable|string|max:2000|required_if:status_data,perlu_perbaikan',
        ]);
        $user = User::where('role', 'jemaah')->findOrFail($id);
        $jemaah = $user->jemaah()
            ->updateOrCreate(['user_id' => $id]);
        $before = $jemaah->status_data;
        $jemaah->update([
            ...$data,
            'operator_id' => auth()->id(),
            'diverifikasi_pada' => in_array($data['status_data'], ['terverifikasi', 'perlu_perbaikan'])
                ? now() : null,
        ]);

        $verified = $data['status_data'] === 'terverifikasi';
        $needsRevision = $data['status_data'] === 'perlu_perbaikan';
        $user->notify(new DocumentStatusUpdatedToJemaah([
            'title' => $verified ? 'Data Diri Terverifikasi' : ($needsRevision ? 'Data Diri Perlu Diperbaiki' : 'Status Data Diri Diperbarui'),
            'message' => $verified
                ? 'Data diri Anda telah diverifikasi admin.'
                : ($needsRevision ? 'Data diri perlu diperbaiki: '.$data['catatan_admin'] : 'Status pengajuan data diri Anda telah diperbarui.'),
            'jemaah_id' => $jemaah->id,
            'url' => '/pendaftaran-saya',
        ]));
        $this->writeVerificationLog($user, JemaahVerificationLog::TYPE_DATA, $before, $data['status_data'], $data['catatan_admin'] ?? ($verified ? 'Data jemaah diverifikasi.' : 'Status data jemaah diperbarui.'), $jemaah);

        return response()->json(['message' => 'Status data jemaah berhasil diperbarui.']);
    }

    public function detail($id)
    {
        return User::with('jemaah')->where('role', 'jemaah')->findOrFail($id);
    }

    public function registration()
    {
        abort_unless(auth()->user()->role === 'jemaah', 403);

        return view('home.profil.index', [
            'hasDeparture' => $this->hasDeparture(auth()->user()),
        ]);
    }

    public function profile()
    {
        return view('home.account-profile.index');
    }

    public function updateAccountProfile(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'no_telepon' => 'nullable|string|max:20',
            'password' => 'nullable|confirmed|min:6',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
        ]);

        DB::transaction(function () use ($request, $data, $user) {
            $user->update(['name' => $data['name']]);
            if (!empty($data['password'])) {
                $user->update(['password' => Hash::make($data['password'])]);
            }
            if ($user->role === 'jemaah') {
                $payload = [];
                if ($request->has('no_telepon')) {
                    $payload['no_telepon'] = $data['no_telepon'] ?: ($user->jemaah?->no_telepon ?? '-');
                }

                $jemaah = $user->jemaah()->updateOrCreate(
                    ['user_id' => $user->id],
                    $payload ?: ['no_telepon' => $user->jemaah?->no_telepon ?? '-']
                );
                if ($request->hasFile('foto_profil')) {
                    if ($jemaah->foto_profil) {
                        Storage::disk('public')->delete($jemaah->foto_profil);
                    }
                    $jemaah->update([
                        'foto_profil' => $request->file('foto_profil')->store('jemaah/profil', 'public'),
                    ]);
                }
            }
        });

        return back()->with('berhasil', 'Profil berhasil diperbarui.');
    }

    public function updateRegistration(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->role === 'jemaah', 403);
        abort_unless($this->hasDeparture($user), 422, 'Pilih paket dan keberangkatan terlebih dahulu.');

        $request->validate([
            'nik' => ['required', 'string', 'max:30', Rule::unique('data_jemaah')->ignore($user->jemaah?->id)],
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => ['required', Rule::in(['laki_laki', 'perempuan'])],
            'status_pernikahan' => ['required', Rule::in(['menikah', 'belum_menikah'])],
            'pekerjaan' => 'required|string|max:150',
            'alamat' => 'required|string|max:1000',
            'kontak_darurat' => 'required|string|max:20',
            'hubungan_kontak_darurat' => 'required|string|max:100',
            'nomor_paspor' => ['required', 'string', 'max:50', Rule::unique('data_jemaah')->ignore($user->jemaah?->id)],
            'tanggal_terbit_paspor' => 'required|date',
            'tanggal_kedaluwarsa_paspor' => 'required|date|after:tanggal_terbit_paspor',
            'tempat_penerbitan_paspor' => 'required|string|max:150',
            'golongan_darah' => ['required', Rule::in(['A', 'B', 'AB', 'O'])],
            'riwayat_penyakit' => 'nullable|string|max:2000',
            'alergi' => 'nullable|string|max:2000',
            'scan_paspor' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
        ]);

        $jemaah = DB::transaction(function () use ($request, $user) {
            $jemaah = $user->jemaah()->updateOrCreate(
                ['user_id' => $user->id],
                $this->jemaahPayload($request) + [
                    'status_data' => 'menunggu_verifikasi',
                    'catatan_admin' => null,
                    'diverifikasi_pada' => null,
                ]
            );

            $this->storeUploads($request, $jemaah);

            return $jemaah->refresh();
        });

        foreach (User::whereIn('role', ['admin', 'operator'])->get() as $admin) {
            $admin->notify(new RegisterToAdmin([
                'title' => 'Pengajuan Data Diri Baru',
                'message' => "{$user->name} mengajukan data diri untuk diverifikasi.",
                'jemaah_id' => $jemaah->id,
                'url' => '/jemaah',
            ]));
        }

        return back()->with('berhasil', 'Pendaftaran berhasil disimpan dan diajukan untuk diverifikasi.');
    }

    private function hasDeparture(User $user): bool
    {
        return $user->jemaah
            && KeberangkatanJemaah::where('jemaah_id', $user->jemaah->id)
                ->whereIn('status', KeberangkatanJemaah::STATUSES)->exists();
    }

    private function validateJemaah(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', 'min:6'],
            'nik' => ['nullable', 'string', 'max:30', Rule::unique('data_jemaah')->ignore($user?->jemaah?->id)],
            'no_telepon' => 'required|string|max:20',
            'jenis_kelamin' => ['nullable', Rule::in(['laki_laki', 'perempuan'])],
            'status_pernikahan' => ['nullable', Rule::in(['menikah', 'belum_menikah'])],
            'tanggal_lahir' => 'nullable|date',
            'tanggal_terbit_paspor' => 'nullable|date',
            'tanggal_kedaluwarsa_paspor' => 'nullable|date|after:tanggal_terbit_paspor',
            'scan_paspor' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'foto_profil' => 'nullable|image|max:3072',
        ]);
    }

    private function jemaahPayload(Request $request): array
    {
        return $request->only([
            'nik', 'jenis_kelamin', 'no_telepon', 'tempat_lahir', 'tanggal_lahir',
            'alamat', 'pekerjaan', 'status_pernikahan', 'kontak_darurat',
            'hubungan_kontak_darurat', 'nomor_paspor', 'tanggal_terbit_paspor',
            'tanggal_kedaluwarsa_paspor', 'tempat_penerbitan_paspor',
            'golongan_darah', 'riwayat_penyakit', 'alergi',
        ]);
    }

    private function storeUploads(Request $request, DataJemaah $jemaah): void
    {
        $paths = [];
        if ($request->hasFile('scan_paspor')) {
            if ($jemaah->scan_paspor) {
                Storage::disk('public')->delete($jemaah->scan_paspor);
            }
            $paths['scan_paspor'] = $request->file('scan_paspor')->store('jemaah/paspor', 'public');
        }
        if ($request->hasFile('foto_profil')) {
            if ($jemaah->foto_profil) {
                Storage::disk('public')->delete($jemaah->foto_profil);
            }
            $paths['foto_profil'] = $request->file('foto_profil')->store('jemaah/profil', 'public');
        }
        if ($paths) {
            $jemaah->update($paths);
        }
    }

    private function accountStatusBadge(?string $status): string
    {
        $map = [
            'aktif' => ['success', 'Aktif'],
            'proses' => ['warning', 'Menunggu Aktivasi'],
            'tidak_aktif' => ['danger', 'Tidak Aktif'],
        ];
        [$color, $label] = $map[$status] ?? ['secondary', '-'];
        return "<span class=\"badge badge-{$color}\">{$label}</span>";
    }

    private function dataStatusBadge(?string $status): string
    {
        $map = [
            'belum_lengkap' => ['secondary', 'Belum Lengkap'],
            'menunggu_verifikasi' => ['warning', 'Menunggu Verifikasi'],
            'terverifikasi' => ['success', 'Terverifikasi'],
            'perlu_perbaikan' => ['danger', 'Perlu Perbaikan'],
        ];
        [$color, $label] = $map[$status] ?? ['secondary', 'Belum Lengkap'];
        return "<span class=\"badge badge-{$color}\">{$label}</span>";
    }

    private function writeVerificationLog(User $user, string $type, ?string $before, ?string $after, ?string $note = null, ?DataJemaah $jemaah = null): void
    {
        JemaahVerificationLog::create([
            'user_id' => $user->id,
            'jemaah_id' => $jemaah?->id ?? $user->jemaah?->id,
            'actor_id' => auth()->id(),
            'type' => $type,
            'status_before' => $before,
            'status_after' => $after,
            'note' => $note,
        ]);
    }
}
