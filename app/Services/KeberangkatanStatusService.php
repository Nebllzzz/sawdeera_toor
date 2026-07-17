<?php

namespace App\Services;

use App\Models\Keberangkatan;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class KeberangkatanStatusService
{
    private const TRANSITIONS = [
        Keberangkatan::STATUS_DRAFT => [
            'activate' => Keberangkatan::STATUS_AKTIF,
        ],
        Keberangkatan::STATUS_AKTIF => [
            'submit' => Keberangkatan::STATUS_PENGAJUAN,
        ],
        Keberangkatan::STATUS_PENGAJUAN => [
            'approve' => Keberangkatan::STATUS_DISETUJUI,
            'revise' => Keberangkatan::STATUS_DIREVISI,
        ],
        Keberangkatan::STATUS_DIREVISI => [
            'submit' => Keberangkatan::STATUS_PENGAJUAN,
        ],
        Keberangkatan::STATUS_DISETUJUI => [
            'depart' => Keberangkatan::STATUS_BERANGKAT,
        ],
        Keberangkatan::STATUS_BERANGKAT => [
            'start' => Keberangkatan::STATUS_BERLANGSUNG,
        ],
        Keberangkatan::STATUS_BERLANGSUNG => [
            'return' => Keberangkatan::STATUS_PULANG,
        ],
        Keberangkatan::STATUS_PULANG => [
            'finish' => Keberangkatan::STATUS_SELESAI,
        ],
    ];

    private const ACTION_ROLES = [
        'activate' => ['operator'],
        'submit' => ['operator'],
        'approve' => ['admin'],
        'revise' => ['admin'],
        'depart' => ['operator'],
        'start' => ['operator'],
        'return' => ['operator'],
        'finish' => ['operator'],
    ];

    public function nextStatus(Keberangkatan $keberangkatan, string $action): string
    {
        $next = self::TRANSITIONS[$keberangkatan->status][$action] ?? null;
        if (!$next) {
            throw ValidationException::withMessages([
                'status' => 'Transisi status tidak valid untuk jadwal ini.',
            ]);
        }

        return $next;
    }

    public function ensureAllowed(Keberangkatan $keberangkatan, User $user, string $action): string
    {
        if (!in_array($user->role, self::ACTION_ROLES[$action] ?? [], true)) {
            abort(403, 'Anda tidak memiliki akses untuk aksi ini.');
        }

        return $this->nextStatus($keberangkatan, $action);
    }

    public function transition(Keberangkatan $keberangkatan, User $user, string $action, ?string $alasanRevisi = null): Keberangkatan
    {
        $next = $this->ensureAllowed($keberangkatan, $user, $action);

        if ($action === 'revise' && blank($alasanRevisi)) {
            throw ValidationException::withMessages([
                'alasan_revisi' => 'Alasan revisi wajib diisi.',
            ]);
        }

        $keberangkatan->status = $next;
        $keberangkatan->updated_by = $user->id;
        if ($action === 'revise') {
            $keberangkatan->alasan_revisi = $alasanRevisi;
        }
        if (in_array($action, ['submit', 'approve'], true)) {
            $keberangkatan->alasan_revisi = null;
        }
        $keberangkatan->save();

        return $keberangkatan;
    }

    public function actionsFor(Keberangkatan $keberangkatan, User $user): array
    {
        return collect(self::TRANSITIONS[$keberangkatan->status] ?? [])
            ->keys()
            ->filter(fn ($action) => in_array($user->role, self::ACTION_ROLES[$action] ?? [], true))
            ->values()
            ->all();
    }

    public function actionLabel(string $action): string
    {
        return [
            'activate' => 'Aktifkan Jadwal',
            'submit' => 'Ajukan Jadwal',
            'approve' => 'Setujui Jadwal',
            'revise' => 'Minta Revisi',
            'depart' => 'Tandai Berangkat',
            'start' => 'Tandai Berlangsung',
            'return' => 'Tandai Pulang',
            'finish' => 'Tandai Selesai',
        ][$action] ?? ucfirst($action);
    }
}
