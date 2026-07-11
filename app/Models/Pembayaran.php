<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['total_tagihan' => 'decimal:2'];
    }

    public function keberangkatanJemaah()
    {
        return $this->hasOne(
            KeberangkatanJemaah::class,
            'keberangkatan_id',
            'keberangkatan_id'
        );
    }

    public function jemaah()
    {
        return $this->belongsTo(DataJemaah::class, 'jemaah_id');
    }

    public function pengajuan()
    {
        return $this->belongsTo(KeberangkatanJemaah::class, 'keberangkatan_jemaah_id');
    }

    public function tahapan()
    {
        return $this->hasMany(PembayaranTahapan::class)->orderBy('urutan');
    }
}
