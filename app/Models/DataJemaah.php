<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataJemaah extends Model
{
    protected $table = 'data_jemaah';
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date:Y-m-d',
            'tanggal_terbit_paspor' => 'date:Y-m-d',
            'tanggal_kedaluwarsa_paspor' => 'date:Y-m-d',
            'diverifikasi_pada' => 'datetime',
        ];
    }

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
