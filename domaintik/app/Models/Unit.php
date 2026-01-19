<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $table = 'referensi.units';

    protected $fillable = [
        'category_id',
        'name',
        'code',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(UnitCategory::class, 'category_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'unit_id');
    }
}
