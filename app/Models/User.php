<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     */
    protected $table = 'akun.pengguna';

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
        'nm',
        'usn',
        'email',
        'ktp',
        'tgl_lahir',
        'kata_sandi',
        'peran_uuid',
        'a_aktif',
        'create_at',
        'last_update',
        'last_sync',
        'id_creator',
        'id_updater',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'kata_sandi',
    ];

    /**
     * Get the password for the user (Laravel Auth compatibility).
     */
    public function getAuthPassword()
    {
        return $this->kata_sandi;
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'UUID';
    }

    /**
     * Relationship to Peran (Role)
     */
    public function peran()
    {
        return $this->belongsTo(Peran::class, 'peran_uuid', 'UUID');
    }
}