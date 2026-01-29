<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peran extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'akun.peran';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'UUID';

    /**
     * The "type" of the primary key ID.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nm_peran',
        'a_aktif',
        'create_at',
        'last_update',
        'id_creator',
        'id_updater',
    ];

    /**
     * Relationship to Users
     */
    public function pengguna()
    {
        return $this->hasMany(User::class, 'peran_uuid', 'UUID');
    }
}
