<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataJemaah extends Model
{
    public const BASE_REQUIRED_DOCUMENTS = [
        'ktp', 'kartu_keluarga', 'visa', 'vaksin', 'foto_4x6',
    ];

    public const MARRIAGE_DOCUMENT = 'buku_nikah';

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

    public function dokumen()
    {
        return $this->hasMany(DokumenJemaah::class, 'jemaah_id');
    }

    public function verificationLogs()
    {
        return $this->hasMany(JemaahVerificationLog::class, 'jemaah_id');
    }

    public function requiredDocumentTypes(): array
    {
        return $this->status_pernikahan === 'menikah'
            ? [...self::BASE_REQUIRED_DOCUMENTS, self::MARRIAGE_DOCUMENT]
            : self::BASE_REQUIRED_DOCUMENTS;
    }

    public function hasVerifiedRequiredDocuments(): bool
    {
        $documents = $this->relationLoaded('dokumen')
            ? $this->dokumen
            : $this->dokumen()->get();

        return collect($this->requiredDocumentTypes())->every(
            fn (string $type) => $documents->firstWhere('jenis_dokumen', $type)?->status === 'diverifikasi'
        );
    }
}
