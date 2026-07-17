<?php

namespace App\Http\Controllers;

use App\Models\Keberangkatan;
use App\Models\KeberangkatanJemaah;
use App\Models\KeberangkatanJemaahReschedule;
use App\Models\Maskapai;
use App\Models\PaketUmrah;
use App\Models\Pembayaran;
use App\Models\User;
use App\Notifications\PaymentUploadedToAdmin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KeberangkatanJemaahController extends Controller
{
    private const SCHEMES = [
        'sekali_bayar' => ['steps' => 1, 'minimum_days' => 0, 'label' => 'Satu Kali Bayar'],
        'cicilan_3_bulan' => ['steps' => 3, 'minimum_days' => 60, 'label' => '3 Bulan Cicilan'],
        'cicilan_6_bulan' => ['steps' => 6, 'minimum_days' => 150, 'label' => '6 Bulan Cicilan'],
        'cicilan_12_bulan' => ['steps' => 12, 'minimum_days' => 330, 'label' => '12 Bulan Cicilan'],
    ];

    public function index(Request $request)
    {
        abort_unless(auth()->user()->role === 'jemaah', 403);
        $pengajuan = $this->currentPengajuan();

        return view('home.keberangkatan-jemaah.index', [
            'mode' => 'departure',
            'pengajuan' => $pengajuan,
        ]);
    }

    public function paketIndex(Request $request)
    {
        abort_unless(auth()->user()->role === 'jemaah', 403);
        $pengajuan = $this->currentPengajuan();

        $query = PaketUmrah::with(['hotelMakkah', 'hotelMadinah'])->where('is_active', true);
        if ($search = trim((string) $request->search)) {
            $query->where('nama_paket', 'like', "%{$search}%");
        }
        if ($request->filled('durasi')) {
            $query->where('durasi', $request->integer('durasi'));
        }
        if ($request->filled('harga')) {
            match ($request->harga) {
                'under_25' => $query->where('harga', '<', 25000000),
                '25_35' => $query->whereBetween('harga', [25000000, 35000000]),
                'over_35' => $query->where('harga', '>', 35000000),
                default => null,
            };
        }
        if ($request->filled('maskapai')) {
            $durasiMaskapai = Keberangkatan::whereIn('status', [Keberangkatan::STATUS_AKTIF, Keberangkatan::STATUS_DISETUJUI])
                ->where(fn ($q) => $q->where('maskapai_berangkat_id', $request->integer('maskapai'))
                    ->orWhere('maskapai_pulang_id', $request->integer('maskapai')))
                ->whereNotNull('paket_id')
                ->with('paket')
                ->get()->map(fn ($k) => (int) $k->paket?->durasi)
                ->filter()
                ->unique();
            $query->whereIn('durasi', $durasiMaskapai);
        }

        $pakets = $query->orderBy('harga')->paginate(8)->withQueryString();
        $durations = PaketUmrah::where('is_active', true)->distinct()->orderBy('durasi')->pluck('durasi');
        $maskapais = Maskapai::orderBy('nama')->get();

        return view('home.keberangkatan-jemaah.index', compact('pengajuan', 'pakets', 'durations', 'maskapais') + [
            'mode' => 'packages',
        ]);
    }

    public function paketDetail($id)
    {
        $paket = PaketUmrah::with(['hotelMakkah', 'hotelMadinah', 'fasilitas', 'program'])
            ->where('is_active', true)->findOrFail($id);

        return response()->json([
            'paket' => $paket,
            'keberangkatan' => $this->availableSchedules($paket),
            'can_apply' => !$this->hasActivePengajuan(),
        ]);
    }

    public function jadwalByPaket($paketId, $durasi)
    {
        $paket = PaketUmrah::where('is_active', true)->findOrFail($paketId);
        abort_unless((int) $paket->durasi === (int) $durasi, 422);

        $jadwal = $this->availableSchedules($paket)
            ->map(function ($item) {
                $days = (int) today()->diffInDays($item->tanggal_keberangkatan, false);
                $item->days_remaining = $days;
                $item->available_schemes = collect(self::SCHEMES)
                    ->filter(fn ($scheme) => $days >= $scheme['minimum_days'])
                    ->map(fn ($scheme, $key) => ['value' => $key, ...$scheme])
                    ->values();
                return $item;
            })->values();

        return response()->json($jadwal);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->role === 'jemaah', 403);
        $data = $request->validate([
            'keberangkatan_id' => 'required|exists:keberangkatan,id',
            'paket_umrah_id' => 'required|exists:paket_umrah,id',
            'jenis_pembayaran' => ['required', Rule::in(array_keys(self::SCHEMES))],
            'dp_persen' => 'nullable|required_unless:jenis_pembayaran,sekali_bayar|integer|in:15,30',
        ]);

        $jemaah = auth()->user()->jemaah;
        abort_unless($jemaah, 422, 'Lengkapi Pendaftaran Saya terlebih dahulu.');
        if (KeberangkatanJemaah::where('jemaah_id', $jemaah->id)->whereIn('status', KeberangkatanJemaah::STATUSES)->exists()) {
            return response()->json(['message' => 'Anda sudah memiliki pengajuan keberangkatan aktif.'], 422);
        }

        $paket = PaketUmrah::where('is_active', true)->findOrFail($data['paket_umrah_id']);
        $jadwal = Keberangkatan::whereIn('status', [Keberangkatan::STATUS_AKTIF, Keberangkatan::STATUS_DISETUJUI])
            ->where('paket_id', $paket->id)
            ->whereDate('tanggal_keberangkatan', '>', today())
            ->findOrFail($data['keberangkatan_id']);
        abort_unless(!$jadwal->isFull(), 422, 'Kuota jadwal ini sudah penuh.');

        $scheme = self::SCHEMES[$data['jenis_pembayaran']];
        $days = today()->diffInDays($jadwal->tanggal_keberangkatan, false);
        if ($days < $scheme['minimum_days']) {
            return response()->json([
                'message' => "Skema {$scheme['label']} hanya tersedia minimal {$scheme['minimum_days']} hari sebelum keberangkatan.",
            ], 422);
        }

        $payment = DB::transaction(function () use ($data, $jemaah, $paket, $jadwal, $scheme) {
            $pengajuan = KeberangkatanJemaah::create([
                'jemaah_id' => $jemaah->id,
                'keberangkatan_id' => $jadwal->id,
                'paket_umrah_id' => $paket->id,
                'status' => KeberangkatanJemaah::STATUS_PENDAFTARAN,
            ]);

            $payment = Pembayaran::create([
                'keberangkatan_jemaah_id' => $pengajuan->id,
                'jemaah_id' => $jemaah->id,
                'keberangkatan_id' => $jadwal->id,
                'total_tagihan' => $paket->harga,
                'jumlah' => null,
                'jenis_pembayaran' => $data['jenis_pembayaran'],
                'dp_persen' => $scheme['steps'] === 1 ? null : $data['dp_persen'],
                'jumlah_tahap' => $scheme['steps'],
                'status' => 'belum_bayar',
                'status_rencana' => 'aktif',
            ]);

            foreach ($this->buildInstallments(
                (float) $paket->harga,
                $scheme['steps'],
                $scheme['steps'] === 1 ? 100 : (int) $data['dp_persen'],
                $jadwal->tanggal_keberangkatan
            ) as $installment) {
                $payment->tahapan()->create($installment);
            }
            return $payment;
        });

        $this->notifyStaff(
            'Pengajuan Keberangkatan Baru',
            auth()->user()->name." mengajukan {$paket->nama_paket} dengan skema {$scheme['label']}.",
            ['pembayaran_id' => $payment->id, 'url' => "/admin/pemabayan/{$payment->id}/detail"]
        );
        auth()->user()->notify(new \App\Notifications\PaymentStatusUpdatedToJemaah([
            'title' => 'Pengajuan Keberangkatan Berhasil',
            'message' => "Paket {$paket->nama_paket} berhasil diajukan. Rencana {$scheme['label']} telah dibuat.",
            'pembayaran_id' => $payment->id,
            'url' => '/pemabayan',
        ]));

        return response()->json([
            'message' => 'Pengajuan berhasil. Rencana pembayaran sudah dibuat.',
            'redirect' => '/pemabayan',
        ]);
    }

    public function approveSchedule()
    {
        abort_unless(auth()->user()->role === 'jemaah', 403);
        $pengajuan = $this->currentPengajuan();
        abort_unless($pengajuan, 404, 'Pengajuan keberangkatan tidak ditemukan.');
        abort_unless($pengajuan->status === KeberangkatanJemaah::STATUS_PENDAFTARAN, 422, 'Jadwal ini tidak dapat disetujui saat ini.');
        abort_unless(in_array($pengajuan->keberangkatan?->status, [
            Keberangkatan::STATUS_AKTIF,
            Keberangkatan::STATUS_DISETUJUI,
            Keberangkatan::STATUS_BERANGKAT,
        ], true), 422, 'Jadwal belum tersedia untuk disetujui.');

        $pengajuan->update(['status' => KeberangkatanJemaah::STATUS_SETUJU]);

        return response()->json(['message' => 'Jadwal keberangkatan berhasil disetujui.']);
    }

    public function rescheduleOptions()
    {
        abort_unless(auth()->user()->role === 'jemaah', 403);
        $pengajuan = $this->currentPengajuan();
        abort_unless($pengajuan, 404, 'Pengajuan keberangkatan tidak ditemukan.');
        $this->ensureCanRequestReschedule($pengajuan);

        $current = $pengajuan->keberangkatan;
        $options = Keberangkatan::with(['paket', 'maskapaiBerangkat', 'maskapaiPulang'])
            ->withCount(['jemaah' => fn ($q) => $q->whereIn('status', KeberangkatanJemaah::STATUSES)])
            ->where('paket_id', $pengajuan->paket_umrah_id)
            ->where('id', '!=', $current->id)
            ->whereIn('status', [Keberangkatan::STATUS_AKTIF, Keberangkatan::STATUS_DISETUJUI])
            ->whereDate('tanggal_keberangkatan', '>', $current->tanggal_keberangkatan)
            ->whereDate('tanggal_keberangkatan', '>', today())
            ->orderBy('tanggal_keberangkatan')
            ->get()
            ->filter(fn ($item) => !$item->isFull())
            ->map(fn ($item) => [
                'id' => $item->id,
                'kode' => $item->kode_keberangkatan,
                'tanggal_keberangkatan' => $item->tanggal_keberangkatan?->toDateString(),
                'tanggal_pulang' => $item->tanggal_pulang?->toDateString(),
                'sisa_kuota' => $item->sisa_kuota,
                'paket' => $item->paket?->nama_paket,
                'maskapai' => $item->maskapaiBerangkat?->nama,
            ])->values();

        return response()->json($options);
    }

    public function requestReschedule(Request $request)
    {
        abort_unless(auth()->user()->role === 'jemaah', 403);
        $data = $request->validate([
            'keberangkatan_tujuan_id' => 'required|exists:keberangkatan,id',
            'alasan_pengajuan' => 'nullable|string|max:2000',
        ]);

        $pengajuan = $this->currentPengajuan();
        abort_unless($pengajuan, 404, 'Pengajuan keberangkatan tidak ditemukan.');
        $this->ensureCanRequestReschedule($pengajuan);

        DB::transaction(function () use ($pengajuan, $data) {
            $pengajuan = KeberangkatanJemaah::whereKey($pengajuan->id)->lockForUpdate()->firstOrFail();
            abort_if($pengajuan->reschedules()->where('status', KeberangkatanJemaahReschedule::STATUS_MENUNGGU)->exists(), 422, 'Masih ada pengajuan reschedule yang menunggu.');

            $asal = Keberangkatan::whereKey($pengajuan->keberangkatan_id)->lockForUpdate()->firstOrFail();
            $tujuan = Keberangkatan::whereKey($data['keberangkatan_tujuan_id'])->withCount('jemaah')->lockForUpdate()->firstOrFail();

            abort_if($tujuan->id === $asal->id, 422, 'Pilih jadwal tujuan yang berbeda.');
            abort_unless((int) $tujuan->paket_id === (int) $pengajuan->paket_umrah_id, 422, 'Jadwal tujuan harus berasal dari paket yang sama.');
            abort_unless($tujuan->tanggal_keberangkatan->gt($asal->tanggal_keberangkatan), 422, 'Jadwal tujuan harus lebih mundur dari jadwal saat ini.');
            abort_unless(in_array($tujuan->status, [Keberangkatan::STATUS_AKTIF, Keberangkatan::STATUS_DISETUJUI], true), 422, 'Jadwal tujuan belum dapat dipilih.');
            abort_unless(!$tujuan->isFull(), 422, 'Kuota jadwal tujuan sudah penuh.');

            $pengajuan->reschedules()->create([
                'jemaah_id' => $pengajuan->jemaah_id,
                'keberangkatan_asal_id' => $asal->id,
                'keberangkatan_tujuan_id' => $tujuan->id,
                'status' => KeberangkatanJemaahReschedule::STATUS_MENUNGGU,
                'alasan_pengajuan' => $data['alasan_pengajuan'] ?? null,
                'diajukan_pada' => now(),
            ]);
            $pengajuan->update(['status' => KeberangkatanJemaah::STATUS_RESCHEDULE]);
        });

        return response()->json(['message' => 'Pengajuan perubahan jadwal berhasil dikirim.']);
    }

    private function buildInstallments(float $total, int $steps, int $dp, Carbon $departure): array
    {
        if ($steps === 1) {
            return [[
                'urutan' => 1, 'nama_tahap' => 'Pembayaran Penuh', 'persentase' => 100,
                'nominal' => $total, 'jatuh_tempo' => today(), 'status' => 'belum_bayar',
            ]];
        }

        $finalDue = $departure->copy()->subDays(30)->startOfDay();
        $start = today()->startOfDay();
        $remainingPercent = 100 - $dp;
        $basePercent = round($remainingPercent / ($steps - 1), 4);
        $result = [];
        $usedNominal = 0;
        $usedPercent = 0;

        for ($i = 1; $i <= $steps; $i++) {
            $percent = $i === 1 ? $dp : ($i === $steps ? 100 - $usedPercent : $basePercent);
            $nominal = $i === $steps ? $total - $usedNominal : round($total * $percent / 100, 2);
            $ratio = ($i - 1) / ($steps - 1);
            $due = $start->copy()->addDays((int) round($start->diffInDays($finalDue) * $ratio));
            $result[] = [
                'urutan' => $i,
                'nama_tahap' => $i === 1 ? 'DP (Pembayaran 1)' : ($i === $steps ? 'Pelunasan' : "Cicilan {$i}"),
                'persentase' => $percent,
                'nominal' => $nominal,
                'jatuh_tempo' => $due,
                'status' => 'belum_bayar',
            ];
            $usedPercent += $percent;
            $usedNominal += $nominal;
        }
        return $result;
    }

    private function availableSchedules(PaketUmrah $paket)
    {
        return Keberangkatan::with(['maskapaiBerangkat', 'maskapaiPulang', 'leader'])
            ->where('paket_id', $paket->id)
            ->whereIn('status', [Keberangkatan::STATUS_AKTIF, Keberangkatan::STATUS_DISETUJUI])
            ->whereDate('tanggal_keberangkatan', '>', today())
            ->withCount(['jemaah' => fn ($q) => $q->whereIn('status', KeberangkatanJemaah::STATUSES)])
            ->orderBy('tanggal_keberangkatan')
            ->get()
            ->filter(function ($item) use ($paket) {
                return $item->tanggal_keberangkatan && $item->tanggal_pulang && !$item->isFull();
            })
            ->values();
    }

    private function notifyStaff(string $title, string $message, array $extra = []): void
    {
        foreach (User::whereIn('role', ['admin', 'operator'])->get() as $user) {
            $user->notify(new PaymentUploadedToAdmin(compact('title', 'message') + $extra));
        }
    }

    private function currentPengajuan(): ?KeberangkatanJemaah
    {
        $jemaah = auth()->user()->jemaah;

        return $jemaah ? KeberangkatanJemaah::with([
            'keberangkatan.maskapaiBerangkat', 'keberangkatan.maskapaiPulang',
            'keberangkatan.leader', 'paketUmrah.hotelMakkah', 'paketUmrah.hotelMadinah',
            'paketUmrah.fasilitas', 'paketUmrah.program', 'pembayaran.tahapan',
            'pendingReschedule.keberangkatanTujuan', 'reschedules.keberangkatanAsal', 'reschedules.keberangkatanTujuan',
        ])->where('jemaah_id', $jemaah->id)->latest('id')->first() : null;
    }

    private function hasActivePengajuan(): bool
    {
        $jemaah = auth()->user()->jemaah;

        return $jemaah
            ? KeberangkatanJemaah::where('jemaah_id', $jemaah->id)->whereIn('status', KeberangkatanJemaah::STATUSES)->exists()
            : false;
    }

    private function ensureCanRequestReschedule(KeberangkatanJemaah $pengajuan): void
    {
        abort_unless(in_array($pengajuan->status, [KeberangkatanJemaah::STATUS_PENDAFTARAN, KeberangkatanJemaah::STATUS_SETUJU], true), 422, 'Status pengajuan tidak dapat mengajukan perubahan.');
        abort_unless($pengajuan->keberangkatan, 422, 'Jadwal keberangkatan tidak ditemukan.');
        abort_if($pengajuan->reschedules()->where('status', KeberangkatanJemaahReschedule::STATUS_MENUNGGU)->exists(), 422, 'Masih ada pengajuan reschedule yang menunggu.');
        $days = today()->diffInDays($pengajuan->keberangkatan->tanggal_keberangkatan, false);
        abort_unless($days >= 45, 422, 'Batas pengajuan perubahan minimal H-45 sebelum keberangkatan sudah lewat.');
    }
}
