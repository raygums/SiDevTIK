<?php

namespace App\Services;

use App\Models\User;
use App\Models\Submission;
use App\Models\SubmissionLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PimpinanService - Service untuk fitur Pimpinan (Super Admin)
 * 
 * Responsibilities:
 * - Manajemen semua role pengguna (User, Verifikator, Eksekutor, Admin)
 * - View all system activity logs
 * - Dashboard statistics untuk monitoring
 * 
 * Design Pattern: Service Pattern
 */
class PimpinanService
{
    /**
     * Get dashboard statistics for Pimpinan
     */
    public function getDashboardStats(): array
    {
        return [
            'users' => $this->getUserStatistics(),
            'submissions' => $this->getSubmissionStatistics(),
            'activities' => $this->getRecentActivityStats(),
        ];
    }

    /**
     * Get user statistics by role
     */
    public function getUserStatistics(): array
    {
        $stats = User::query()
            ->select('akun.peran.nm_peran', DB::raw('COUNT(*) as total'))
            ->join('akun.peran', 'akun.pengguna.peran_uuid', '=', 'akun.peran.UUID')
            ->groupBy('akun.peran.nm_peran')
            ->pluck('total', 'nm_peran')
            ->toArray();

        $total = array_sum($stats);
        $aktif = User::where('a_aktif', true)->count();
        $nonaktif = User::where('a_aktif', false)->count();

        return [
            'total' => $total,
            'aktif' => $aktif,
            'nonaktif' => $nonaktif,
            'by_role' => [
                'pengguna' => $stats['Pengguna'] ?? 0,
                'verifikator' => $stats['Verifikator'] ?? 0,
                'eksekutor' => $stats['Eksekutor'] ?? 0,
                'admin' => $stats['Administrator'] ?? 0,
                'pimpinan' => $stats['Pimpinan'] ?? 0,
            ],
        ];
    }

    /**
     * Get submission statistics
     */
    public function getSubmissionStatistics(): array
    {
        $total = Submission::count();
        $bulanIni = Submission::whereMonth('create_at', now()->month)
            ->whereYear('create_at', now()->year)
            ->count();
        
        $statusCounts = Submission::query()
            ->select('status.nm_status', DB::raw('COUNT(*) as total'))
            ->join('referensi.status_pengajuan as status', 'transaksi.pengajuan.status_uuid', '=', 'status.UUID')
            ->groupBy('status.nm_status')
            ->pluck('total', 'nm_status')
            ->toArray();

        return [
            'total' => $total,
            'bulan_ini' => $bulanIni,
            'diajukan' => $statusCounts['Diajukan'] ?? 0,
            'diverifikasi' => $statusCounts['Disetujui Verifikator'] ?? 0,
            'dikerjakan' => $statusCounts['Sedang Dikerjakan'] ?? 0,
            'selesai' => $statusCounts['Selesai'] ?? 0,
            'ditolak' => ($statusCounts['Ditolak Verifikator'] ?? 0) + ($statusCounts['Ditolak Eksekutor'] ?? 0),
        ];
    }

