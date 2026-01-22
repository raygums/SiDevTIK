<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitCategory extends Model
{
    use HasUuids;

    protected $table = 'referensi.kategori_unit';
    protected $primaryKey = 'UUID';
    protected $keyType = 'string';
    public $incrementing = false;

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'last_update';

    protected $fillable = [
        'nm_kategori',
    ];

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'kategori_uuid', 'UUID');
    }
}
