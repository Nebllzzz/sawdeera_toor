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

    public function isFullyVerified(): bool
    {
        $steps = $this->relationLoaded('tahapan') ? $this->tahapan : $this->tahapan()->get();

        return $steps->isNotEmpty() && $steps->every(fn ($step) => $step->status === 'diverifikasi');
    }

    public function isInvoiceAvailable(): bool
    {
        $steps = $this->relationLoaded('tahapan') ? $this->tahapan : $this->tahapan()->get();

        return $this->status === 'diverifikasi'
            && $steps->count() === (int) $this->jumlah_tahap
            && $steps->every(fn ($step) => $step->status === 'diverifikasi'
                && filled($step->bukti_pembayaran)
                && filled($step->uploaded_at));
    }
}
