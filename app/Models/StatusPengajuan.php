<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusPengajuan extends Model
{
    use HasUuids;

    protected $table = 'referensi.status_pengajuan';
    protected $primaryKey = 'UUID';
    protected $keyType = 'string';
    public $incrementing = false;

    const CREATED_AT = 'create_at';
    const UPDATED_AT = null; // Tidak ada updated_at

    protected $fillable = [
        'nm_status',
    ];

    // Konstanta status
    public const DRAFT = 'draft';
    public const DIAJUKAN = 'diajukan';
    public const DIVERIFIKASI = 'diverifikasi';
    public const DIPROSES = 'diproses';
    public const SELESAI = 'selesai';
    public const DITOLAK = 'ditolak';

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'status_uuid', 'UUID');
    }

    /**
     * Get color for status badge
     */
    public function getColorAttribute(): string
    {
        return match(strtolower($this->nm_status)) {
            'draft' => 'gray',
            'diajukan' => 'blue',
            'diverifikasi' => 'yellow',
            'diproses' => 'purple',
            'selesai' => 'green',
            'ditolak' => 'red',
            default => 'gray',
        };
    }
}
