<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $guarded = ['id'];

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
}
