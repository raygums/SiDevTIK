<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisLayanan extends Model
{
    use HasUuids;

    protected $table = 'referensi.jenis_layanan';
    protected $primaryKey = 'UUID';
    protected $keyType = 'string';
    public $incrementing = false;

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'last_update';

    protected $fillable = [
        'nm_layanan',
        'deskripsi',
        'a_aktif',
    ];

    protected $casts = [
        'a_aktif' => 'boolean',
    ];

    // Konstanta tipe layanan
    public const DOMAIN = 'domain';
    public const HOSTING = 'hosting';
    public const VPS = 'vps';

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'jenis_layanan_uuid', 'UUID');
    }
}
