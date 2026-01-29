<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $table = 'referensi.service_types';

    protected $fillable = [
        'name',
    ];
}
