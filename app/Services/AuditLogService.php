<?php

namespace App\Services;

use App\Models\User;
use App\Models\SubmissionLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Service untuk mengelola Log Audit
 * - Login Activity
 * - Submission Status Changes
 */
class AuditLogService
{
    /**
     * Get login activity logs untuk semua user atau user tertentu
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
                          'pengguna:UUID,nm,email',
                          'jenisLayanan:UUID,nm_layanan'
                      ]);
                },
                'statusLama:UUID,nm_status',
                'statusBaru:UUID,nm_status',
                'creator:UUID,nm,email'
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
     * Get combined activity timeline untuk specific user
     * Menggabungkan login logs dan submission logs dalam satu timeline
     * 
     * @param string $userUuid
     * @param int $perPage
     * @return array
     */
    public function getUserActivityTimeline(string $userUuid, int $perPage = 20): array
    {
        $user = User::with(['peran:UUID,nm_peran'])
            ->where('UUID', $userUuid)
            ->firstOrFail();

        // Get login history (hanya last login yang available)
        $loginActivity = collect([]);
        if ($user->last_login_at) {
            $loginActivity->push([
                'type' => 'login',
                'timestamp' => $user->last_login_at,
                'ip_address' => $user->last_login_ip,
                'data' => [
                    'user_name' => $user->nm,
                    'user_email' => $user->email
                ]
            ]);
        }

        // Get submission logs
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

        // Merge dan sort by timestamp
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
}