    /**
     * Get recent activity statistics (last 7 days)
     */
    public function getRecentActivityStats(): array
    {
        $sevenDaysAgo = now()->subDays(7);

        // Get activity counts by day for chart
        $dailyActivity = SubmissionLog::where('create_at', '>=', $sevenDaysAgo)
            ->select(DB::raw("DATE(create_at) as date"), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Get today's activities
        $todayTotal = SubmissionLog::whereDate('create_at', today())->count();

        // Get activity by actor role
        $byActorRole = SubmissionLog::query()
            ->where('audit.riwayat_pengajuan.create_at', '>=', $sevenDaysAgo)
            ->join('akun.pengguna', 'audit.riwayat_pengajuan.id_creator', '=', 'pengguna.UUID')
            ->join('akun.peran', 'pengguna.peran_uuid', '=', 'peran.UUID')
            ->select('peran.nm_peran', DB::raw('COUNT(*) as total'))
            ->groupBy('peran.nm_peran')
            ->pluck('total', 'nm_peran')
            ->toArray();

        return [
            'total_7_days' => array_sum($dailyActivity),
            'today' => $todayTotal,
            'daily' => $dailyActivity,
            'by_role' => $byActorRole,
        ];
    }

    /**
     * Get all users with filters for management
     * Pimpinan can manage ALL roles
     */
    public function getAllUsers(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = User::query()->with('peran');

        // Filter: Role
        if (!empty($filters['role']) && $filters['role'] !== 'all') {
            $query->whereHas('peran', function($q) use ($filters) {
                $q->where('nm_peran', 'ILIKE', $filters['role']);
            });
        }

        // Filter: Status Aktivasi
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('a_aktif', $filters['status'] === 'aktif');
        }

        // Filter: Tipe Akun (SSO vs Lokal)
        if (!empty($filters['tipe_akun']) && $filters['tipe_akun'] !== 'all') {
            if ($filters['tipe_akun'] === 'sso') {
                $query->whereNotNull('sso_id');
            } else {
                $query->whereNull('sso_id');
            }
        }

        // Search: Nama, Username, Email
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('nm', 'ILIKE', "%{$search}%")
                  ->orWhere('usn', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'create_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Toggle user status (aktif/nonaktif)
     */
    public function toggleUserStatus(string $userUuid): bool
    {
        try {
            DB::beginTransaction();

            $user = User::where('UUID', $userUuid)->firstOrFail();
            
            // Prevent self-deactivation
            if ($user->UUID === auth()->user()->UUID) {
                throw new \Exception('Tidak dapat menonaktifkan akun sendiri');
            }

            $oldStatus = $user->a_aktif;
            $newStatus = !$oldStatus;
            
            $user->update(['a_aktif' => $newStatus]);

            Log::info('Pimpinan Toggle User Status', [
                'pimpinan_uuid' => auth()->user()->UUID,
                'pimpinan_name' => auth()->user()->nm,
                'user_uuid' => $userUuid,
                'user_name' => $user->nm,
                'user_role' => $user->peran->nm_peran ?? '-',
                'old_status' => $oldStatus ? 'Active' : 'Inactive',
                'new_status' => $newStatus ? 'Active' : 'Inactive',
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to toggle user status', [
                'user_uuid' => $userUuid,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Change user role
     */
    public function changeUserRole(string $userUuid, string $newRoleUuid): bool
    {
        try {
            DB::beginTransaction();

            $user = User::where('UUID', $userUuid)->firstOrFail();
            
            // Prevent changing own role
            if ($user->UUID === auth()->user()->UUID) {
                throw new \Exception('Tidak dapat mengubah role sendiri');
            }

            $oldRole = $user->peran->nm_peran ?? '-';
            
            $user->update(['peran_uuid' => $newRoleUuid]);
            $user->refresh();

            Log::info('Pimpinan Change User Role', [
                'pimpinan_uuid' => auth()->user()->UUID,
                'pimpinan_name' => auth()->user()->nm,
                'user_uuid' => $userUuid,
                'user_name' => $user->nm,
                'old_role' => $oldRole,
                'new_role' => $user->peran->nm_peran ?? '-',
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to change user role', [
                'user_uuid' => $userUuid,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get all activity logs (submission logs) with filters
     */
    public function getActivityLogs(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = SubmissionLog::query()
            ->with([
                'pengajuan.rincian', 
                'pengajuan.jenisLayanan',
                'pengajuan.pengguna',
                'statusLama', 
                'statusBaru', 
                'creator.peran'
            ]);

        // Filter: Actor Role (who performed the action)
        if (!empty($filters['actor_role']) && $filters['actor_role'] !== 'all') {
            $query->whereHas('creator.peran', function($q) use ($filters) {
                $q->where('nm_peran', 'ILIKE', $filters['actor_role']);
            });
        }

        // Filter: Action Type (based on status change)
        if (!empty($filters['action_type']) && $filters['action_type'] !== 'all') {
            $query->whereHas('statusBaru', function($q) use ($filters) {
                if ($filters['action_type'] === 'approved') {
                    $q->whereIn('nm_status', ['Disetujui Verifikator', 'Sedang Dikerjakan', 'Selesai']);
                } elseif ($filters['action_type'] === 'rejected') {
                    $q->whereIn('nm_status', ['Ditolak Verifikator', 'Ditolak Eksekutor']);
                } elseif ($filters['action_type'] === 'submitted') {
                    $q->where('nm_status', 'Diajukan');
                }
            });
        }

        // Filter: Service Type
        if (!empty($filters['service_type']) && $filters['service_type'] !== 'all') {
            $query->whereHas('pengajuan.jenisLayanan', function($q) use ($filters) {
                $q->where('nm_layanan', 'ILIKE', $filters['service_type']);
            });
        }

        // Filter: Date Range
        if (!empty($filters['date_from'])) {
            $query->whereDate('create_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('create_at', '<=', $filters['date_to']);
        }

        // Search: Ticket number, user name, domain
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('pengajuan', function($sq) use ($search) {
                    $sq->where('no_tiket', 'ILIKE', "%{$search}%");
                })
                ->orWhereHas('pengajuan.rincian', function($sq) use ($search) {
                    $sq->where('nm_domain', 'ILIKE', "%{$search}%");
                })
                ->orWhereHas('creator', function($sq) use ($search) {
                    $sq->where('nm', 'ILIKE', "%{$search}%");
                })
                ->orWhereHas('pengajuan.pengguna', function($sq) use ($search) {
                    $sq->where('nm', 'ILIKE', "%{$search}%");
                });
            });
        }

        // Order by most recent
        $query->orderBy('create_at', 'desc');

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get recent activity logs for dashboard
     */
    public function getRecentActivityLogs(int $limit = 10): Collection
    {
        return SubmissionLog::query()
            ->with([
                'pengajuan.rincian', 
                'pengajuan.jenisLayanan',
                'pengajuan.pengguna',
                'statusLama', 
                'statusBaru', 
                'creator.peran'
            ])
            ->orderBy('create_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activity detail by log UUID
     */
    public function getActivityDetail(string $logUuid): ?SubmissionLog
    {
        return SubmissionLog::query()
            ->with([
                'pengajuan.rincian', 
                'pengajuan.jenisLayanan',
                'pengajuan.pengguna',
                'pengajuan.unitKerja.category',
                'statusLama', 
                'statusBaru', 
                'creator.peran'
            ])
            ->where('UUID', $logUuid)
            ->first();
    }

    /**
     * Get user detail with their activity history
     */
    public function getUserDetail(string $userUuid): array
    {
        $user = User::with('peran')->where('UUID', $userUuid)->firstOrFail();

        // Get user's submissions
        $submissions = Submission::with(['jenisLayanan', 'status', 'rincian'])
            ->where('id_creator', $userUuid)
            ->orderBy('create_at', 'desc')
            ->limit(10)
            ->get();

        // Get user's actions (as creator of logs)
        $actions = SubmissionLog::with(['pengajuan.rincian', 'statusLama', 'statusBaru'])
            ->where('id_creator', $userUuid)
            ->orderBy('create_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'user' => $user,
            'submissions' => $submissions,
            'actions' => $actions,
        ];
    }

    /**
     * Get all available roles for dropdown
     */
    public function getAllRoles(): Collection
    {
        return \App\Models\Peran::orderBy('nm_peran')->get();
    }

    /**
     * Bulk activate users
     */
    public function bulkActivateUsers(array $userUuids): int
    {
        try {
            DB::beginTransaction();

            // Exclude self from bulk operation
            $userUuids = array_filter($userUuids, fn($uuid) => $uuid !== auth()->user()->UUID);

            $count = User::whereIn('UUID', $userUuids)
                ->where('a_aktif', false)
                ->update(['a_aktif' => true]);

            Log::info('Pimpinan Bulk Activate Users', [
                'pimpinan_uuid' => auth()->user()->UUID,
                'pimpinan_name' => auth()->user()->nm,
                'user_count' => $count,
                'user_uuids' => $userUuids,
            ]);

            DB::commit();
            return $count;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to bulk activate users', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Bulk deactivate users
     */
    public function bulkDeactivateUsers(array $userUuids): int
    {
        try {
            DB::beginTransaction();

            // Exclude self from bulk operation
            $userUuids = array_filter($userUuids, fn($uuid) => $uuid !== auth()->user()->UUID);

            $count = User::whereIn('UUID', $userUuids)
                ->where('a_aktif', true)
                ->update(['a_aktif' => false]);

            Log::info('Pimpinan Bulk Deactivate Users', [
                'pimpinan_uuid' => auth()->user()->UUID,
                'pimpinan_name' => auth()->user()->nm,
                'user_count' => $count,
                'user_uuids' => $userUuids,
            ]);

            DB::commit();
            return $count;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to bulk deactivate users', ['error' => $e->getMessage()]);
            return 0;
        }
    }
}
