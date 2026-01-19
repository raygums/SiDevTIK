<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitCategory extends Model
{
    protected $table = 'referensi.unit_categories';

    protected $fillable = [
        'name',
    ];

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'category_id');
    }
}
