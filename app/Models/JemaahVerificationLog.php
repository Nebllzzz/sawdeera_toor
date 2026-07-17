<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JemaahVerificationLog extends Model
{
    public const TYPE_ACCOUNT = 'account';
    public const TYPE_DATA = 'data';

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jemaah()
    {
        return $this->belongsTo(DataJemaah::class, 'jemaah_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
