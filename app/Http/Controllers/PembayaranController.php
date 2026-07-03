<?php

namespace App\Http\Controllers;

use App\Models\KeberangkatanJemaah;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Notifications\PaymentUploadedToAdmin;
use App\Notifications\PaymentStatusUpdatedToJemaah;

class PembayaranController extends Controller
{
    // use: pembayarans (tabel: pembayaran, FK: keberangkatan_id -> keberangkatan_jemaah.keberangkatan_id)

    // MENU JEMAah
    public function pemabayanIndex()
    {
        $user = auth()->user();

        // Pastikan hanya role jemaah yang bisa masuk halaman ini
        if (($user->role ?? null) !== 'jemaah') {
            abort(403);
        }

        // Jika user belum punya record jemaah, halaman tetap aman
        $jemaah = $user->jemaah;
        if (!$jemaah) {
            return view('home.pemabayan.index', [
                'keberangkatanJemaah' => null,
                'pembayaran' => null,
            ]);
        }

        $keberangkatanJemaah = KeberangkatanJemaah::with(['keberangkatan'])
            ->where('jemaah_id', $jemaah->id)
            ->first();

        $pembayaran = null;
        if ($keberangkatanJemaah) {
            $pembayaran = Pembayaran::where('keberangkatan_id', $keberangkatanJemaah->keberangkatan_id)
                ->where('jemaah_id', $jemaah->id)
                ->orderByDesc('id')
                ->first();
        }

        return view('home.pemabayan.index', [
            'keberangkatanJemaah' => $keberangkatanJemaah,
            'pembayaran' => $pembayaran,
        ]);
    }


    public function pemabayanUpload(Request $r)
    {
        $r->validate([
            'keberangkatan_id' => 'required|exists:keberangkatan,id',
            'jumlah' => 'required|numeric|min:0.01',
            'jenis_pembayaran' => 'required|in:dp,cicilan,pelunasan',
            'metode_pembayaran' => 'required|string|max:255',
            'bukti_pembayaran' => 'required|file|mimes:png,jpg,jpeg,pdf|max:2048',
        ]);

        $jemaah = auth()->user()->jemaah;

        // Pastikan jemaah punya keberangkatan (FK pembayaran.keberangkatan_id berasal dari keberangkatan_jemaah.keberangkatan_id)
        $keberangkatanJemaah = KeberangkatanJemaah::where([
            ['jemaah_id', '=', $jemaah->id],
            ['keberangkatan_id', '=', $r->keberangkatan_id],
        ])->first();

        if (!$keberangkatanJemaah) {
            return back()->with('error', 'Anda belum punya keberangkatan untuk upload pembayaran');
        }

        $path = $r->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');

        // Jika sudah pernah upload sebelumnya untuk keberangkatan_id & jemaah_id yang sama:
        // - status diproses/ditolak => update row tersebut (bukan create baru)
        // - status diverifikasi => seharusnya tidak masuk route ini, tapi tetap aman: block
        $existing = Pembayaran::where('keberangkatan_id', $r->keberangkatan_id)
            ->where('jemaah_id', $jemaah->id)
            ->orderByDesc('id')
            ->first();

        if ($existing && $existing->status === 'diverifikasi') {
            return back()->with('error', 'Pembayaran sudah diverifikasi. Upload/edit tidak diizinkan.');
        }

        if ($existing) {
            $existing->update([
                'jumlah' => $r->jumlah,
                'jenis_pembayaran' => $r->jenis_pembayaran,
                'metode_pembayaran' => $r->metode_pembayaran,
                'bukti_pembayaran' => $path,
                'status' => 'diproses',
                'keterangan_penolakan' => null,
            ]);
            $payment = $existing;
        } else {
            $payment = Pembayaran::create([
                'jemaah_id' => $jemaah->id,
                'keberangkatan_id' => $r->keberangkatan_id,
                'jumlah' => $r->jumlah,
                'jenis_pembayaran' => $r->jenis_pembayaran,
                'metode_pembayaran' => $r->metode_pembayaran,
                'bukti_pembayaran' => $path,
                'status' => 'diproses',
                'keterangan_penolakan' => null,
            ]);
        }

        // notify admins about uploaded payment
        $admins = User::where('role', 'admin')->get();
        $data = [
            'title' => 'Upload Pembayaran',
            'message' => "{$jemaah->user->name} mengupload pembayaran ({$r->jenis_pembayaran}) sejumlah {$r->jumlah}",
            'pembayaran_id' => $payment->id ?? null,
        ];
        foreach ($admins as $admin) {
            $admin->notify(new PaymentUploadedToAdmin($data));
        }

        return back()->with('success', 'Pembayaran berhasil diupload/diupdate, menunggu verifikasi admin.');
    }

