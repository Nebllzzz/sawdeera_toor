<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataJemaah extends Model
{
    protected $table = 'data_jemaah';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function dokumen(){
        return $this->hasMany(DokumenJemaah::class,'jemaah_id');
    }
}
