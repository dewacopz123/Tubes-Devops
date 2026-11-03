<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $fillable = [
        'nama', 'deskripsi',
    ];

    public function pemeriksaan(): HasMany
    {
        return $this->hasMany(Pemeriksaan::class, 'kategori_id');
    }
}
