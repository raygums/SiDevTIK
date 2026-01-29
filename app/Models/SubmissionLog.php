<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionLog extends Model
{
    use HasUuids;

    protected $table = 'audit.riwayat_pengajuan';
    protected $primaryKey = 'UUID';
    protected $keyType = 'string';
    public $incrementing = false;

    const CREATED_AT = 'create_at';
    const UPDATED_AT = null; // Tidak ada updated_at

    protected $fillable = [
        'pengajuan_uuid',
        'status_lama_uuid',
        'status_baru_uuid',
        'catatan_log',
        'id_creator',
    ];

    protected $casts = [
        'create_at' => 'datetime',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'pengajuan_uuid', 'UUID');
    }

    public function statusLama(): BelongsTo
    {
        return $this->belongsTo(StatusPengajuan::class, 'status_lama_uuid', 'UUID');
    }

    public function statusBaru(): BelongsTo
    {
        return $this->belongsTo(StatusPengajuan::class, 'status_baru_uuid', 'UUID');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_creator', 'UUID');
    }

    // Aliases for backward compatibility
    public function submission(): BelongsTo
    {
        return $this->pengajuan();
    }

    public function user(): BelongsTo
    {
        return $this->creator();
    }
}
