<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keberangkatan extends Model
{
    protected $table = 'keberangkatan';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_keberangkatan' => 'datetime',
        'tanggal_pulang' => 'datetime',
    ];

    public function maskapaiBerangkat()
    {
        return $this->belongsTo(Maskapai::class,'maskapai_berangkat_id');
    }

    public function maskapaiPulang()
    {
        return $this->belongsTo(Maskapai::class,'maskapai_pulang_id');
    }

    public function leader()
    {
        return $this->belongsTo(TourLeader::class,'tour_leader_id');
    }

    public function jemaah()
    {
        return $this->hasMany(KeberangkatanJemaah::class);
    }
}
