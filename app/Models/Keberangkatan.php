<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keberangkatan extends Model
{
    protected $table = 'keberangkatan';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_AKTIF = 'aktif';

    public const STATUS_PENGAJUAN = 'pengajuan';

    public const STATUS_DIREVISI = 'direvisi';

    public const STATUS_DISETUJUI = 'disetujui';

    public const STATUS_BERANGKAT = 'berangkat';

    public const STATUS_BERLANGSUNG = 'berlangsung';

    public const STATUS_PULANG = 'pulang';

    public const STATUS_SELESAI = 'selesai';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_AKTIF,
        self::STATUS_PENGAJUAN,
        self::STATUS_DIREVISI,
        self::STATUS_DISETUJUI,
        self::STATUS_BERANGKAT,
        self::STATUS_BERLANGSUNG,
        self::STATUS_PULANG,
        self::STATUS_SELESAI,
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_keberangkatan' => 'datetime',
        'tanggal_pulang' => 'datetime',
    ];

    public function maskapaiBerangkat()
    {
        return $this->belongsTo(Maskapai::class, 'maskapai_berangkat_id');
    }

    public function paket()
    {
        return $this->belongsTo(PaketUmrah::class, 'paket_id');
    }

    public function maskapaiPulang()
    {
        return $this->belongsTo(Maskapai::class, 'maskapai_pulang_id');
    }

    public function leader()
    {
        return $this->belongsTo(TourLeader::class, 'tour_leader_id');
    }

    public function jemaah()
    {
        return $this->hasMany(KeberangkatanJemaah::class);
    }

    public function rescheduleRequests()
    {
        return $this->hasMany(KeberangkatanJemaahReschedule::class, 'keberangkatan_asal_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pengubah()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getKodeKeberangkatanAttribute(): string
    {
        return 'KBR-'.str_pad(strtoupper(base_convert((string) $this->id, 10, 36)), 6, '0', STR_PAD_LEFT);
    }

    public function getTerisiAttribute(): int
    {
        return $this->jemaah_count ?? $this->jemaah()->whereIn('status', [
            KeberangkatanJemaah::STATUS_PENDAFTARAN,
            KeberangkatanJemaah::STATUS_SETUJU,
            KeberangkatanJemaah::STATUS_RESCHEDULE,
        ])->count();
    }

    public function getSisaKuotaAttribute(): int
    {
        return max(0, (int) ($this->kuota ?? 0) - $this->terisi);
    }

    public function isFull(): bool
    {
        return $this->sisa_kuota <= 0;
    }

    public static function statusLabel(?string $status): string
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_AKTIF => 'Aktif',
            self::STATUS_PENGAJUAN => 'Menunggu Approval',
            self::STATUS_DIREVISI => 'Direvisi',
            self::STATUS_DISETUJUI => 'Disetujui',
            self::STATUS_BERANGKAT => 'Berangkat',
            self::STATUS_BERLANGSUNG => 'Berlangsung',
            self::STATUS_PULANG => 'Pulang',
            self::STATUS_SELESAI => 'Selesai',
        ][$status] ?? ucfirst((string) $status);
    }

    public static function statusBadgeClass(?string $status): string
    {
        return [
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_AKTIF => 'success',
            self::STATUS_PENGAJUAN => 'warning',
            self::STATUS_DIREVISI => 'danger',
            self::STATUS_DISETUJUI => 'primary',
            self::STATUS_BERANGKAT => 'info',
            self::STATUS_BERLANGSUNG => 'info',
            self::STATUS_PULANG => 'dark',
            self::STATUS_SELESAI => 'success',
        ][$status] ?? 'secondary';
    }
}
