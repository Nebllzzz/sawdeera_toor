<?php

namespace App\Services;

use App\Models\Keberangkatan;
use App\Models\KeberangkatanJemaah;
use App\Models\PaketUmrah;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class JemaahRecapService
{
    private const MONTHS = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public static function reportTypes(): array
    {
        return [
            'pendaftaran' => 'Rekapitulasi Pendaftaran',
            'verifikasi' => 'Rekapitulasi Verifikasi',
            'pembayaran' => 'Rekapitulasi Pembayaran',
            'keberangkatan' => 'Rekapitulasi Keberangkatan',
            'paket_umrah' => 'Rekapitulasi Paket Umrah',
            'status_jemaah' => 'Rekapitulasi Status Jemaah',
            'dokumen' => 'Rekapitulasi Dokumen',
        ];
    }

    public function build(string $type, ?int $packageId, Carbon $start, Carbon $end): array
    {
        $packages = PaketUmrah::query()
            ->when($packageId, fn ($query) => $query->whereKey($packageId))
            ->orderBy('nama_paket')
            ->get();

        $result = match ($type) {
            'pendaftaran' => $this->registration($packages, $packageId, $start, $end),
            'verifikasi' => $this->verification($packages, $packageId, $start, $end),
            'pembayaran' => $this->payment($packages, $packageId, $start, $end),
            'keberangkatan' => $this->departure($packages, $packageId, $start, $end),
            'paket_umrah' => $this->packageRecap($packages, $packageId, $start, $end),
            'status_jemaah' => $this->jemaahStatus($packages, $packageId, $start, $end),
            'dokumen' => $this->document($packages, $packageId, $start, $end),
        };

        return [
            'type' => $type,
            'title' => self::reportTypes()[$type],
            'period_label' => $this->periodLabel($start, $end),
            'package_label' => $packageId ? ($packages->first()?->nama_paket ?? '-') : 'Semua Paket Umrah',
            ...$result,
        ];
    }

    public function formatValue(mixed $value, string $format = 'number'): string
    {
        return match ($format) {
            'currency' => 'Rp '.number_format((float) $value, 0, ',', '.'),
            'percent' => number_format((float) $value, 1, ',', '.').'%',
            'decimal' => number_format((float) $value, 1, ',', '.'),
            default => is_numeric($value) ? number_format((float) $value, 0, ',', '.') : (string) $value,
        };
    }

    private function registration(Collection $packages, ?int $packageId, Carbon $start, Carbon $end): array
    {
        $metrics = ['total' => 0, 'verified' => 0, 'waiting' => 0, 'rejected' => 0, 'verification_rate' => 0];
        $rows = $this->emptyRows($packages, $start, $end, $metrics);
        $items = $this->assignmentQuery($packageId)
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->get();

        foreach ($items as $item) {
            $key = $this->rowKey($item->paket_umrah_id, $item->created_at);
            if (! isset($rows[$key])) {
                continue;
            }
            $rows[$key]['total']++;
            match ($item->jemaah?->user?->status) {
                'aktif' => $rows[$key]['verified']++,
                'tidak_aktif' => $rows[$key]['rejected']++,
                default => $rows[$key]['waiting']++,
            };
        }

        foreach ($rows as &$row) {
            $row['verification_rate'] = $this->percentage($row['verified'], $row['total']);
        }
        unset($row);

        $totals = $this->sumTotals($rows, ['total', 'verified', 'waiting', 'rejected']);
        $totals['verification_rate'] = $this->percentage($totals['verified'], $totals['total']);

        return [
            'note' => 'Periode dihitung dari tanggal jemaah memilih paket. Status mengikuti status verifikasi akun terbaru.',
            'columns' => $this->columns([
                ['key' => 'total', 'label' => 'Pendaftaran Baru'],
                ['key' => 'verified', 'label' => 'Terverifikasi'],
                ['key' => 'waiting', 'label' => 'Menunggu Verifikasi'],
                ['key' => 'rejected', 'label' => 'Ditolak'],
                ['key' => 'verification_rate', 'label' => 'Persentase Verifikasi', 'format' => 'percent'],
            ]),
            'summary' => [
                $this->card('Total Pendaftaran', $totals['total'], 'fa-users', 'blue'),
                $this->card('Terverifikasi', $totals['verified'], 'fa-circle-check', 'green', $this->formatValue($totals['verification_rate'], 'percent').' dari pendaftaran'),
                $this->card('Menunggu Verifikasi', $totals['waiting'], 'fa-clock', 'amber'),
                $this->card('Ditolak', $totals['rejected'], 'fa-circle-xmark', 'red'),
            ],
            'rows' => array_values($rows),
            'totals' => $totals,
        ];
    }

    private function verification(Collection $packages, ?int $packageId, Carbon $start, Carbon $end): array
    {
        $metrics = ['total' => 0, 'verified' => 0, 'waiting' => 0, 'rejected' => 0, 'verification_rate' => 0];
        $rows = $this->emptyRows($packages, $start, $end, $metrics);
        $items = $this->assignmentQuery($packageId)->get();

        foreach ($items as $item) {
            $eventDate = $item->jemaah?->user?->updated_at ?? $item->created_at;
            if (! $this->inside($eventDate, $start, $end)) {
                continue;
            }
            $key = $this->rowKey($item->paket_umrah_id, $eventDate);
            if (! isset($rows[$key])) {
                continue;
            }
            $rows[$key]['total']++;
            match ($item->jemaah?->user?->status) {
                'aktif' => $rows[$key]['verified']++,
                'tidak_aktif' => $rows[$key]['rejected']++,
                default => $rows[$key]['waiting']++,
            };
        }

        foreach ($rows as &$row) {
            $row['verification_rate'] = $this->percentage($row['verified'], $row['total']);
        }
        unset($row);

        $totals = $this->sumTotals($rows, ['total', 'verified', 'waiting', 'rejected']);
        $totals['verification_rate'] = $this->percentage($totals['verified'], $totals['total']);

        return [
            'note' => 'Periode dihitung dari pembaruan status akun terakhir untuk jemaah yang sudah memilih paket.',
            'columns' => $this->columns([
                ['key' => 'verified', 'label' => 'Terverifikasi'],
                ['key' => 'waiting', 'label' => 'Menunggu'],
                ['key' => 'rejected', 'label' => 'Ditolak'],
                ['key' => 'total', 'label' => 'Total Akun'],
                ['key' => 'verification_rate', 'label' => 'Persentase Terverifikasi', 'format' => 'percent'],
            ]),
            'summary' => [
                $this->card('Akun Terverifikasi', $totals['verified'], 'fa-circle-check', 'green'),
                $this->card('Akun Menunggu', $totals['waiting'], 'fa-clock', 'amber'),
                $this->card('Akun Ditolak', $totals['rejected'], 'fa-circle-xmark', 'red'),
                $this->card('Total Akun', $totals['total'], 'fa-users', 'blue', $this->formatValue($totals['verification_rate'], 'percent').' terverifikasi'),
            ],
            'rows' => array_values($rows),
            'totals' => $totals,
        ];
    }

    private function payment(Collection $packages, ?int $packageId, Carbon $start, Carbon $end): array
    {
        $metrics = ['paid' => 0, 'partial' => 0, 'unpaid' => 0, 'rejected' => 0, 'received' => 0];
        $rows = $this->emptyRows($packages, $start, $end, $metrics);
        $payments = \App\Models\Pembayaran::with(['pengajuan.paketUmrah', 'tahapan'])
            ->whereNotNull('keberangkatan_jemaah_id')
            ->when($packageId, fn ($query) => $query->whereHas('pengajuan', fn ($q) => $q->where('paket_umrah_id', $packageId)))
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->get();

        foreach ($payments as $payment) {
            $package = $payment->pengajuan?->paket_umrah_id;
            $key = $this->rowKey($package, $payment->created_at);
            if (! isset($rows[$key])) {
                continue;
            }

            $steps = $payment->tahapan;
            $verifiedSteps = $steps->where('status', 'diverifikasi');
            $rows[$key]['received'] += (float) $verifiedSteps->sum('nominal');
            $allPaid = $steps->isNotEmpty() && $verifiedSteps->count() === $steps->count();
            $hasRejected = $payment->status === 'ditolak' || $steps->contains('status', 'ditolak');

            if ($hasRejected) {
                $rows[$key]['rejected']++;
            } elseif ($allPaid) {
                $rows[$key]['paid']++;
            } elseif ($verifiedSteps->isNotEmpty()) {
                $rows[$key]['partial']++;
            } else {
                $rows[$key]['unpaid']++;
            }
        }

        $totals = $this->sumTotals($rows, ['paid', 'partial', 'unpaid', 'rejected', 'received']);

        return [
            'note' => 'Periode dihitung dari pembuatan rencana pembayaran. Pembayaran masuk hanya menjumlahkan termin yang sudah diverifikasi.',
            'columns' => $this->columns([
                ['key' => 'paid', 'label' => 'Lunas (Jemaah)'],
                ['key' => 'partial', 'label' => 'Cicilan / DP (Jemaah)'],
                ['key' => 'unpaid', 'label' => 'Belum Bayar (Jemaah)'],
                ['key' => 'rejected', 'label' => 'Ditolak (Jemaah)'],
                ['key' => 'received', 'label' => 'Total Pembayaran Masuk', 'format' => 'currency'],
            ]),
            'summary' => [
                $this->card('Total Pembayaran Masuk', $totals['received'], 'fa-wallet', 'blue', null, 'currency'),
                $this->card('Lunas', $totals['paid'], 'fa-clock', 'green', 'Jemaah'),
                $this->card('Cicilan / DP', $totals['partial'], 'fa-receipt', 'amber', 'Jemaah'),
                $this->card('Belum Bayar', $totals['unpaid'], 'fa-money-bill-wave', 'red', 'Jemaah'),
            ],
            'rows' => array_values($rows),
            'totals' => $totals,
        ];
    }

    private function departure(Collection $packages, ?int $packageId, Carbon $start, Carbon $end): array
    {
        $metrics = ['schedules' => 0, 'quota' => 0, 'filled' => 0, 'ready' => 0, 'departed' => 0, 'not_departed' => 0, 'departure_rate' => 0];
        $rows = $this->emptyRows($packages, $start, $end, $metrics);
        $schedules = $this->scheduleQuery($packageId, $start, $end)->get();
        $departedStatuses = [
            Keberangkatan::STATUS_BERANGKAT, Keberangkatan::STATUS_BERLANGSUNG,
            Keberangkatan::STATUS_PULANG, Keberangkatan::STATUS_SELESAI,
        ];

        foreach ($schedules as $schedule) {
            $key = $this->rowKey($schedule->paket_id, $schedule->tanggal_keberangkatan);
            if (! isset($rows[$key])) {
                continue;
            }
            $jemaah = $schedule->jemaah->whereIn('status', KeberangkatanJemaah::STATUSES);
            $filled = $jemaah->count();
            $departed = in_array($schedule->status, $departedStatuses, true) ? $filled : 0;
            $rows[$key]['schedules']++;
            $rows[$key]['quota'] += (int) $schedule->kuota;
            $rows[$key]['filled'] += $filled;
            $rows[$key]['ready'] += $jemaah->where('status', KeberangkatanJemaah::STATUS_SETUJU)->count();
            $rows[$key]['departed'] += $departed;
            $rows[$key]['not_departed'] += $filled - $departed;
        }

        foreach ($rows as &$row) {
            $row['departure_rate'] = $this->percentage($row['departed'], $row['filled']);
        }
        unset($row);
        $totals = $this->sumTotals($rows, ['schedules', 'quota', 'filled', 'ready', 'departed', 'not_departed']);
        $totals['departure_rate'] = $this->percentage($totals['departed'], $totals['filled']);

        return [
            'note' => 'Periode dihitung dari tanggal keberangkatan pada jadwal. Status berangkat meliputi berangkat, berlangsung, pulang, dan selesai.',
            'columns' => $this->columns([
                ['key' => 'schedules', 'label' => 'Total Jadwal'],
                ['key' => 'quota', 'label' => 'Total Kuota'],
                ['key' => 'filled', 'label' => 'Jemaah Terisi'],
                ['key' => 'ready', 'label' => 'Siap Berangkat'],
                ['key' => 'departed', 'label' => 'Berangkat'],
                ['key' => 'not_departed', 'label' => 'Belum Berangkat'],
                ['key' => 'departure_rate', 'label' => 'Realisasi', 'format' => 'percent'],
            ]),
            'summary' => [
                $this->card('Total Jadwal', $totals['schedules'], 'fa-route', 'purple'),
                $this->card('Total Kuota', $totals['quota'], 'fa-users', 'amber'),
                $this->card('Jemaah Terisi', $totals['filled'], 'fa-user-group', 'blue'),
                $this->card('Berangkat', $totals['departed'], 'fa-plane-departure', 'green', $this->formatValue($totals['departure_rate'], 'percent').' dari terisi'),
                $this->card('Belum Berangkat', $totals['not_departed'], 'fa-calendar-xmark', 'red'),
            ],
            'rows' => array_values($rows),
            'totals' => $totals,
        ];
    }

    private function packageRecap(Collection $packages, ?int $packageId, Carbon $start, Carbon $end): array
    {
        $metrics = ['schedules' => 0, 'quota' => 0, 'filled' => 0, 'remaining' => 0, 'occupancy_rate' => 0, 'jemaah_share' => 0];
        $rows = $this->emptyRows($packages, $start, $end, $metrics);
        $schedules = $this->scheduleQuery($packageId, $start, $end)->get();

        foreach ($schedules as $schedule) {
            $key = $this->rowKey($schedule->paket_id, $schedule->tanggal_keberangkatan);
            if (! isset($rows[$key])) {
                continue;
            }
            $filled = $schedule->jemaah->whereIn('status', KeberangkatanJemaah::STATUSES)->count();
            $rows[$key]['schedules']++;
            $rows[$key]['quota'] += (int) $schedule->kuota;
            $rows[$key]['filled'] += $filled;
            $rows[$key]['remaining'] += max(0, (int) $schedule->kuota - $filled);
        }

        $totalFilled = array_sum(array_column($rows, 'filled'));
        foreach ($rows as &$row) {
            $row['occupancy_rate'] = $this->percentage($row['filled'], $row['quota']);
            $row['jemaah_share'] = $this->percentage($row['filled'], $totalFilled);
        }
        unset($row);
        $totals = $this->sumTotals($rows, ['schedules', 'quota', 'filled', 'remaining']);
        $totals['occupancy_rate'] = $this->percentage($totals['filled'], $totals['quota']);
        $totals['jemaah_share'] = $totalFilled > 0 ? 100 : 0;

        $byPackage = collect($rows)->groupBy('package_name')->map(fn ($items) => $items->sum('filled'));
        $favorite = $byPackage->filter(fn ($value) => $value > 0)->sortDesc()->keys()->first() ?? '-';
        $totalPackages = $schedules->pluck('paket_id')->unique()->count();

        return [
            'note' => 'Periode dihitung dari tanggal keberangkatan jadwal. Kuota dan keterisian dijumlahkan untuk seluruh jadwal paket pada bulan tersebut.',
            'columns' => $this->columns([
                ['key' => 'schedules', 'label' => 'Jumlah Jadwal'],
                ['key' => 'quota', 'label' => 'Kuota'],
                ['key' => 'filled', 'label' => 'Terisi'],
                ['key' => 'remaining', 'label' => 'Sisa Kuota'],
                ['key' => 'occupancy_rate', 'label' => 'Persentase Terisi', 'format' => 'percent'],
                ['key' => 'jemaah_share', 'label' => 'Persentase Jemaah', 'format' => 'percent'],
            ]),
            'summary' => [
                $this->card('Total Jemaah', $totals['filled'], 'fa-users', 'blue'),
                $this->card('Total Paket', $totalPackages, 'fa-suitcase', 'green'),
                $this->card('Paket Terfavorit', $favorite, 'fa-star', 'amber', 'Berdasarkan jumlah jemaah', 'text'),
                $this->card('Rata-rata Keterisian', $totals['occupancy_rate'], 'fa-chart-column', 'purple', null, 'percent'),
            ],
            'rows' => array_values($rows),
            'totals' => $totals,
        ];
    }

    private function jemaahStatus(Collection $packages, ?int $packageId, Carbon $start, Carbon $end): array
    {
        $metrics = ['total' => 0, 'active' => 0, 'inactive' => 0, 'ready' => 0, 'incomplete' => 0, 'rejected' => 0, 'ready_rate' => 0];
        $rows = $this->emptyRows($packages, $start, $end, $metrics);
        $items = $this->assignmentQuery($packageId)
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->get();

        foreach ($items as $item) {
            $key = $this->rowKey($item->paket_umrah_id, $item->created_at);
            if (! isset($rows[$key])) {
                continue;
            }
            $userStatus = $item->jemaah?->user?->status;
            $dataStatus = $item->jemaah?->status_data;
            $hasRejectedDocument = $item->jemaah?->dokumen?->contains('status', 'ditolak') ?? false;
            $hasRejectedPayment = $item->pembayaran?->status === 'ditolak'
                || ($item->pembayaran?->tahapan?->contains('status', 'ditolak') ?? false);

            $rows[$key]['total']++;
            $rows[$key][$userStatus === 'aktif' ? 'active' : 'inactive']++;
            $rows[$key]['ready'] += $item->status === KeberangkatanJemaah::STATUS_SETUJU ? 1 : 0;
            $rows[$key]['incomplete'] += $dataStatus !== 'terverifikasi' ? 1 : 0;
            $rows[$key]['rejected'] += ($userStatus === 'tidak_aktif' || $dataStatus === 'perlu_perbaikan' || $hasRejectedDocument || $hasRejectedPayment) ? 1 : 0;
        }

        foreach ($rows as &$row) {
            $row['ready_rate'] = $this->percentage($row['ready'], $row['total']);
        }
        unset($row);
        $totals = $this->sumTotals($rows, ['total', 'active', 'inactive', 'ready', 'incomplete', 'rejected']);
        $totals['ready_rate'] = $this->percentage($totals['ready'], $totals['total']);

        return [
            'note' => 'Periode dihitung dari tanggal pemilihan paket. Kolom status bersifat indikator dan dapat saling beririsan.',
            'columns' => $this->columns([
                ['key' => 'total', 'label' => 'Total Jemaah'],
                ['key' => 'active', 'label' => 'Aktif'],
                ['key' => 'inactive', 'label' => 'Nonaktif'],
                ['key' => 'ready', 'label' => 'Siap Berangkat'],
                ['key' => 'incomplete', 'label' => 'Belum Lengkap'],
                ['key' => 'rejected', 'label' => 'Ditolak / Perlu Revisi'],
                ['key' => 'ready_rate', 'label' => 'Persentase Siap', 'format' => 'percent'],
            ]),
            'summary' => [
                $this->card('Total Jemaah', $totals['total'], 'fa-users', 'blue'),
                $this->card('Aktif', $totals['active'], 'fa-user-check', 'green'),
                $this->card('Nonaktif', $totals['inactive'], 'fa-user-lock', 'gray'),
                $this->card('Siap Berangkat', $totals['ready'], 'fa-clipboard-check', 'amber', $this->formatValue($totals['ready_rate'], 'percent').' dari total'),
                $this->card('Belum Lengkap', $totals['incomplete'], 'fa-grip', 'purple'),
                $this->card('Ditolak / Revisi', $totals['rejected'], 'fa-circle-xmark', 'red'),
            ],
            'rows' => array_values($rows),
            'totals' => $totals,
        ];
    }

    private function document(Collection $packages, ?int $packageId, Carbon $start, Carbon $end): array
    {
        $metrics = ['required' => 0, 'uploaded' => 0, 'verified' => 0, 'processing' => 0, 'rejected' => 0, 'complete_jemaah' => 0, 'completion_rate' => 0];
        $rows = $this->emptyRows($packages, $start, $end, $metrics);
        $items = $this->assignmentQuery($packageId)
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->get();

        foreach ($items as $item) {
            $key = $this->rowKey($item->paket_umrah_id, $item->created_at);
            if (! isset($rows[$key])) {
                continue;
            }
            $requiredTypes = ['paspor', 'ktp', 'kartu_keluarga', 'visa', 'vaksin', 'foto_4x6'];
            if ($item->jemaah?->status_pernikahan === 'menikah') {
                $requiredTypes[] = 'buku_nikah';
            }
            $documents = $item->jemaah?->dokumen?->whereIn('jenis_dokumen', $requiredTypes) ?? collect();
            $required = count($requiredTypes);
            $uploaded = $documents->whereIn('status', ['diproses', 'diverifikasi', 'ditolak'])->count();
            $verified = $documents->where('status', 'diverifikasi')->count();

            $rows[$key]['required'] += $required;
            $rows[$key]['uploaded'] += $uploaded;
            $rows[$key]['verified'] += $verified;
            $rows[$key]['processing'] += $documents->where('status', 'diproses')->count();
            $rows[$key]['rejected'] += $documents->where('status', 'ditolak')->count();
            $rows[$key]['complete_jemaah'] += $verified === $required ? 1 : 0;
        }

        foreach ($rows as &$row) {
            $row['completion_rate'] = $this->percentage($row['verified'], $row['required']);
        }
        unset($row);
        $totals = $this->sumTotals($rows, ['required', 'uploaded', 'verified', 'processing', 'rejected', 'complete_jemaah']);
        $totals['completion_rate'] = $this->percentage($totals['verified'], $totals['required']);

        return [
            'note' => 'Periode dihitung dari tanggal pemilihan paket. Kebutuhan dokumen adalah 6 dokumen, ditambah buku nikah untuk jemaah menikah.',
            'columns' => $this->columns([
                ['key' => 'required', 'label' => 'Dokumen Wajib'],
                ['key' => 'uploaded', 'label' => 'Sudah Diunggah'],
                ['key' => 'verified', 'label' => 'Terverifikasi'],
                ['key' => 'processing', 'label' => 'Diproses'],
                ['key' => 'rejected', 'label' => 'Ditolak'],
                ['key' => 'complete_jemaah', 'label' => 'Jemaah Lengkap'],
                ['key' => 'completion_rate', 'label' => 'Kelengkapan', 'format' => 'percent'],
            ]),
            'summary' => [
                $this->card('Dokumen Wajib', $totals['required'], 'fa-folder-open', 'blue'),
                $this->card('Dokumen Diunggah', $totals['uploaded'], 'fa-cloud-arrow-up', 'purple'),
                $this->card('Terverifikasi', $totals['verified'], 'fa-file-circle-check', 'green', $this->formatValue($totals['completion_rate'], 'percent').' dari kebutuhan'),
                $this->card('Sedang Diproses', $totals['processing'], 'fa-clock', 'amber'),
                $this->card('Ditolak', $totals['rejected'], 'fa-file-circle-xmark', 'red'),
                $this->card('Jemaah Dokumen Lengkap', $totals['complete_jemaah'], 'fa-user-check', 'cyan'),
            ],
            'rows' => array_values($rows),
            'totals' => $totals,
        ];
    }

    private function assignmentQuery(?int $packageId)
    {
        return KeberangkatanJemaah::with([
            'jemaah.user', 'jemaah.dokumen', 'paketUmrah', 'keberangkatan',
            'pembayaran.tahapan',
        ])->when($packageId, fn ($query) => $query->where('paket_umrah_id', $packageId));
    }

    private function scheduleQuery(?int $packageId, Carbon $start, Carbon $end)
    {
        return Keberangkatan::with(['paket', 'jemaah'])
            ->whereNotNull('paket_id')
            ->when($packageId, fn ($query) => $query->where('paket_id', $packageId))
            ->whereBetween('tanggal_keberangkatan', [$start->toDateString(), $end->toDateString()]);
    }

    private function emptyRows(Collection $packages, Carbon $start, Carbon $end, array $metrics): array
    {
        $rows = [];
        foreach ($packages as $package) {
            foreach ($this->months($start, $end) as $month) {
                $rows[$this->rowKey($package->id, $month)] = [
                    'package_id' => $package->id,
                    'package_name' => $package->nama_paket,
                    'month_key' => $month->format('Y-m'),
                    'month_label' => self::MONTHS[(int) $month->month].' '.$month->year,
                    ...$metrics,
                ];
            }
        }

        return $rows;
    }

    private function columns(array $metrics): array
    {
        return [
            ['key' => 'package_name', 'label' => 'Paket Umrah', 'format' => 'text'],
            ['key' => 'month_label', 'label' => 'Bulan', 'format' => 'text'],
            ...array_map(fn ($column) => ['format' => 'number', ...$column], $metrics),
        ];
    }

    private function months(Carbon $start, Carbon $end): array
    {
        $months = [];
        $cursor = $start->copy()->startOfMonth();
        $last = $end->copy()->startOfMonth();
        while ($cursor->lte($last)) {
            $months[] = $cursor->copy();
            $cursor->addMonth();
        }

        return $months;
    }

    private function rowKey(?int $packageId, mixed $date): string
    {
        return ((int) $packageId).'|'.Carbon::parse($date)->format('Y-m');
    }

    private function inside(mixed $date, Carbon $start, Carbon $end): bool
    {
        if (! $date) {
            return false;
        }
        $value = Carbon::parse($date);

        return $value->betweenIncluded($start->copy()->startOfDay(), $end->copy()->endOfDay());
    }

    private function sumTotals(array $rows, array $keys): array
    {
        $totals = [];
        foreach ($keys as $key) {
            $totals[$key] = array_sum(array_column($rows, $key));
        }

        return $totals;
    }

    private function percentage(float|int $value, float|int $total): float
    {
        return $total > 0 ? round(($value / $total) * 100, 1) : 0;
    }

    private function card(string $label, mixed $value, string $icon, string $color, ?string $detail = null, string $format = 'number'): array
    {
        return [
            'label' => $label,
            'value' => $value,
            'formatted' => $format === 'text' ? (string) $value : $this->formatValue($value, $format),
            'icon' => $icon,
            'color' => $color,
            'detail' => $detail,
        ];
    }

    private function periodLabel(Carbon $start, Carbon $end): string
    {
        return $start->format('d/m/Y').' - '.$end->format('d/m/Y');
    }
}
