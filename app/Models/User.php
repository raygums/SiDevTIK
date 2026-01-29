<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

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
        'UUID',
        'sso_id',
        'nm',           
        'usn',          
        'email',
        'ktp',
        'tgl_lahir',
        'kata_sandi',
        'peran_uuid',
        'id_sdm',
        'id_pd',
        'a_aktif',
        'last_login_at',
        'last_login_ip',
        'remember_token',
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
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'a_aktif' => 'boolean',
        'tgl_lahir' => 'date',
        'last_login_at' => 'datetime',
        'create_at' => 'datetime',
        'last_update' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->UUID)) {
                $model->UUID = (string) Str::uuid();
            }
        });
    }

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

    /**
     * Get user's role name (helper accessor)
     */
    public function getRoleAttribute(): string
    {
        return strtolower($this->peran?->nm_peran ?? 'pengguna');
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return strtolower($this->peran?->nm_peran ?? '') === strtolower($role);
    }

    /**
     * Get display name - use nm column
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->nm ?? $this->usn ?? 'User';
    }
}