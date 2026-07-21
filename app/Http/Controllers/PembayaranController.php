<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\PembayaranTahapan;
use App\Models\User;
use App\Notifications\PaymentStatusUpdatedToJemaah;
use App\Notifications\PaymentUploadedToAdmin;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PembayaranController extends Controller
{
    public function pemabayanIndex()
    {
        abort_unless(auth()->user()->role === 'jemaah', 403);
        $jemaah = auth()->user()->jemaah;
        $pembayaran = $jemaah ? Pembayaran::with([
            'tahapan.verifier', 'pengajuan.paketUmrah.hotelMakkah',
            'pengajuan.paketUmrah.hotelMadinah', 'pengajuan.keberangkatan.maskapaiBerangkat',
        ])->where('jemaah_id', $jemaah->id)->whereNotNull('keberangkatan_jemaah_id')->latest()->first() : null;

        return view('home.pemabayan.index', compact('pembayaran'));
    }

    public function downloadInvoice()
    {
        abort_unless(auth()->user()->role === 'jemaah', 403);

        $jemaah = auth()->user()->jemaah;
        $pembayaran = $jemaah ? Pembayaran::with([
            'jemaah.user', 'tahapan.verifier', 'pengajuan.paketUmrah',
            'pengajuan.keberangkatan',
        ])->where('jemaah_id', $jemaah->id)
            ->whereNotNull('keberangkatan_jemaah_id')
            ->latest()
            ->first() : null;

        abort_unless(
            $pembayaran?->isInvoiceAvailable(),
            403,
            'Invoice hanya tersedia setelah seluruh tahap pembayaran diunggah dan diverifikasi lunas.'
        );

        $invoiceNumber = 'SWD/INV/'.($pembayaran->created_at?->format('Y') ?? now()->format('Y')).'/'.str_pad((string) $pembayaran->id, 6, '0', STR_PAD_LEFT);
        $paidAt = $pembayaran->tahapan->max('verified_at') ?? $pembayaran->updated_at;
        $filename = 'invoice-sawdeera-'.str_pad((string) $pembayaran->id, 6, '0', STR_PAD_LEFT).'.pdf';

        return Pdf::loadView('home.pemabayan.invoice', compact(
            'pembayaran',
            'invoiceNumber',
            'paidAt'
        ))->setPaper('a4')->download($filename);
    }

    public function pemabayanUpload(Request $request)
    {
        abort_unless(auth()->user()->role === 'jemaah', 403);
        $data = $request->validate([
            'tahap_id' => 'required|exists:pembayaran_tahapan,id',
            'metode_pembayaran' => 'required|string|max:100',
            'bukti_pembayaran' => 'required|file|mimes:png,jpg,jpeg,pdf|max:5120',
            'catatan_jemaah' => 'nullable|string|max:1000',
        ]);

        $tahap = PembayaranTahapan::with('pembayaran.jemaah.user')->findOrFail($data['tahap_id']);
        abort_unless($tahap->pembayaran->jemaah_id === auth()->user()->jemaah?->id, 403);
        abort_if($tahap->status === 'diverifikasi', 422, 'Tahap pembayaran ini sudah diverifikasi.');

        $current = $tahap->pembayaran->tahapan()
            ->where('status', '!=', 'diverifikasi')->orderBy('urutan')->first();
        abort_unless($current?->id === $tahap->id, 422, 'Selesaikan tahap pembayaran sebelumnya terlebih dahulu.');
        abort_if($tahap->status === 'diproses', 422, 'Bukti pembayaran sedang diverifikasi admin.');

        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran/termin', 'public');
        $tahap->update([
            'metode_pembayaran' => $data['metode_pembayaran'],
            'bukti_pembayaran' => $path,
            'catatan_jemaah' => $data['catatan_jemaah'] ?? null,
            'status' => 'diproses',
            'keterangan_penolakan' => null,
            'uploaded_at' => now(),
            'verified_by' => null,
            'verified_at' => null,
        ]);
        $tahap->pembayaran->update(['status' => 'diproses']);

        foreach (User::whereIn('role', ['admin', 'operator'])->get() as $admin) {
            $admin->notify(new PaymentUploadedToAdmin([
                'title' => 'Bukti Pembayaran Baru',
                'message' => auth()->user()->name." mengunggah bukti {$tahap->nama_tahap} senilai Rp ".number_format($tahap->nominal, 0, ',', '.'),
                'pembayaran_id' => $tahap->pembayaran_id,
                'tahap_id' => $tahap->id,
                'url' => "/admin/pemabayan/{$tahap->pembayaran_id}/detail",
            ]));
        }

        return back()->with('success', 'Bukti pembayaran dikirim dan menunggu verifikasi admin.');
    }

    public function pemabayanAdmin()
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator']), 403);

        return view('home.pemabayan.admin');
    }

    public function pemabayanAdminData(Request $request)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator']), 403);
        $user = auth()->user();
        $query = Pembayaran::with(['jemaah.user', 'pengajuan.paketUmrah', 'tahapan'])
            ->whereNotNull('keberangkatan_jemaah_id')
            ->when($user->role === 'operator', fn ($q) => $q->whereHas('jemaah', fn ($q) => $q->where('operator_id', $user->id)))
            ->select('pembayaran.*')
            ->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama', fn ($r) => e($r->jemaah?->user?->name ?? '-'))
            ->addColumn('paket', fn ($r) => e($r->pengajuan?->paketUmrah?->nama_paket ?? '-'))
            ->addColumn('skema', fn ($r) => $this->schemeLabel($r->jenis_pembayaran))
            ->addColumn('progress', function ($r) {
                $paid = $r->tahapan->where('status', 'diverifikasi')->count();

                return "{$paid}/{$r->jumlah_tahap} tahap";
            })
            ->addColumn('menunggu', fn ($r) => $r->tahapan->where('status', 'diproses')->count())
            ->addColumn('status_view', fn ($r) => $this->statusBadge($r->status))
            ->addColumn('action', fn ($r) => '<a class="btn btn-sm btn-primary" href="/admin/pemabayan/'.$r->id.'/detail"><i class="fas fa-eye mr-1"></i> Detail</a>')
            ->rawColumns(['status_view', 'action'])
            ->make(true);
    }

    public function pemabayanAdminDetail($id)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator']), 403);
        $pembayaran = Pembayaran::with([
            'jemaah.user', 'pengajuan.paketUmrah.hotelMakkah', 'pengajuan.paketUmrah.hotelMadinah',
            'pengajuan.keberangkatan.maskapaiBerangkat', 'tahapan.verifier',
        ])->findOrFail($id);

        return view('home.pemabayan.detail', compact('pembayaran'));
    }

    public function pemabayanAdminShow($id)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator']), 403);

        return PembayaranTahapan::with('pembayaran.jemaah.user')->findOrFail($id);
    }

    public function pemabayanAdminApprove($id)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator']), 403);
        $tahap = PembayaranTahapan::with('pembayaran.jemaah.user')->findOrFail($id);
        abort_unless($tahap->status === 'diproses', 422, 'Bukti tidak sedang menunggu verifikasi.');

        DB::transaction(function () use ($tahap) {
            $tahap->update([
                'status' => 'diverifikasi', 'verified_by' => auth()->id(),
                'verified_at' => now(), 'keterangan_penolakan' => null,
            ]);
            $payment = $tahap->pembayaran;
            $allPaid = ! $payment->tahapan()->where('status', '!=', 'diverifikasi')->exists();
            $payment->update(['status' => $allPaid ? 'diverifikasi' : 'belum_bayar']);
        });

        $tahap->pembayaran->jemaah->user->notify(new PaymentStatusUpdatedToJemaah([
            'title' => 'Pembayaran Diverifikasi',
            'message' => "{$tahap->nama_tahap} telah diverifikasi. ".($tahap->pembayaran->fresh()->status === 'diverifikasi' ? 'Seluruh tagihan Anda sudah lunas.' : 'Tahap berikutnya kini dapat dibayar.'),
            'pembayaran_id' => $tahap->pembayaran_id,
            'tahap_id' => $tahap->id,
            'url' => '/pemabayan',
        ]));

        return response()->json(['success' => true, 'message' => 'Pembayaran berhasil diverifikasi.']);
    }

    public function pemabayanAdminReject(Request $request, $id)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator']), 403);
        $data = $request->validate(['alasan' => 'required|string|max:1500']);
        $tahap = PembayaranTahapan::with('pembayaran.jemaah.user')->findOrFail($id);
        abort_unless($tahap->status === 'diproses', 422, 'Bukti tidak sedang menunggu verifikasi.');
        $tahap->update([
            'status' => 'ditolak', 'keterangan_penolakan' => $data['alasan'],
            'verified_by' => auth()->id(), 'verified_at' => now(),
        ]);
        $tahap->pembayaran->update(['status' => 'ditolak']);
        $tahap->pembayaran->jemaah->user->notify(new PaymentStatusUpdatedToJemaah([
            'title' => 'Pembayaran Perlu Diperbaiki',
            'message' => "{$tahap->nama_tahap} ditolak: {$data['alasan']}",
            'pembayaran_id' => $tahap->pembayaran_id,
            'tahap_id' => $tahap->id,
            'url' => '/pemabayan',
        ]));

        return response()->json(['success' => true, 'message' => 'Pembayaran ditolak dan jemaah telah diberi notifikasi.']);
    }

    private function schemeLabel(string $scheme): string
    {
        return [
            'sekali_bayar' => 'Satu Kali Bayar', 'cicilan_3_bulan' => '3 Kali Cicilan',
            'cicilan_6_bulan' => '6 Kali Cicilan', 'cicilan_12_bulan' => '12 Kali Cicilan',
        ][$scheme] ?? $scheme;
    }

    private function statusBadge(string $status): string
    {
        [$color, $label] = match ($status) {
            'diverifikasi' => ['success', 'Lunas'], 'diproses' => ['warning', 'Perlu Verifikasi'],
            'ditolak' => ['danger', 'Perlu Perbaikan'], default => ['secondary', 'Berjalan'],
        };

        return "<span class=\"badge badge-{$color}\">{$label}</span>";
    }
}
