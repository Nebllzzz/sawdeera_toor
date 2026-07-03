<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenJemaah extends Model
{
    protected $table = 'dokumen_jemaah';
    protected $guarded = ['id'];

    public function jemaah()
    {
        return $this->belongsTo(DataJemaah::class, 'jemaah_id');
    }
}
