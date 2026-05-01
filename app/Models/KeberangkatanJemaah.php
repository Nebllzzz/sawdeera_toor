<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeberangkatanJemaah extends Model
{
    protected $table = 'keberangkatan_jemaah';

    protected $guarded = ['id'];

    public $timestamps = false;

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
}
