<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenJemaah extends Model
{
    protected $table = 'dokumen_jemaah';
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['verified_at' => 'datetime'];
    }

    public function jemaah()
    {
        return $this->belongsTo(DataJemaah::class, 'jemaah_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
