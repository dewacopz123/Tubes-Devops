<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Akun extends Model
{
    protected $table = 'akun';

    protected $fillable = [
        'username', 'email', 'password_hash', 'role',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pasien(): HasOne
    {
        return $this->hasOne(Pasien::class, 'akun_id');
    }

    public function dokter(): HasOne
    {
        return $this->hasOne(Dokter::class, 'akun_id');
    }

    public function petugasLab(): HasOne
    {
        return $this->hasOne(PetugasLab::class, 'akun_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'changed_by_akun_id');
    }
}
