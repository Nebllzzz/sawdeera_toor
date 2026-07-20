<?php

namespace App\Http\Controllers;

use App\Models\KeberangkatanJemaah;
use App\Models\Pembayaran;

class StatusVerificationController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->role === 'jemaah', 403);
        $user = auth()->user();
        $jemaah = $user->jemaah;
        $pengajuan = $jemaah ? KeberangkatanJemaah::with([
            'paketUmrah', 'keberangkatan', 'pembayaran.tahapan',
        ])->where('jemaah_id', $jemaah->id)->latest('id')->first() : null;
        $documents = $jemaah?->dokumen->keyBy('jenis_dokumen') ?? collect();
        $payment = $pengajuan?->pembayaran;

        $steps = [
            'account' => $this->status('verified', 'Selesai', 'Akun sudah terdaftar.', $user->created_at),
            'package' => $pengajuan
                ? $this->status('verified', 'Selesai', 'Paket umrah telah dipilih.')
                : $this->status('waiting', 'Belum Selesai', 'Anda belum memilih paket keberangkatan.'),
            'profile' => $this->profileStatus($jemaah),
            'documents' => $this->documentStatus($documents, $jemaah),
            'payment' => $this->paymentUploadStatus($payment),
            'approval' => $this->adminApprovalStatus($jemaah, $documents, $payment),
        ];
        $completedCount = collect($steps)->where('state', 'verified')->count();
        $progressPercent = (int) round(($completedCount / 6) * 100);
        $complete = $completedCount === 6;

        return view('home.status-verifikasi.index', compact(
            'user', 'jemaah', 'pengajuan', 'payment', 'documents', 'steps', 'complete', 'completedCount', 'progressPercent'
        ));
    }

    private function profileStatus($jemaah): array
    {
        return match ($jemaah?->status_data) {
            'terverifikasi' => $this->status('verified', 'Terverifikasi', 'Data pribadi dan identitas telah diverifikasi.', $jemaah?->diverifikasi_pada),
            'perlu_perbaikan' => $this->status('rejected', 'Perlu Perbaikan', 'Data pribadi perlu diperbaiki.', $jemaah?->diverifikasi_pada, $jemaah?->catatan_admin),
            'menunggu_verifikasi' => $this->status('processing', 'Sedang Diverifikasi', 'Data pribadi sedang diperiksa admin.'),
            default => $this->status('waiting', 'Belum Lengkap', 'Lengkapi data pada menu Pendaftaran Saya.'),
        };
    }

    private function documentStatus($documents, $jemaah): array
    {
        $requiredDocuments = $this->requiredDocumentsFor($jemaah);
        $docs = collect($requiredDocuments)->map(fn ($type) => $documents->get($type));
        $uploadedCount = $docs->filter(fn ($doc) => in_array($doc?->status, ['diproses', 'diverifikasi', 'ditolak'], true))->count();
        $rejected = $docs->filter(fn ($doc) => $doc?->status === 'ditolak')->sortByDesc('verified_at')->first();
        if ($rejected) {
            return $this->status('rejected', 'Perlu Perbaikan', 'Terdapat dokumen yang ditolak.', $rejected->verified_at, $rejected->keterangan_penolakan);
        }
        if ($docs->filter(fn ($doc) => $doc?->status === 'diverifikasi')->count() === count($requiredDocuments)) {
            return $this->status('verified', 'Selesai', 'Seluruh dokumen pendukung telah diverifikasi.', $docs->max('verified_at'));
        }
        if ($uploadedCount > 0) {
            return $this->status('processing', 'Sedang Diverifikasi', "{$uploadedCount} dari ".count($requiredDocuments).' dokumen sudah diunggah dan sedang diperiksa.');
        }

        return $this->status('waiting', 'Belum Selesai', 'Unggah '.count($requiredDocuments).' dokumen pendukung yang diwajibkan.');
    }

    private function paymentUploadStatus(?Pembayaran $payment): array
    {
        if (! $payment) {
            return $this->status('waiting', 'Belum Selesai', 'Rencana pembayaran belum tersedia.');
        }

        $steps = $payment->tahapan ?? collect();
        if ($steps->isNotEmpty() && $steps->every(fn ($step) => $step->status === 'diverifikasi')) {
            return $this->status('verified', 'Selesai', 'Seluruh tahap pembayaran telah diverifikasi.', $steps->max('verified_at'));
        }
        if ($rejected = $steps->firstWhere('status', 'ditolak')) {
            return $this->status('rejected', 'Perlu Perbaikan', 'Terdapat bukti pembayaran yang ditolak.', $rejected->verified_at, $rejected->keterangan_penolakan);
        }
        $processed = $steps->whereIn('status', ['diproses', 'diverifikasi'])->count();
        if ($processed > 0) {
            return $this->status('processing', 'Sedang Diverifikasi', "{$processed} dari {$steps->count()} tahap pembayaran sedang atau sudah diverifikasi.");
        }

        return $this->status('waiting', 'Belum Selesai', 'Unggah bukti pembayaran sesuai rencana tagihan.');
    }

    private function adminApprovalStatus($jemaah, $documents, ?Pembayaran $payment): array
    {
        $profile = $this->profileStatus($jemaah);
        $document = $this->documentStatus($documents, $jemaah);
        if (! $payment) {
            return $this->status('waiting', 'Belum Selesai', 'Pembayaran belum tersedia.');
        }

        return match ($payment->status) {
            'diverifikasi' => ($profile['state'] === 'verified' && $document['state'] === 'verified')
                ? $this->status('verified', 'Selesai', 'Data, dokumen, dan pembayaran telah disetujui admin.', $payment->updated_at)
                : $this->status('processing', 'Sedang Diverifikasi', 'Menunggu seluruh approval admin selesai.'),
            'diproses' => $this->status('processing', 'Sedang Diverifikasi', 'Bukti pembayaran sedang diperiksa admin.'),
            'ditolak' => $this->status('rejected', 'Perlu Perbaikan', 'Terdapat bukti pembayaran yang ditolak.', $payment->updated_at, $payment->tahapan->firstWhere('status', 'ditolak')?->keterangan_penolakan),
            default => $this->status('waiting', 'Belum Selesai', 'Menunggu verifikasi admin.'),
        };
    }

    private function status(string $state, string $label, string $description, $date = null, ?string $note = null): array
    {
        return compact('state', 'label', 'description', 'date', 'note');
    }

    private function requiredDocumentsFor($jemaah): array
    {
        return $jemaah?->requiredDocumentTypes() ?? \App\Models\DataJemaah::BASE_REQUIRED_DOCUMENTS;
    }
}
