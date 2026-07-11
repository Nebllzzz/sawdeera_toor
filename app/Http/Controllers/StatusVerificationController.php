<?php

namespace App\Http\Controllers;

use App\Models\KeberangkatanJemaah;
use App\Models\Pembayaran;

class StatusVerificationController extends Controller
{
    private const BASE_DOCUMENTS = [
        'ktp', 'paspor', 'visa', 'vaksin', 'kartu_keluarga', 'foto_4x6',
    ];

    private const MARRIED_DOCUMENTS = [
        'ktp', 'paspor', 'visa', 'vaksin', 'kartu_keluarga', 'buku_nikah', 'foto_4x6',
    ];

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
            'profile' => $this->profileStatus($jemaah),
            'package' => $pengajuan
                ? $this->status('verified', 'Terverifikasi', 'Paket dan jadwal keberangkatan telah dipilih.')
                : $this->status('waiting', 'Menunggu', 'Anda belum memilih paket keberangkatan.'),
            'documents' => $this->documentStatus($documents, $jemaah),
            'payment' => $this->paymentStatus($payment),
        ];
        $complete = collect($steps)->every(fn ($step) => $step['state'] === 'verified');
        $steps['finish'] = $complete
            ? $this->status('verified', 'Selesai', 'Seluruh proses pendaftaran telah selesai.')
            : $this->status('waiting', 'Menunggu', 'Selesaikan seluruh cabang verifikasi terlebih dahulu.');

        return view('home.status-verifikasi.index', compact(
            'user', 'jemaah', 'pengajuan', 'payment', 'documents', 'steps', 'complete'
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
        $rejected = $docs->filter(fn ($doc) => $doc?->status === 'ditolak')->sortByDesc('verified_at')->first();
        if ($rejected) {
            return $this->status('rejected', 'Perlu Perbaikan', 'Terdapat dokumen yang ditolak.', $rejected->verified_at, $rejected->keterangan_penolakan);
        }
        if ($docs->filter(fn ($doc) => $doc?->status === 'diverifikasi')->count() === count($requiredDocuments)) {
            return $this->status('verified', 'Terverifikasi', 'Seluruh dokumen pendukung telah diverifikasi.', $docs->max('verified_at'));
        }
        if ($docs->filter(fn ($doc) => $doc?->status === 'diproses')->isNotEmpty()) {
            return $this->status('processing', 'Sedang Diverifikasi', 'Dokumen persyaratan sedang diperiksa.');
        }
        return $this->status('waiting', 'Belum Lengkap', 'Unggah '.count($requiredDocuments).' dokumen pendukung yang diwajibkan.');
    }

    private function paymentStatus(?Pembayaran $payment): array
    {
        if (!$payment) {
            return $this->status('waiting', 'Menunggu', 'Rencana pembayaran belum tersedia.');
        }
        return match ($payment->status) {
            'diverifikasi' => $this->status('verified', 'Lunas', 'Seluruh tahap pembayaran telah diverifikasi.', $payment->updated_at),
            'diproses' => $this->status('processing', 'Sedang Diverifikasi', 'Bukti pembayaran sedang diperiksa admin.'),
            'ditolak' => $this->status('rejected', 'Perlu Perbaikan', 'Terdapat bukti pembayaran yang ditolak.', $payment->updated_at, $payment->tahapan->firstWhere('status', 'ditolak')?->keterangan_penolakan),
            default => $this->status('waiting', 'Belum Lunas', 'Selesaikan pembayaran sesuai rencana tagihan.'),
        };
    }

    private function status(string $state, string $label, string $description, $date = null, ?string $note = null): array
    {
        return compact('state', 'label', 'description', 'date', 'note');
    }

    private function requiredDocumentsFor($jemaah): array
    {
        return $jemaah?->status_pernikahan === 'menikah'
            ? self::MARRIED_DOCUMENTS
            : self::BASE_DOCUMENTS;
    }
}
