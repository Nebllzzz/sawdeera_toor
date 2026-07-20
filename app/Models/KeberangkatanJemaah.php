<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    public function scopeReadyForApproval(Builder $query): Builder
    {
        return $query
            ->whereHas('jemaah', function (Builder $query) {
                $query->where('status_data', 'terverifikasi')
                    ->whereHas('user', fn (Builder $query) => $query->where('status', 'aktif'));

                foreach (DataJemaah::BASE_REQUIRED_DOCUMENTS as $type) {
                    $query->whereHas('dokumen', fn (Builder $query) => $query
                        ->where('jenis_dokumen', $type)
                        ->where('status', 'diverifikasi'));
                }

                $query->where(function (Builder $query) {
                    $query->where('status_pernikahan', 'belum_menikah')
                        ->orWhere(function (Builder $query) {
                            $query->where('status_pernikahan', 'menikah')
                                ->whereHas('dokumen', fn (Builder $query) => $query
                                    ->where('jenis_dokumen', DataJemaah::MARRIAGE_DOCUMENT)
                                    ->where('status', 'diverifikasi'));
                        });
                });
            })
            ->whereHas('pembayaran', fn (Builder $query) => $query
                ->where('status', 'diverifikasi')
                ->whereHas('tahapan')
                ->whereDoesntHave('tahapan', fn (Builder $query) => $query->where('status', '!=', 'diverifikasi')));
    }

    public static function statusLabel(?string $status): string
    {
        return [
            self::STATUS_PENDAFTARAN => 'Pendaftaran',
            self::STATUS_RESCHEDULE => 'Reschedule',
            self::STATUS_SETUJU => 'Jadwal Berlaku',
        ][$status] ?? ucfirst((string) $status);
    }
}
