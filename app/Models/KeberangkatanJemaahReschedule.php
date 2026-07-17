<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeberangkatanJemaahReschedule extends Model
{
    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_DISETUJUI = 'disetujui';
    public const STATUS_DITOLAK = 'ditolak';

    protected $table = 'keberangkatan_jemaah_reschedules';
    protected $guarded = ['id'];

    protected $casts = [
        'diajukan_pada' => 'datetime',
        'diproses_pada' => 'datetime',
    ];

    public function keberangkatanJemaah()
    {
        return $this->belongsTo(KeberangkatanJemaah::class, 'keberangkatan_jemaah_id');
    }

    public function jemaah()
    {
        return $this->belongsTo(DataJemaah::class, 'jemaah_id');
    }

    public function keberangkatanAsal()
    {
        return $this->belongsTo(Keberangkatan::class, 'keberangkatan_asal_id');
    }

    public function keberangkatanTujuan()
    {
        return $this->belongsTo(Keberangkatan::class, 'keberangkatan_tujuan_id');
    }

    public function pemroses()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
