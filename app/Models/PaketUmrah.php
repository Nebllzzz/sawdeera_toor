<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketUmrah extends Model
{
    protected $table = 'paket_umrah';
    protected $guarded = ['id'];

    public function hotelMakkah()
    {
        return $this->belongsTo(Hotel::class, 'hotel_makkah_id');
    }

    public function hotelMadinah()
    {
        return $this->belongsTo(Hotel::class, 'hotel_madinah_id');
    }

    public function fasilitas()
    {
        return $this->hasMany(PaketFasilitas::class, 'paket_id');
    }

    public function program()
    {
        return $this->hasMany(PaketProgram::class, 'paket_id')
            ->orderBy('hari', 'asc');
    }

    public function keberangkatanJemaah()
    {
        return $this->hasMany(KeberangkatanJemaah::class, 'paket_umrah_id');
    }

    public function keberangkatan()
    {
        return $this->hasMany(Keberangkatan::class, 'paket_id');
    }
}
