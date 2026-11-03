<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'audit_log';

    public $timestamps = false; // karena pakai changed_at

    protected $fillable = [
        'entity', 'entity_id', 'aksi', 'changed_by_akun_id', 'changed_at', 'detail',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
        'detail' => 'array',
    ];

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(Akun::class, 'changed_by_akun_id');
    }
}
