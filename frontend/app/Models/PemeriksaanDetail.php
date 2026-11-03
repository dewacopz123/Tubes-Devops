<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemeriksaanDetail extends Model
{
    protected $table = 'pemeriksaan_detail';

    protected $fillable = [
        'pemeriksaan_id', 'parameter_nama', 'nilai', 'satuan',
        'rujukan_min', 'rujukan_max', 'flag',
    ];

    public function pemeriksaan(): BelongsTo
    {
        return $this->belongsTo(Pemeriksaan::class, 'pemeriksaan_id');
    }
}
