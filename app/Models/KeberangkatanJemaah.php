<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeberangkatanJemaah extends Model
{
    protected $table = 'keberangkatan_jemaah';

    public const STATUS_PENDAFTARAN = 'pendaftaran';
    public const STATUS_RESCHEDULE = 'reschedule';
    public const STATUS_SETUJU = 'setuju';

    public const STATUSES = [
        self::STATUS_PENDAFTARAN,
        self::STATUS_RESCHEDULE,
        self::STATUS_SETUJU,
    ];

    protected $guarded = ['id'];

    public function keberangkatan()
    {
        return $this->belongsTo(Keberangkatan::class);
    }
    public function paketUmrah()
    {
        return $this->belongsTo(PaketUmrah::class);
    }
    public function jemaah()
    {
        return $this->belongsTo(DataJemaah::class);
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'keberangkatan_jemaah_id');
    }

    public function reschedules()
    {
        return $this->hasMany(KeberangkatanJemaahReschedule::class, 'keberangkatan_jemaah_id');
    }

    public function pendingReschedule()
    {
        return $this->hasOne(KeberangkatanJemaahReschedule::class, 'keberangkatan_jemaah_id')
            ->where('status', KeberangkatanJemaahReschedule::STATUS_MENUNGGU)
            ->latestOfMany();
    }

    public static function statusLabel(?string $status): string
    {
        return [
            self::STATUS_PENDAFTARAN => 'Pendaftaran',
            self::STATUS_RESCHEDULE => 'Reschedule',
            self::STATUS_SETUJU => 'Setuju',
        ][$status] ?? ucfirst((string) $status);
    }
}
