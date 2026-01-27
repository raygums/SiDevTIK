<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasUuids;

    protected $table = 'referensi.unit_kerja';
    protected $primaryKey = 'UUID';
    protected $keyType = 'string';
    public $incrementing = false;

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'last_update';

    protected $fillable = [
        'nm_lmbg',
        'kode_unit',
        'kategori_uuid',
        'a_aktif',
    ];

    protected $casts = [
        'a_aktif' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(UnitCategory::class, 'kategori_uuid', 'UUID');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'unit_kerja_uuid', 'UUID');
    }
}
