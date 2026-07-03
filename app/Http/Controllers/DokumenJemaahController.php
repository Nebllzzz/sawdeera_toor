<?php

namespace App\Http\Controllers;

use App\Models\DataJemaah;
use App\Models\DokumenJemaah;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\User;
use App\Notifications\DocumentUploadedToAdmin;
use App\Notifications\DocumentStatusUpdatedToJemaah;

class DokumenJemaahController extends Controller
{
    public function indexDokumen()
    {
        $jemaah = auth()->user()->jemaah;

        $docs = DokumenJemaah::where('jemaah_id', $jemaah->id)->get()
            ->keyBy('jenis_dokumen');

        return view('home.dokumen.index', [
            'data' => $docs
        ]);
    }

    public function uploadDokumen(Request $r)
    {
        $r->validate([
            'jenis_dokumen' => 'required',
            'file' => 'required|file|mimes:png,jpg,jpeg,pdf|max:2048'
        ]);

        $jemaah = auth()->user()->jemaah;

        $path = $r->file('file')->store('dokumen', 'public');

        $doc = DokumenJemaah::updateOrCreate(
            [
                'jemaah_id' => $jemaah->id,
                'jenis_dokumen' => $r->jenis_dokumen
            ],
            [
                'file_path' => $path,
                'status' => 'diproses',
                'keterangan_penolakan' => null
            ]
        );

        // notify admins about uploaded document
        $admins = User::where('role', 'admin')->get();
        $data = [
            'title' => 'Upload Dokumen',
            'message' => "{$jemaah->user->name} mengupload dokumen: {$r->jenis_dokumen}",
            'dokumen_id' => $doc->id,
        ];
        foreach ($admins as $admin) {
            $admin->notify(new DocumentUploadedToAdmin($data));
        }

        return back()->with('success', 'Dokumen berhasil diupload');
    }

    public function index()
    {
        return view('home.dokumen.admin');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {

            $user = auth()->user();

            $query = DataJemaah::with([
                'user',
                'dokumen'
            ])
                ->when($user->role === 'operator', function ($q) use ($user) {

                    $q->where('operator_id', $user->id);
                });

            return DataTables::of($query)

                ->addIndexColumn()

                ->addColumn('nama', fn($r) => $r->user?->name ?? '-')

                ->addColumn('nik', fn($r) => $r->nik ?? '-')

                ->addColumn('ktp', fn($r) => $this->btnDok($r, 'ktp'))

                ->addColumn('paspor', fn($r) => $this->btnDok($r, 'paspor'))

                ->addColumn('visa', fn($r) => $this->btnDok($r, 'visa'))

                ->addColumn('vaksin', fn($r) => $this->btnDok($r, 'vaksin'))

                ->rawColumns(['ktp', 'paspor', 'visa', 'vaksin'])

                ->make(true);
        }
    }

    public function show($id)
    {
        return DokumenJemaah::findOrFail($id);
    }

    public function approve($id)
    {
        $doc = DokumenJemaah::findOrFail($id);
        $doc->update([
            'status' => 'diverifikasi',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'keterangan_penolakan' => null
        ]);

        // notify the jemaah
        if ($doc->jemaah && $doc->jemaah->user) {
            $user = $doc->jemaah->user;
            $data = [
                'title' => 'Status Dokumen',
                'message' => "Dokumen {$doc->jenis_dokumen} Anda telah diverifikasi",
                'dokumen_id' => $doc->id,
            ];
            $user->notify(new DocumentStatusUpdatedToJemaah($data));
        }

        return response()->json(['success' => true]);
    }

    public function reject(Request $r, $id)
    {
        $doc = DokumenJemaah::findOrFail($id);
        $doc->update([
            'status' => 'ditolak',
            'keterangan_penolakan' => $r->alasan
        ]);

        if ($doc->jemaah && $doc->jemaah->user) {
            $user = $doc->jemaah->user;
            $data = [
                'title' => 'Status Dokumen',
                'message' => "Dokumen {$doc->jenis_dokumen} Anda ditolak: {$r->alasan}",
                'dokumen_id' => $doc->id,
            ];
            $user->notify(new DocumentStatusUpdatedToJemaah($data));
        }

        return response()->json(['success' => true]);
    }

    private function btnDok($row, $type)
    {
        $doc = $row->dokumen->where('jenis_dokumen', $type)->first();

        if (!$doc) {
            return '-';
        }

        return "<a href='javascript:void(0)' onclick='openModal({$doc->id})'>Lihat Detail</a>";
    }
}
