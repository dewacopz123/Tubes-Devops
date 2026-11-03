<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemeriksaanFile extends Model
{
    protected $table = 'pemeriksaan_file';

    public $timestamps = false; // karena pakai uploaded_at, bukan created_at/updated_at

    protected $fillable = [
        'pemeriksaan_id', 'file_path', 'file_type', 'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function pemeriksaan(): BelongsTo
    {
        return $this->belongsTo(Pemeriksaan::class, 'pemeriksaan_id');
    }
}
