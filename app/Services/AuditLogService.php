<?php

namespace App\Services;

use App\Models\User;
use App\Models\LoginLog;
use App\Models\SubmissionLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Service untuk mengelola Log Audit
 * - Login Activity (Full History dengan audit.riwayat_login)
 * - Submission Status Changes
 * 
 * @author Domain TIK Development Team
 * @version 2.0.0 - Added comprehensive login history tracking
 * @updated 2026-02-03
 */
class AuditLogService
{
    // ==========================================
    // LOGIN HISTORY RECORDING
    // ==========================================
    
    /**
     * Record login attempt ke tabel audit.riwayat_login
     * 
     * Method ini dipanggil setiap kali ada login attempt (berhasil/gagal)
     * untuk keperluan audit trail dan security monitoring.
     * 
     * Features:
     * - Automatic IP detection (support proxy headers)
     * - User agent sanitization
     * - Flexible status enum
     * - Error detail logging
     * - Database transaction safety
     * 
     * Status Values:
     * - BERHASIL: Login sukses
     * - GAGAL_PASSWORD: Password salah
     * - GAGAL_SUSPEND: Akun suspended/tidak aktif
     * - GAGAL_NOT_FOUND: User tidak ditemukan
     * - GAGAL_SSO: SSO authentication failed
     * 
     * Usage Examples:
     * 
     * // Successful login
     * $this->auditLogService->recordLoginLog(
     *     userUuid: $user->UUID,
     *     status: 'BERHASIL',
     *     request: $request
     * );
     * 
     * // Failed login with custom note
     * $this->auditLogService->recordLoginLog(
     *     userUuid: null, // User tidak ditemukan
     *     status: 'GAGAL_NOT_FOUND',
     *     request: $request,
     *     keterangan: "Username '{$username}' tidak terdaftar"
     * );
     * 
     * // Failed login - account suspended
     * $this->auditLogService->recordLoginLog(
     *     userUuid: $user->UUID,
     *     status: 'GAGAL_SUSPEND',
     *     request: $request,
     *     keterangan: "Akun dinonaktifkan oleh admin"
     * );
     * 
     * @param string|null $userUuid UUID user (null jika user tidak ditemukan)
     * @param string $status Status akses (BERHASIL, GAGAL_PASSWORD, dll)
     * @param \Illuminate\Http\Request|null $request Request object untuk extract IP dan User Agent
     * @param string|null $keterangan Detail tambahan (error message, notes)
     * @param string|null $customIp Override IP address (optional)
     * @param string|null $customUserAgent Override user agent (optional)
     * @return LoginLog|null Created log record atau null jika gagal
     * 
     * @throws \Exception Jika terjadi error saat save ke database
     */
    public function recordLoginLog(
        ?string $userUuid,
        string $status,
        ?\Illuminate\Http\Request $request = null,
        ?string $keterangan = null,
        ?string $customIp = null
    ): ?LoginLog {
        try {
            // ==========================================
            // IP ADDRESS DETECTION
            // ==========================================
            
            /**
             * Priority order untuk mendapatkan real IP:
             * 1. Custom IP (jika di-provide)
             * 2. X-Forwarded-For (jika behind proxy/load balancer)
             * 3. X-Real-IP (nginx proxy)
             * 4. Remote Address (direct connection)
             * 
             * Security Note:
             * - X-Forwarded-For bisa di-spoof, hanya trust jika behind trusted proxy
             * - Batasi panjang IP untuk prevent injection
             */
            $ipAddress = $customIp;
            
            if (!$ipAddress && $request) {
                // Check proxy headers first
                $ipAddress = $request->header('X-Forwarded-For');
                
                if ($ipAddress) {
                    // X-Forwarded-For bisa multiple IPs (client, proxy1, proxy2)
                    // Ambil IP pertama (client IP)
                    $ipAddress = trim(explode(',', $ipAddress)[0]);
                } else {
                    // Fallback: X-Real-IP atau Remote Address
                    $ipAddress = $request->header('X-Real-IP') 
                              ?? $request->ip();
                }
            }
            
            // Sanitasi IP address
            $ipAddress = $this->sanitizeIpAddress($ipAddress);

            // ==========================================
            // CREATE LOG RECORD
            // ==========================================
            
            /**
             * Insert ke audit.riwayat_login
             * 
             * Database akan auto-generate:
             * - UUID (primary key)
             * - create_at (current timestamp)
             * 
             * Kita provide:
             * - pengguna_uuid (nullable)
             * - alamat_ip (nullable)
             * - status_akses (required)
             * - keterangan (nullable)
             */
            $log = LoginLog::create([
                'pengguna_uuid' => $userUuid,
                'alamat_ip'     => $ipAddress,
                'status_akses'  => $status,
                'keterangan'    => $keterangan,
            ]);

            // Log berhasil dibuat
            \Log::info('Login attempt logged', [
                'log_uuid'      => $log->UUID,
                'user_uuid'     => $userUuid,
                'status'        => $status,
                'ip'            => $ipAddress,
            ]);

            return $log;

        } catch (\Exception $e) {
            // Jangan throw exception ke atas
            // Logging error tidak boleh mengganggu flow utama
            \Log::error('Failed to record login log', [
                'error'     => $e->getMessage(),
                'user_uuid' => $userUuid,
                'status'    => $status,
                'trace'     => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Sanitize IP address untuk security
     * 
     * - Validasi format IPv4/IPv6
     * - Batasi panjang max 45 chars
     * - Strip dangerous characters
     * 
     * @param string|null $ip Raw IP address
     * @return string|null Sanitized IP atau null
     */
    private function sanitizeIpAddress(?string $ip): ?string
    {
        if (!$ip) {
            return null;
        }

        // Remove whitespace
        $ip = trim($ip);

        // Validate IPv4 or IPv6
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
            // Batasi panjang max 45 chars (IPv6 max length)
            return substr($ip, 0, 45);
        }

        // Invalid IP format
        \Log::warning('Invalid IP address format', ['ip' => $ip]);
        return null;
    }



    // ==========================================
    // LOGIN HISTORY QUERIES - NEW VERSION
    // ==========================================
    
    /**
     * Get comprehensive login history dari audit.riwayat_login
     * 
     * VERSION 2.0: Full history tracking dengan tabel dedicated
     * 
     * Features:
     * - Full login history (semua attempt, bukan hanya last_login)
     * - Filter by user, status, date range, IP
     * - Eager loading untuk prevent N+1
     * - Pagination support
     * - Support berbagai status (BERHASIL, GAGAL_PASSWORD, GAGAL_SUSPEND, dll)
     * 
     * Filter Options:
     * - search: Cari by nama/email/username user
     * - user_uuid: Specific user UUID
     * - status: Filter by status_akses (BERHASIL, GAGAL_PASSWORD, dll)
     * - date_from: Start date (create_at >=)
     * - date_to: End date (create_at <=)
     * - ip_address: Filter by specific IP
     * - only_successful: true = hanya BERHASIL
     * - only_failed: true = hanya GAGAL_*
     * - period: Shortcut (today, yesterday, this_week, this_month, last_7_days, last_30_days)
     * 
     * Usage Example:
     * 
     * // Get all successful logins today
     * $logs = $auditService->getLoginHistory([
     *     'only_successful' => true,
     *     'period' => 'today'
     * ]);
     * 
     * // Get failed login attempts for specific user
     * $logs = $auditService->getLoginHistory([
     *     'user_uuid' => $userUuid,
     *     'only_failed' => true,
     *     'date_from' => '2026-01-01',
     *     'date_to' => '2026-01-31'
     * ]);
     * 
     * // Security monitoring: Find suspicious IPs
     * $logs = $auditService->getLoginHistory([
     *     'ip_address' => '192.168.1.1',
     *     'only_failed' => true
     * ]);
     * 
     * @param array $filters Filter parameters
     * @param int $perPage Items per page
     * @return LengthAwarePaginator
     */
    public function getLoginHistory(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = LoginLog::query()
            ->with([
                'pengguna' => function ($q) {
                    // Eager load user data + role (prevent N+1)
                    $q->select('UUID', 'nm', 'email', 'usn', 'peran_uuid', 'a_aktif')
                      ->with('peran:UUID,nm_peran');
                }
            ]);

        // ==========================================
        // APPLY FILTERS
        // ==========================================

        // Filter: Search by nama, email, username user
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('pengguna', function ($q) use ($search) {
                $q->where('nm', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('usn', 'ILIKE', "%{$search}%");
            });
        }

        // Filter: Specific user UUID
        if (!empty($filters['user_uuid'])) {
            $query->where('pengguna_uuid', $filters['user_uuid']);
        }

        // Filter: Specific status akses
        if (!empty($filters['status'])) {
            $query->where('status_akses', $filters['status']);
        }

        // Filter: Only successful logins
        if (!empty($filters['only_successful'])) {
            $query->where('status_akses', 'BERHASIL');
        }

        // Filter: Only failed logins
        if (!empty($filters['only_failed'])) {
            $query->where('status_akses', '!=', 'BERHASIL');
        }

        // Filter: Specific IP address
        if (!empty($filters['ip_address'])) {
            $query->where('alamat_ip', $filters['ip_address']);
        }

        // Filter: Date range (create_at)
        if (!empty($filters['date_from'])) {
            $query->whereDate('create_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('create_at', '<=', $filters['date_to']);
        }

        // Filter: Period shortcuts
        if (!empty($filters['period'])) {
            switch ($filters['period']) {
                case 'today':
                    $query->whereDate('create_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('create_at', today()->subDay());
                    break;
                case 'this_week':
                    $query->whereBetween('create_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('create_at', now()->month)
                          ->whereYear('create_at', now()->year);
                    break;
                case 'last_7_days':
                    $query->where('create_at', '>=', now()->subDays(7));
                    break;
                case 'last_30_days':
                    $query->where('create_at', '>=', now()->subDays(30));
                    break;
            }
        }

        // ==========================================
        // SORTING & PAGINATION
        // ==========================================

        // Sort by create_at DESC (newest first)
        $query->orderBy('create_at', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Get ALL login history without pagination (for merging purposes)
     * 
     * Digunakan ketika ingin menggabungkan dengan submission logs.
     * Tidak menggunakan pagination agar tidak conflict dengan manual pagination di controller.
     * 
     * @param array $filters Filter parameters (sama seperti getLoginHistory)
     * @return \Illuminate\Support\Collection
     */
    public function getLoginHistoryCollection(array $filters = []): \Illuminate\Support\Collection
    {
        $query = LoginLog::query()
            ->with([
                'pengguna' => function ($q) {
                    $q->select('UUID', 'nm', 'email', 'usn', 'peran_uuid', 'a_aktif')
                      ->with('peran:UUID,nm_peran');
                }
            ]);

        // Apply same filters as getLoginHistory
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('pengguna', function ($q) use ($search) {
                $q->where('nm', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('usn', 'ILIKE', "%{$search}%");
            });
        }

        if (!empty($filters['user_uuid'])) {
            $query->where('pengguna_uuid', $filters['user_uuid']);
        }

        if (!empty($filters['status'])) {
            $query->where('status_akses', $filters['status']);
        }

        if (!empty($filters['only_successful'])) {
            $query->where('status_akses', 'BERHASIL');
        }

        if (!empty($filters['only_failed'])) {
            $query->where('status_akses', '!=', 'BERHASIL');
        }

        if (!empty($filters['ip_address'])) {
            $query->where('alamat_ip', $filters['ip_address']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('create_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('create_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['period'])) {
            switch ($filters['period']) {
                case 'today':
                    $query->whereDate('create_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('create_at', today()->subDay());
                    break;
                case 'this_week':
                    $query->whereBetween('create_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('create_at', now()->month)
                          ->whereYear('create_at', now()->year);
                    break;
                case 'last_7_days':
                    $query->where('create_at', '>=', now()->subDays(7));
                    break;
                case 'last_30_days':
                    $query->where('create_at', '>=', now()->subDays(30));
                    break;
            }
        }

        return $query->orderBy('create_at', 'desc')->get();
    }

    /**
     * Get comprehensive login statistics
     * 
     * Dashboard metrics untuk monitoring login activity
     * 
     * Returns:
     * - Total login attempts (berhasil + gagal)
     * - Successful vs failed counts
     * - Success rate percentage
     * - Unique users (yang pernah login berhasil)
     * - Activity trends (today, this week)
     * - Failed login breakdown by status
     * - Top 5 most active IPs (untuk detect brute force)
     * 
     * Usage:
     * 
     * // Get all-time statistics
     * $stats = $auditService->getLoginStatisticsNew();
     * 
     * // Get statistics for specific period
     * $stats = $auditService->getLoginStatisticsNew([
     *     'date_from' => '2026-01-01',
     *     'date_to' => '2026-01-31'
     * ]);
     * 
     * @param array $filters Optional date range filters
     * @return array Statistics data
     */
    public function getLoginStatisticsNew(array $filters = []): array
    {
        // Base query
        $query = LoginLog::query();

        // Apply date range if provided
        if (!empty($filters['date_from'])) {
            $query->whereDate('create_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('create_at', '<=', $filters['date_to']);
        }

        // Total attempts (clone query untuk reuse)
        $totalAttempts = (clone $query)->count();

        // Successful logins
        $successfulLogins = (clone $query)->where('status_akses', 'BERHASIL')->count();

        // Failed logins
        $failedLogins = (clone $query)->where('status_akses', '!=', 'BERHASIL')->count();

        // Unique users (yang pernah login berhasil)
        $uniqueUsers = (clone $query)
            ->where('status_akses', 'BERHASIL')
            ->distinct('pengguna_uuid')
            ->count('pengguna_uuid');

        // Today's activity
        $todayAttempts = LoginLog::whereDate('create_at', today())->count();
        $todaySuccessful = LoginLog::whereDate('create_at', today())
            ->where('status_akses', 'BERHASIL')
            ->count();

        // This week's activity
        $weekAttempts = LoginLog::whereBetween('create_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();

        // Failed login breakdown by status
        $failedBreakdown = LoginLog::query()
            ->select('status_akses', DB::raw('COUNT(*) as total'))
            ->where('status_akses', '!=', 'BERHASIL')
            ->when(!empty($filters['date_from']), function ($q) use ($filters) {
                $q->whereDate('create_at', '>=', $filters['date_from']);
            })
            ->when(!empty($filters['date_to']), function ($q) use ($filters) {
                $q->whereDate('create_at', '<=', $filters['date_to']);
            })
            ->groupBy('status_akses')
            ->orderByDesc('total')
            ->get();

        // Top 5 most active IPs (potential brute force)
        $topIPs = LoginLog::query()
            ->select('alamat_ip', DB::raw('COUNT(*) as total'))
            ->whereNotNull('alamat_ip')
            ->when(!empty($filters['date_from']), function ($q) use ($filters) {
                $q->whereDate('create_at', '>=', $filters['date_from']);
            })
            ->when(!empty($filters['date_to']), function ($q) use ($filters) {
                $q->whereDate('create_at', '<=', $filters['date_to']);
            })
            ->groupBy('alamat_ip')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Total registered users (only Pengguna role)
        $totalUsers = User::whereHas('peran', function ($query) {
            $query->where('nm_peran', 'Pengguna');
        })->count();

        return [
            'total_users'         => $totalUsers,
            'total_attempts'      => $totalAttempts,
            'successful_logins'   => $successfulLogins,
            'failed_logins'       => $failedLogins,
            'unique_users'        => $uniqueUsers,
            'success_rate'        => $totalAttempts > 0 
                ? round(($successfulLogins / $totalAttempts) * 100, 2) 
                : 0,
            'today_attempts'      => $todayAttempts,
            'today_successful'    => $todaySuccessful,
            'week_attempts'       => $weekAttempts,
            'failed_breakdown'    => $failedBreakdown,
            'top_ips'             => $topIPs,
        ];
    }

    // ==========================================
    // LEGACY METHOD - OLD VERSION (DEPRECATED)
    // ==========================================
    
    /**
     * @deprecated Use getLoginHistory() instead
     * 
     * OLD VERSION: Query dari User.last_login_at (hanya menyimpan last login saja)
     * NEW VERSION: Query dari audit.riwayat_login (full login history)
     * 
     * Method ini tetap ada untuk backward compatibility,
     * tapi sebaiknya gunakan getLoginHistory() untuk fitur baru.
     * 
     * @param array $filters Filter parameters (search, date_from, date_to, status)
     * @param int $perPage Pagination size
     * @return LengthAwarePaginator
     */
    public function getLoginLogs(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = User::query()
            ->with(['peran:UUID,nm_peran'])
            ->whereHas('peran', function ($q) {
                // Hanya tampilkan user dengan role Pengguna
                $q->where('nm_peran', 'Pengguna');
            })
            ->select([
                'UUID',
                'nm',
                'email',
                'usn',
                'peran_uuid',
                'last_login_at',
                'last_login_ip',
                'create_at',
                'a_aktif'
            ]);

        // Filter by search (nama, email, username)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nm', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('usn', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by status aktif
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('a_aktif', $filters['status'] === 'aktif');
        }

        // Filter by date range (last_login_at)
        if (!empty($filters['date_from'])) {
            $query->whereDate('last_login_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('last_login_at', '<=', $filters['date_to']);
        }

        // Filter users yang pernah login atau belum
        if (!empty($filters['has_login'])) {
            if ($filters['has_login'] === 'yes') {
                $query->whereNotNull('last_login_at');
            } elseif ($filters['has_login'] === 'no') {
                $query->whereNull('last_login_at');
            }
        } else {
            // Default: hanya tampilkan yang sudah pernah login
            $query->whereNotNull('last_login_at');
        }

        return $query->orderBy('last_login_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get submission status change logs
     * 
     * @param array $filters Filter parameters (search, date_from, date_to, service_type, status)
     * @param int $perPage Pagination size
     * @return LengthAwarePaginator
     */
    public function getSubmissionLogs(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = SubmissionLog::query()
            ->with([
                'pengajuan' => function ($q) {
                    $q->select('UUID', 'no_tiket', 'pengguna_uuid', 'jenis_layanan_uuid', 'create_at')
                      ->with([
                          'pengguna' => function ($pq) {
                              $pq->select('UUID', 'nm', 'email', 'usn', 'peran_uuid')
                                 ->with('peran:UUID,nm_peran');
                          },
                          'jenisLayanan:UUID,nm_layanan'
                      ]);
                },
                'statusLama:UUID,nm_status',
                'statusBaru:UUID,nm_status',
                'creator' => function ($cq) {
                    $cq->select('UUID', 'nm', 'email', 'peran_uuid')
                       ->with('peran:UUID,nm_peran');
                }
            ])
            ->select([
                'UUID',
                'pengajuan_uuid',
                'status_lama_uuid',
                'status_baru_uuid',
                'catatan_log',
                'create_at',
                'id_creator'
            ]);

        // Filter by search (no_tiket, nama pemohon, email)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('pengajuan', function ($q) use ($search) {
                $q->where('no_tiket', 'ILIKE', "%{$search}%")
                  ->orWhereHas('pengguna', function ($userQ) use ($search) {
                      $userQ->where('nm', 'ILIKE', "%{$search}%")
                            ->orWhere('email', 'ILIKE', "%{$search}%");
                  });
            });
        }

        // Filter by service type (domain, hosting, vps)
        if (!empty($filters['service_type'])) {
            $query->whereHas('pengajuan.jenisLayanan', function ($q) use ($filters) {
                $q->where('nm_layanan', $filters['service_type']);
            });
        }

        // Filter by new status
        if (!empty($filters['status'])) {
            $query->whereHas('statusBaru', function ($q) use ($filters) {
                $q->where('nm_status', 'ILIKE', "%{$filters['status']}%");
            });
        }

        // Filter by date range (create_at of log)
        if (!empty($filters['date_from'])) {
            $query->whereDate('create_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('create_at', '<=', $filters['date_to']);
        }

        // Filter by specific user (pemohon)
        if (!empty($filters['user_uuid'])) {
            $query->whereHas('pengajuan', function ($q) use ($filters) {
                $q->where('pengguna_uuid', $filters['user_uuid']);
            });
        }

        return $query->orderBy('create_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get ALL submission logs without pagination (for merging purposes)
     * 
     * Digunakan ketika ingin menggabungkan dengan login logs.
     * Tidak menggunakan pagination agar tidak conflict dengan manual pagination di controller.
     * 
     * @param array $filters Filter parameters (sama seperti getSubmissionLogs)
     * @return \Illuminate\Support\Collection
     */
    public function getSubmissionLogsCollection(array $filters = []): \Illuminate\Support\Collection
    {
        $query = SubmissionLog::query()
            ->with([
                'pengajuan' => function ($q) {
                    $q->select('UUID', 'no_tiket', 'pengguna_uuid', 'jenis_layanan_uuid', 'create_at')
                      ->with([
                          'pengguna' => function ($pq) {
                              $pq->select('UUID', 'nm', 'email', 'usn', 'peran_uuid')
                                 ->with('peran:UUID,nm_peran');
                          },
                          'jenisLayanan:UUID,nm_layanan'
                      ]);
                },
                'statusLama:UUID,nm_status',
                'statusBaru:UUID,nm_status',
                'creator' => function ($cq) {
                    $cq->select('UUID', 'nm', 'email', 'peran_uuid')
                       ->with('peran:UUID,nm_peran');
                }
            ])
            ->select([
                'UUID',
                'pengajuan_uuid',
                'status_lama_uuid',
                'status_baru_uuid',
                'catatan_log',
                'create_at',
                'id_creator'
            ]);

        // Apply same filters as getSubmissionLogs
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('pengajuan', function ($q) use ($search) {
                $q->where('no_tiket', 'ILIKE', "%{$search}%")
                  ->orWhereHas('pengguna', function ($userQ) use ($search) {
                      $userQ->where('nm', 'ILIKE', "%{$search}%")
                            ->orWhere('email', 'ILIKE', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['service_type'])) {
            $query->whereHas('pengajuan.jenisLayanan', function ($q) use ($filters) {
                $q->where('nm_layanan', $filters['service_type']);
            });
        }

        if (!empty($filters['status'])) {
            $query->whereHas('statusBaru', function ($q) use ($filters) {
                $q->where('nm_status', 'ILIKE', "%{$filters['status']}%");
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('create_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('create_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['user_uuid'])) {
            $query->whereHas('pengajuan', function ($q) use ($filters) {
                $q->where('pengguna_uuid', $filters['user_uuid']);
            });
        }

        return $query->orderBy('create_at', 'desc')->get();
    }

    /**
     * Get combined activity timeline untuk specific user - UPDATED VERSION
     * 
     * Menggabungkan FULL login history dan submission logs dalam satu timeline
     * 
     * VERSION 2.0: Menggunakan audit.riwayat_login untuk full login history
     * (Bukan hanya last_login_at dari user table)
     * 
     * @param string $userUuid User UUID
     * @param int $perPage Items per page
     * @return array
     */
    public function getUserActivityTimeline(string $userUuid, int $perPage = 20): array
    {
        $user = User::with(['peran:UUID,nm_peran'])
            ->where('UUID', $userUuid)
            ->firstOrFail();

        // ==========================================
        // Get FULL login history dari audit.riwayat_login
        // ==========================================
        $loginActivity = LoginLog::where('pengguna_uuid', $userUuid)
            ->select(['UUID', 'pengguna_uuid', 'alamat_ip', 'status_akses', 'keterangan', 'create_at'])
            ->get()
            ->map(function ($log) use ($user) {
                return [
                    'type' => 'login',
                    'timestamp' => $log->create_at,
                    'ip_address' => $log->alamat_ip,
                    'data' => [
                        'user_name' => $user->nm,
                        'user_email' => $user->email,
                        'status' => $log->status_akses,
                        'status_label' => $log->getStatusLabel(),
                        'notes' => $log->keterangan,
                        'is_successful' => $log->isSuccessful(),
                    ]
                ];
            });

        // ==========================================
        // Get submission logs (unchanged)
        // ==========================================
        $submissionLogs = SubmissionLog::with([
                'pengajuan' => function ($q) {
                    $q->select('UUID', 'no_tiket', 'jenis_layanan_uuid')
                      ->with('jenisLayanan:UUID,nm_layanan');
                },
                'statusLama:UUID,nm_status',
                'statusBaru:UUID,nm_status',
            ])
            ->whereHas('pengajuan', function ($q) use ($userUuid) {
                $q->where('pengguna_uuid', $userUuid);
            })
            ->select(['UUID', 'pengajuan_uuid', 'status_lama_uuid', 'status_baru_uuid', 'catatan_log', 'create_at'])
            ->get()
            ->map(function ($log) {
                return [
                    'type' => 'submission_status',
                    'timestamp' => $log->create_at,
                    'data' => [
                        'ticket_number' => $log->pengajuan->no_tiket ?? '-',
                        'service_type' => $log->pengajuan->jenisLayanan->nm_layanan ?? '-',
                        'status_old' => $log->statusLama->nm_status ?? '-',
                        'status_new' => $log->statusBaru->nm_status ?? '-',
                        'notes' => $log->catatan_log,
                    ]
                ];
            });

        // ==========================================
        // Merge & Sort Timeline
        // ==========================================
        $timeline = $loginActivity->merge($submissionLogs)
            ->sortByDesc('timestamp')
            ->values();

        // Manual pagination
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $items = $timeline->slice($offset, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $items,
            $timeline->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return [
            'user' => $user,
            'timeline' => $paginator
        ];
    }

    /**
     * Get statistics untuk login logs
     * 
     * @return array
     */
    public function getLoginStatistics(): array
    {
        $totalUsers = User::whereHas('peran', function ($q) {
            $q->where('nm_peran', 'Pengguna');
        })->count();

        $usersWithLogin = User::whereHas('peran', function ($q) {
            $q->where('nm_peran', 'Pengguna');
        })->whereNotNull('last_login_at')->count();

        $activeToday = User::whereHas('peran', function ($q) {
            $q->where('nm_peran', 'Pengguna');
        })
        ->whereDate('last_login_at', today())
        ->count();

        $activeThisWeek = User::whereHas('peran', function ($q) {
            $q->where('nm_peran', 'Pengguna');
        })
        ->where('last_login_at', '>=', now()->startOfWeek())
        ->count();

        return [
            'total_users' => $totalUsers,
            'users_with_login' => $usersWithLogin,
            'active_today' => $activeToday,
            'active_this_week' => $activeThisWeek,
        ];
    }

    /**
     * Get statistics untuk submission logs
     * 
     * @return array
     */
    public function getSubmissionStatistics(): array
    {
        $totalLogs = SubmissionLog::count();

        $logsToday = SubmissionLog::whereDate('create_at', today())->count();

        $logsThisWeek = SubmissionLog::where('create_at', '>=', now()->startOfWeek())->count();

        // Most active status changes
        $mostActiveStatuses = DB::table('audit.riwayat_pengajuan as rp')
            ->join('referensi.status_pengajuan as sp', 'rp.status_baru_uuid', '=', 'sp.UUID')
            ->select('sp.nm_status', DB::raw('COUNT(*) as total'))
            ->groupBy('sp.nm_status')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'total_logs' => $totalLogs,
            'logs_today' => $logsToday,
            'logs_this_week' => $logsThisWeek,
            'most_active_statuses' => $mostActiveStatuses,
        ];
    }

    /**
     * Get users with their last activity info for user list view
     * 
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUsersWithLastActivity(int $page = 1, int $perPage = 20)
    {
        // Get all users with their last login info (only Pengguna role)
        $users = User::query()
            ->select([
                'akun.pengguna.UUID',
                'akun.pengguna.nm',
                'akun.pengguna.email',
                'akun.pengguna.usn',
                'akun.pengguna.peran_uuid',
                'akun.pengguna.a_aktif',
                'akun.pengguna.create_at',
                'akun.pengguna.sso_id',
            ])
            ->with('peran:UUID,nm_peran')
            ->whereHas('peran', function ($query) {
                $query->where('nm_peran', 'Pengguna');
            })
            ->withCount('submissions as total_submissions')
            ->orderByDesc('create_at')
            ->paginate($perPage); // Laravel auto-detects current page from request

        // Get last login info for each user
        $userUuids = $users->pluck('UUID');
        
        $lastLogins = LoginLog::query()
            ->select('pengguna_uuid', 'alamat_ip', 'create_at')
            ->whereIn('pengguna_uuid', $userUuids)
            ->where('status_akses', 'BERHASIL')
            ->orderByDesc('create_at')
            ->get()
            ->groupBy('pengguna_uuid')
            ->map(function ($logs) {
                return $logs->first(); // Get the latest login
            });

        // Attach last login info to users
        foreach ($users as $user) {
            $user->last_login_ip = $lastLogins[$user->UUID]->alamat_ip ?? null;
            $user->last_login_at = $lastLogins[$user->UUID]->create_at ?? null;
        }

        return $users;
    }
}