    // MENU ADMIN/OPERATOR
    public function pemabayanAdmin()
    {
        return view('home.pemabayan.admin');
    }

    public function pemabayanAdminData(Request $request)
    {
        if ($request->ajax()) {

            $user = auth()->user();

            $query = Pembayaran::with([
                'jemaah.user'
            ])
                ->whereHas('jemaah', function ($q) use ($user) {

                    // kalau operator
                    if ($user->role === 'operator') {
                        $q->where('operator_id', $user->id);
                    }

                    // admin/pimpinan lihat semua
                })
                ->select('pembayaran.*');

            return DataTables::of($query)

                ->addIndexColumn()

                ->addColumn('nama', function ($r) {
                    return $r->jemaah?->user?->name ?? '-';
                })

                ->addColumn('nik', function ($r) {
                    return $r->jemaah?->nik ?? '-';
                })

                ->addColumn('bukti', fn($row) => $this->btnBukti($row))

                ->addColumn('action', fn($row) => $this->btnAction($row))

                ->rawColumns(['bukti', 'action'])

                ->make(true);
        }
    }

    public function pemabayanAdminShow($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        return response()->json($pembayaran);
    }

    public function pemabayanAdminApprove($id)
    {
        $p = Pembayaran::findOrFail($id);
        $p->update([
            'status' => 'diverifikasi',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'keterangan_penolakan' => null,
        ]);

        // notify jemaah
        if ($p->jemaah && $p->jemaah->user) {
            $user = $p->jemaah->user;
            $data = [
                'title' => 'Status Pembayaran',
                'message' => "Pembayaran Anda (ID: {$p->id}) telah diverifikasi",
                'pembayaran_id' => $p->id,
            ];
            $user->notify(new PaymentStatusUpdatedToJemaah($data));
        }

        return response()->json(['success' => true]);
    }

    public function pemabayanAdminReject(Request $r, $id)
    {
        $p = Pembayaran::findOrFail($id);
        $p->update([
            'status' => 'ditolak',
            'keterangan_penolakan' => $r->alasan,
        ]);

        if ($p->jemaah && $p->jemaah->user) {
            $user = $p->jemaah->user;
            $data = [
                'title' => 'Status Pembayaran',
                'message' => "Pembayaran Anda (ID: {$p->id}) ditolak: {$r->alasan}",
                'pembayaran_id' => $p->id,
            ];
            $user->notify(new PaymentStatusUpdatedToJemaah($data));
        }

        return response()->json(['success' => true]);
    }

    private function btnBukti($row)
    {
        if (!$row->bukti_pembayaran) return '-';
        return "<a href='javascript:void(0)' onclick='openModal({$row->id})'>Lihat Bukti</a>";
    }

    private function btnAction($row)
    {
        if ($row->status === 'diproses') {
            return "<button class='btn btn-danger btn-sm' onclick='showReject({$row->id})'>Tolak</button> <button class='btn btn-success btn-sm' onclick='approve({$row->id})'>Setujui</button>";
        }

        if ($row->status === 'diverifikasi') {
            return "<button class='btn btn-secondary btn-sm' disabled>Sudah Diverifikasi</button>";
        }

        return "<button class='btn btn-secondary btn-sm' disabled>Ditolak</button>";
    }
}
