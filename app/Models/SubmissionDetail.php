<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionDetail extends Model
{
    use HasUuids;

    protected $table = 'transaksi.rincian_pengajuan';
    protected $primaryKey = 'UUID';
    protected $keyType = 'string';
    public $incrementing = false;

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'last_update';

    protected $fillable = [
        'pengajuan_uuid',
        'nm_domain',
        'alamat_ip',
        'kapasitas_penyimpanan',
        'lokasi_server',
        'keterangan_keperluan',
        'file_lampiran',
        'id_creator',
        'id_updater',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'pengajuan_uuid', 'UUID');
    }

    // Alias for backward compatibility
    public function submission(): BelongsTo
    {
        return $this->pengajuan();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors for backward compatibility
    |--------------------------------------------------------------------------
    */
    public function getRequestedDomainAttribute(): ?string
    {
        return $this->nm_domain;
    }

    public function getHostingQuotaAttribute(): ?string
    {
        return $this->kapasitas_penyimpanan;
    }

    public function getVpsPurposeAttribute(): ?string
    {
        return $this->keterangan_keperluan;
    }
}
