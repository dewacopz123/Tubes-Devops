<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pendaftaran extends Model
{
    protected $table = 'pendaftaran';

    protected $fillable = [
        'pasien_id', 'no_antrian', 'tgl_pendaftaran', 'status', 'surat_rujukan_path',
    ];

    protected $casts = [
        'tgl_pendaftaran' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function pemeriksaan(): HasMany
    {
        return $this->hasMany(Pemeriksaan::class, 'pendaftaran_id');
    }
}
