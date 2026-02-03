<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: LoginLog (Riwayat Login)
 * 
 * Purpose:
 * - Eloquent model untuk tabel audit.riwayat_login
 * - Mencatat history login (berhasil/gagal) untuk audit trail
 * - Relasi dengan User model untuk eager loading
 * 
 * Database:
 * - Schema: audit
 * - Table: riwayat_login
 * - Primary Key: UUID
 * 
 * Features:
 * - UUID-based primary key
 * - Custom timestamp (create_at only, no updated_at)
 * - Relationship dengan User model
 * - Query scopes untuk filtering
 * 
 * Performance:
 * - Indexed kolom: pengguna_uuid, create_at, status_akses, alamat_ip
 * - Supports eager loading untuk prevent N+1 queries
 * 
 * Security:
 * - Input sanitization dilakukan di Service layer
 * - Fillable attributes terkontrol
 * - Read-only setelah create (no updates/deletes untuk audit trail)
 * 
 * @property string $UUID Primary key
 * @property string|null $pengguna_uuid Foreign key to User
 * @property string|null $alamat_ip IP address (IPv4/IPv6)
 * @property string|null $perangkat User agent string
 * @property string $status_akses Login status (BERHASIL, GAGAL_PASSWORD, dll)
 * @property string|null $keterangan Additional details/error message
 * @property \Carbon\Carbon $create_at Timestamp login attempt
 * 
 * @property-read User|null $pengguna Related user model
 * 
 * @author Domain TIK Development Team
 * @version 1.0.0
 * @created 2026-02-03
 */
class LoginLog extends Model
{
    use HasUuids;

    // ==========================================
    // TABLE CONFIGURATION
    // ==========================================
    
    /**
     * Nama tabel dengan schema prefix
     * 
     * PostgreSQL multi-schema: audit.riwayat_login
     * 
     * @var string
     */
    protected $table = 'audit.riwayat_login';

    /**
     * Primary key column name
     * 
     * Menggunakan UUID sebagai primary key
     * 
     * @var string
     */
    protected $primaryKey = 'UUID';

    /**
     * Primary key type
     * 
     * UUID adalah string, bukan integer
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Primary key auto-increment
     * 
     * UUID di-generate oleh database, bukan auto-increment integer
     * 
     * @var bool
     */
    public $incrementing = false;

    // ==========================================
    // TIMESTAMP CONFIGURATION
    // ==========================================
    
    /**
     * Disable Laravel default timestamps (created_at, updated_at)
     * 
     * Tabel ini hanya memiliki create_at (custom naming)
     * Tidak ada updated_at karena log bersifat immutable
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * Custom timestamp column name
     * 
     * Kita gunakan create_at (bukan created_at)
     * sesuai standar naming project ini
     * 
     * CONST digunakan untuk reference di code lain
     */
    const CREATED_AT = 'create_at';

    // ==========================================
    // MASS ASSIGNMENT PROTECTION
    // ==========================================
    
    /**
     * Attributes yang boleh di-mass assign
     * 
     * UUID tidak perlu di-fill karena auto-generated
     * 
     * Security Note:
     * - Input akan disanitasi di Service layer sebelum sampai sini
     * - Tidak ada user input yang langsung masuk ke fillable
     * 
     * @var array<string>
     */
    protected $fillable = [
        'pengguna_uuid',    // FK to User
        'alamat_ip',        // IP address (sanitized)
        'perangkat',        // User agent (sanitized)
        'status_akses',     // Login status enum
        'keterangan',       // Additional notes
        'create_at',        // Timestamp (auto-filled by DB)
    ];

    // ==========================================
    // ATTRIBUTE CASTING
    // ==========================================
    
