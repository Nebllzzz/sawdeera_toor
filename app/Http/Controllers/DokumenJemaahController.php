<?php

namespace App\Http\Controllers;

use App\Models\DataJemaah;
use App\Models\DokumenJemaah;
use App\Models\KeberangkatanJemaah;
use App\Models\User;
use App\Notifications\DocumentStatusUpdatedToJemaah;
use App\Notifications\DocumentUploadedToAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class DokumenJemaahController extends Controller
{
    public const DOCUMENTS = [
        'paspor' => ['label' => 'Paspor', 'icon' => 'fa-passport', 'color' => 'blue'],
        'ktp' => ['label' => 'KTP', 'icon' => 'fa-id-card', 'color' => 'green'],
        'kartu_keluarga' => ['label' => 'Kartu Keluarga', 'icon' => 'fa-users', 'color' => 'purple'],
        'buku_nikah' => ['label' => 'Buku Nikah', 'icon' => 'fa-book', 'color' => 'brown'],
        'visa' => ['label' => 'Visa', 'icon' => 'fa-stamp', 'color' => 'indigo'],
        'vaksin' => ['label' => 'Sertifikat Vaksin', 'icon' => 'fa-syringe', 'color' => 'teal'],
        'foto_4x6' => ['label' => 'Foto Jemaah 4×6', 'icon' => 'fa-portrait', 'color' => 'orange'],
    ];

    public function indexDokumen()
    {
        abort_unless(auth()->user()->role === 'jemaah', 403);
        $jemaah = auth()->user()->jemaah;
        $docs = $jemaah
            ? DokumenJemaah::where('jemaah_id', $jemaah->id)->get()->keyBy('jenis_dokumen')
            : collect();
        $hasDeparture = $jemaah && KeberangkatanJemaah::where('jemaah_id', $jemaah->id)
            ->whereIn('status', KeberangkatanJemaah::STATUSES)->exists();
        $hasRegistration = $this->hasCompletedRegistration($jemaah);
        $documentTypes = $this->documentTypesFor($jemaah);
        $visibleDocs = $this->visibleDocuments($docs, $documentTypes);

        return view('home.dokumen.index', [
            'data' => $visibleDocs,
            'documentTypes' => $documentTypes,
            'summary' => $this->summary($visibleDocs, $jemaah),
            'hasDeparture' => $hasDeparture,
            'hasRegistration' => $hasRegistration,
        ]);
    }

    public function uploadDokumen(Request $request)
    {
        abort_unless(auth()->user()->role === 'jemaah', 403);
        $data = $request->validate([
            'jenis_dokumen' => ['required', Rule::in(array_keys(self::DOCUMENTS))],
            'file' => [
                'required', 'file', 'max:5120',
                $request->jenis_dokumen === 'foto_4x6' ? 'mimes:jpg,jpeg,png' : 'mimes:png,jpg,jpeg,pdf',
            ],
        ]);
        $jemaah = auth()->user()->jemaah;
        abort_unless($jemaah, 422, 'Lengkapi data pendaftaran terlebih dahulu.');
        abort_unless(
            KeberangkatanJemaah::where('jemaah_id', $jemaah->id)->whereIn('status', KeberangkatanJemaah::STATUSES)->exists(),
            422,
            'Pilih paket dan keberangkatan terlebih dahulu.'
        );
        abort_unless(
            $this->hasCompletedRegistration($jemaah),
            422,
            'Isi data diri pada menu Pendaftaran Saya terlebih dahulu.'
        );
        abort_unless(
            array_key_exists($data['jenis_dokumen'], $this->documentTypesFor($jemaah)),
            422,
            'Dokumen ini tidak diperlukan untuk status pernikahan Anda.'
        );

        $existing = DokumenJemaah::where('jemaah_id', $jemaah->id)
            ->where('jenis_dokumen', $data['jenis_dokumen'])->first();
        if ($existing?->status === 'diverifikasi') {
            return back()->with('error', 'Dokumen yang sudah diverifikasi tidak dapat diganti.');
        }

        $path = $request->file('file')->store('dokumen/'.$jemaah->id, 'public');
        if ($existing?->file_path) {
            Storage::disk('public')->delete($existing->file_path);
        }
        $doc = DokumenJemaah::updateOrCreate(
            ['jemaah_id' => $jemaah->id, 'jenis_dokumen' => $data['jenis_dokumen']],
            [
                'file_path' => $path, 'status' => 'diproses',
                'keterangan_penolakan' => null, 'verified_by' => null, 'verified_at' => null,
            ]
        );

        $label = self::DOCUMENTS[$data['jenis_dokumen']]['label'];
        foreach (User::whereIn('role', ['admin', 'operator'])->get() as $admin) {
            $admin->notify(new DocumentUploadedToAdmin([
                'title' => 'Dokumen Baru Diunggah',
                'message' => "{$jemaah->user->name} mengunggah {$label}.",
                'dokumen_id' => $doc->id,
                'url' => "/admin/dokumen/{$jemaah->id}",
            ]));
        }

        return back()
            ->with('success', "{$label} berhasil diunggah dan menunggu verifikasi.")
            ->with('berhasil', "{$label} berhasil diunggah dan menunggu verifikasi.");
    }

    public function index()
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator']), 403);
        return view('home.dokumen.admin');
    }

    public function data(Request $request)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator']), 403);
        $user = auth()->user();
        $query = DataJemaah::with(['user', 'dokumen'])
            ->when($user->role === 'operator', fn ($q) => $q->where('operator_id', $user->id))
            ->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama', fn ($r) => e($r->user?->name ?? '-'))
            ->addColumn('email', fn ($r) => e($r->user?->email ?? '-'))
            ->addColumn('kelengkapan', function ($r) {
                $required = array_keys($this->documentTypesFor($r));
                $uploaded = $r->dokumen
                    ->whereIn('jenis_dokumen', $required)
                    ->whereIn('status', ['diproses', 'diverifikasi', 'ditolak'])
                    ->count();

                return $uploaded.'/'.count($required).' dokumen';
            })
            ->addColumn('status_dokumen', fn ($r) => $this->documentStatusBadge($r))
            ->addColumn('action', fn ($r) => '<a href="/admin/dokumen/'.$r->id.'" class="btn btn-sm btn-primary"><i class="fas fa-eye mr-1"></i> Detail</a>')
            ->rawColumns(['status_dokumen', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator']), 403);
        $jemaah = DataJemaah::with(['user', 'dokumen.verifier'])->findOrFail($id);
        if (auth()->user()->role === 'operator' && $jemaah->operator_id !== auth()->id()) {
            abort(403);
        }
        $docs = $jemaah->dokumen->keyBy('jenis_dokumen');
        $documentTypes = $this->documentTypesFor($jemaah);
        $visibleDocs = $this->visibleDocuments($docs, $documentTypes);

        return view('home.dokumen.detail', [
            'jemaah' => $jemaah,
            'data' => $visibleDocs,
            'documentTypes' => $documentTypes,
            'summary' => $this->summary($visibleDocs, $jemaah),
        ]);
    }

    public function approve($id)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator']), 403);
        $doc = DokumenJemaah::with('jemaah.user')->findOrFail($id);
        $doc->update([
            'status' => 'diverifikasi', 'verified_by' => auth()->id(),
            'verified_at' => now(), 'keterangan_penolakan' => null,
        ]);
        $label = self::DOCUMENTS[$doc->jenis_dokumen]['label'] ?? $doc->jenis_dokumen;
        $doc->jemaah->user->notify(new DocumentStatusUpdatedToJemaah([
            'title' => 'Dokumen Diverifikasi',
            'message' => "{$label} Anda telah diverifikasi.",
            'dokumen_id' => $doc->id, 'url' => '/dokumen',
        ]));

        return response()->json(['success' => true, 'message' => "{$label} berhasil diverifikasi."]);
    }

    public function reject(Request $request, $id)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'operator']), 403);
        $data = $request->validate(['alasan' => 'required|string|max:1500']);
        $doc = DokumenJemaah::with('jemaah.user')->findOrFail($id);
        $doc->update([
            'status' => 'ditolak', 'keterangan_penolakan' => $data['alasan'],
            'verified_by' => auth()->id(), 'verified_at' => now(),
        ]);
        $label = self::DOCUMENTS[$doc->jenis_dokumen]['label'] ?? $doc->jenis_dokumen;
        $doc->jemaah->user->notify(new DocumentStatusUpdatedToJemaah([
            'title' => 'Dokumen Perlu Diperbaiki',
            'message' => "{$label} ditolak: {$data['alasan']}",
            'dokumen_id' => $doc->id, 'url' => '/dokumen',
        ]));

        return response()->json(['success' => true, 'message' => "{$label} ditolak dan jemaah telah diberi notifikasi."]);
    }

    private function summary($docs, ?DataJemaah $jemaah = null): array
    {
        $required = array_keys($this->documentTypesFor($jemaah));
        $all = collect($required)->map(fn ($type) => $docs->get($type));
        $rejected = $all->filter(fn ($doc) => $doc?->status === 'ditolak');
        $processing = $all->filter(fn ($doc) => $doc?->status === 'diproses');
        $verified = $all->filter(fn ($doc) => $doc?->status === 'diverifikasi');

        if ($rejected->isNotEmpty()) {
            return ['status' => 'ditolak', 'label' => 'Perlu Perbaikan', 'text' => 'Terdapat dokumen yang perlu diperbaiki.', 'latest' => $rejected->sortByDesc('verified_at')->first()];
        }
        if ($verified->count() === count($required)) {
            return ['status' => 'diverifikasi', 'label' => 'Terverifikasi', 'text' => 'Seluruh dokumen telah diverifikasi.', 'latest' => null];
        }
        if ($processing->isNotEmpty()) {
            return ['status' => 'diproses', 'label' => 'Dalam Proses', 'text' => 'Dokumen Anda sedang diperiksa admin.', 'latest' => null];
        }
        return ['status' => 'belum_lengkap', 'label' => 'Belum Lengkap', 'text' => 'Lengkapi seluruh dokumen pendukung.', 'latest' => null];
    }

    private function hasCompletedRegistration(?DataJemaah $jemaah): bool
    {
        return in_array($jemaah?->status_data, ['menunggu_verifikasi', 'terverifikasi'], true);
    }

    private function documentTypesFor(?DataJemaah $jemaah): array
    {
        $documents = self::DOCUMENTS;

        if ($jemaah?->status_pernikahan !== 'menikah') {
            unset($documents['buku_nikah']);
        }

        return $documents;
    }

    private function visibleDocuments($docs, array $documentTypes)
    {
        $allowedTypes = array_keys($documentTypes);

        return collect($docs)
            ->filter(fn ($doc, $type) => in_array($type, $allowedTypes, true));
    }

    private function documentStatusBadge(DataJemaah $jemaah): string
    {
        $required = array_keys($this->documentTypesFor($jemaah));
        $docs = $jemaah->dokumen->whereIn('jenis_dokumen', $required);

        if ($docs->where('status', 'ditolak')->isNotEmpty()) {
            return '<span class="badge badge-danger">Perlu Revisi</span>';
        }

        if ($docs->where('status', 'diverifikasi')->count() === count($required)) {
            return '<span class="badge badge-success">Terverifikasi</span>';
        }

        if ($docs->whereIn('status', ['diproses', 'diverifikasi'])->isNotEmpty()) {
            return '<span class="badge badge-warning">Sudah Upload</span>';
        }

        return '<span class="badge badge-secondary">Belum Upload</span>';
    }
}
