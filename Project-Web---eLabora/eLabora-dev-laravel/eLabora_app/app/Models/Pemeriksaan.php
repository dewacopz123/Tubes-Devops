<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemeriksaan extends Model
{
    protected $table = 'pemeriksaan';

    protected $fillable = [
        'pendaftaran_id', 'kategori_id', 'dokter_id', 'petugas_lab_id',
        'no_lab', 'tgl_pemeriksaan', 'status_validasi', 'catatan',
    ];

    protected $casts = [
        'tgl_pemeriksaan' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }

    public function petugasLab(): BelongsTo
    {
        return $this->belongsTo(PetugasLab::class, 'petugas_lab_id');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(PemeriksaanDetail::class, 'pemeriksaan_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(PemeriksaanFile::class, 'pemeriksaan_id');
    }
}
