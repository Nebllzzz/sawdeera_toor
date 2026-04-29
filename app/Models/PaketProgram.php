<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketProgram extends Model
{
    protected $table = 'paket_program';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function paket()
    {
        return $this->belongsTo(PaketUmrah::class,'paket_id');
    }
}
