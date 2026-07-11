<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranTahapan extends Model
{
    protected $table = 'pembayaran_tahapan';
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'jatuh_tempo' => 'date',
            'uploaded_at' => 'datetime',
            'verified_at' => 'datetime',
            'nominal' => 'decimal:2',
            'persentase' => 'decimal:4',
        ];
    }

    public function pembayaran() { return $this->belongsTo(Pembayaran::class); }
    public function verifier() { return $this->belongsTo(User::class, 'verified_by'); }
}
