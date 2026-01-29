<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    use HasUuids;

    protected $table = 'transaksi.pengajuan';
    protected $primaryKey = 'UUID';
    protected $keyType = 'string';
    public $incrementing = false;

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'last_update';

    protected $fillable = [
        'no_tiket',
        'pengguna_uuid',
        'unit_kerja_uuid',
        'jenis_layanan_uuid',
        'status_uuid',
        'tgl_pengajuan',
        'id_creator',
        'id_updater',
    ];

    protected $casts = [
        'tgl_pengajuan' => 'date',
        'create_at' => 'datetime',
        'last_update' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Ticket Number Generator
    |--------------------------------------------------------------------------
    */
    public static function generateTicketNumber(): string
    {
        $prefix = 'TIK';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 4));
        
        return "{$prefix}-{$date}-{$random}";
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_uuid', 'UUID');
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_kerja_uuid', 'UUID');
    }

    public function jenisLayanan(): BelongsTo
    {
        return $this->belongsTo(JenisLayanan::class, 'jenis_layanan_uuid', 'UUID');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusPengajuan::class, 'status_uuid', 'UUID');
    }

    public function rincian(): HasOne
    {
        return $this->hasOne(SubmissionDetail::class, 'pengajuan_uuid', 'UUID');
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(SubmissionLog::class, 'pengajuan_uuid', 'UUID');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_creator', 'UUID');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_updater', 'UUID');
    }

    /*
    |--------------------------------------------------------------------------
    | Aliases for backward compatibility
    |--------------------------------------------------------------------------
    */
    public function applicant(): BelongsTo
    {
        return $this->pengguna();
    }

    public function unit(): BelongsTo
    {
        return $this->unitKerja();
    }

    public function details(): HasOne
    {
        return $this->rincian();
    }

    public function logs(): HasMany
    {
        return $this->riwayat();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getTicketNumberAttribute(): string
    {
        return $this->no_tiket;
    }

    public function getServiceTypeAttribute(): ?string
    {
        return $this->jenisLayanan?->nm_layanan;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status?->nm_status ?? '-';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status?->color ?? 'gray';
    }
}