    /**
     * Type casting untuk attributes
     * 
     * create_at → Carbon instance untuk date manipulation
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'create_at' => 'datetime',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================
    
    /**
     * Relasi ke User Model
     * 
     * LoginLog belongsTo User
     * 
     * Relationship ini digunakan untuk:
     * - Eager loading: LoginLog::with('pengguna')->get()
     * - Lazy loading: $log->pengguna->nama
     * 
     * Foreign Key: pengguna_uuid
     * Owner Key: UUID (di tabel akun.pengguna)
     * 
     * Nullable: true (karena ON DELETE SET NULL)
     * 
     * @return BelongsTo<User, LoginLog>
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(
            User::class, 
            'pengguna_uuid', // Foreign key di tabel ini
            'UUID'           // Primary key di tabel User
        )->withDefault([
            'nama' => 'User Deleted',
            'username' => 'N/A',
        ]); // Default values jika user sudah dihapus
    }

    // ==========================================
    // QUERY SCOPES
    // ==========================================
    
    /**
     * Scope: Filter by user UUID
     * 
     * Usage: LoginLog::byUser($userUuid)->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $userUuid
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, string $userUuid)
    {
        return $query->where('pengguna_uuid', $userUuid);
    }

    /**
     * Scope: Filter by status
     * 
     * Usage: LoginLog::byStatus('BERHASIL')->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status_akses', $status);
    }

    /**
     * Scope: Successful logins only
     * 
     * Usage: LoginLog::successful()->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status_akses', 'BERHASIL');
    }

    /**
     * Scope: Failed logins only
     * 
     * Usage: LoginLog::failed()->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('status_akses', '!=', 'BERHASIL');
    }

    /**
     * Scope: Recent logs (ordered by create_at DESC)
     * 
     * Usage: LoginLog::recent()->take(100)->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('create_at', 'desc');
    }

    /**
     * Scope: Filter by date range
     * 
     * Usage: LoginLog::dateRange('2026-01-01', '2026-01-31')->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\Carbon\Carbon $startDate
     * @param string|\Carbon\Carbon $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('create_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Filter by IP address
     * 
     * Usage: LoginLog::fromIp('192.168.1.1')->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $ip
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromIp($query, string $ip)
    {
        return $query->where('alamat_ip', $ip);
    }

    /**
     * Scope: Today's logs
     * 
     * Usage: LoginLog::today()->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeToday($query)
    {
        return $query->whereDate('create_at', today());
    }

    /**
     * Scope: This week's logs
     * 
     * Usage: LoginLog::thisWeek()->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('create_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    // ==========================================
    // ACCESSOR & HELPER METHODS
    // ==========================================
    
    /**
     * Check if login was successful
     * 
     * Usage: if ($log->isSuccessful()) { ... }
     * 
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->status_akses === 'BERHASIL';
    }

    /**
     * Check if login failed
     * 
     * Usage: if ($log->isFailed()) { ... }
     * 
     * @return bool
     */
    public function isFailed(): bool
    {
        return !$this->isSuccessful();
    }

    /**
     * Get human-readable status
     * 
     * Usage: $log->getStatusLabel()
     * 
     * @return string
     */
    public function getStatusLabel(): string
    {
        return match($this->status_akses) {
            'BERHASIL' => 'Login Berhasil',
            'GAGAL_PASSWORD' => 'Password Salah',
            'GAGAL_SUSPEND' => 'Akun Suspended',
            'GAGAL_NOT_FOUND' => 'User Tidak Ditemukan',
            'GAGAL_SSO' => 'SSO Authentication Failed',
            default => $this->status_akses,
        };
    }

    /**
     * Get short device info (simplified user agent)
     * 
     * Usage: $log->getDeviceInfo()
     * 
     * @return string
     */
    public function getDeviceInfo(): string
    {
        if (!$this->perangkat) {
            return 'Unknown Device';
        }

        // Extract basic info from user agent
        // Example: "Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0"
        // Returns: "Chrome on Windows"
        
        $agent = $this->perangkat;
        
        // Detect browser
        $browser = 'Unknown Browser';
        if (str_contains($agent, 'Chrome')) $browser = 'Chrome';
        elseif (str_contains($agent, 'Firefox')) $browser = 'Firefox';
        elseif (str_contains($agent, 'Safari')) $browser = 'Safari';
        elseif (str_contains($agent, 'Edge')) $browser = 'Edge';
        
        // Detect OS
        $os = 'Unknown OS';
        if (str_contains($agent, 'Windows')) $os = 'Windows';
        elseif (str_contains($agent, 'Macintosh')) $os = 'MacOS';
        elseif (str_contains($agent, 'Linux')) $os = 'Linux';
        elseif (str_contains($agent, 'Android')) $os = 'Android';
        elseif (str_contains($agent, 'iOS')) $os = 'iOS';
        
        return "{$browser} on {$os}";
    }
}
