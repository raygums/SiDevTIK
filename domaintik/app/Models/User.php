<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nomor_identitas',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isVerifikator(): bool
    {
        return $this->role === 'verifikator';
    }

    public function isEksekutor(): bool
    {
        return $this->role === 'eksekutor';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'applicant_id');
    }

    public function assignedVerifications(): HasMany
    {
        return $this->hasMany(Submission::class, 'assigned_verifier_id');
    }

    public function assignedExecutions(): HasMany
    {
        return $this->hasMany(Submission::class, 'assigned_executor_id');
    }
}
