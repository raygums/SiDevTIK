<?php

namespace App\Services;

use App\Models\User;
use App\Models\SubmissionLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * AdminService - Service khusus untuk logika Admin
 * 
 * Responsibilities:
 * - Verifikasi akun pengguna (toggle a_aktif)
 * - Audit logs pengguna (waktu daftar, pengajuan pertama, update terakhir)
 * - Statistik user management
 * 
 * Design Pattern: Service Pattern
 * Clean Code: Efektivitas = Keterbacaan + Kesederhanaan + Konsistensi + Reusabilitas
 */
class AdminService
{
    /**
     * Get filtered users untuk verifikasi akun
     * 
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUsersForVerification(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query()->with('peran');

        // IMPORTANT: Admin hanya bisa mengelola user dengan role "Pengguna"
        // Role lain (Admin, Verifikator, Eksekutor, Pimpinan) hanya bisa dikelola oleh Pimpinan
        $query->whereHas('peran', function($q) {
            $q->where('nm_peran', 'Pengguna');
        });

        // Filter: Tipe Akun (SSO vs Lokal)
        if (isset($filters['tipe_akun']) && $filters['tipe_akun'] !== 'all') {
            if ($filters['tipe_akun'] === 'sso') {
                $query->whereNotNull('sso_id');
            } else {
                $query->whereNull('sso_id');
            }
        }

        // Filter: Status Aktivasi
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('a_aktif', $filters['status'] === 'aktif');
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
     * Toggle status aktif user
     * 
     * @param string $userUuid
     * @return bool
     */
    public function toggleUserStatus(string $userUuid): bool
    {
        try {
            DB::beginTransaction();

            $user = User::where('UUID', $userUuid)->firstOrFail();
            
            $oldStatus = $user->a_aktif;
            $newStatus = !$oldStatus;
            
            $user->update(['a_aktif' => $newStatus]);

            Log::info('Admin Toggle User Status', [
                'admin_uuid' => auth()->user()->UUID,
                'admin_name' => auth()->user()->nm,
                'user_uuid' => $userUuid,
                'user_name' => $user->nm,
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
     * Bulk activate users
     * 
     * @param array $userUuids
     * @return int Number of users activated
     */
    public function bulkActivateUsers(array $userUuids): int
    {
        try {
            DB::beginTransaction();

            $count = User::whereIn('UUID', $userUuids)
                ->where('a_aktif', false)
                ->update(['a_aktif' => true]);

            Log::info('Admin Bulk Activate Users', [
                'admin_uuid' => auth()->user()->UUID,
                'admin_name' => auth()->user()->nm,
                'user_uuids' => $userUuids,
                'count' => $count,
            ]);

            DB::commit();
            return $count;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to bulk activate users', [
                'user_uuids' => $userUuids,
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Get user audit logs (waktu daftar, pengajuan pertama, update terakhir)
     * 
     * @param string $userUuid
     * @return array
     */
    public function getUserAuditLogs(string $userUuid): array
    {
        $user = User::where('UUID', $userUuid)
            ->with(['peran', 'submissions' => function($q) {
                $q->orderBy('tgl_pengajuan', 'asc')->limit(1);
            }])
            ->firstOrFail();

        // Waktu daftar akun
        $waktuDaftar = $user->create_at;

        // Waktu pengajuan pertama
        $pengajuanPertama = $user->submissions->first()?->tgl_pengajuan;

        // Waktu update terakhir
        $updateTerakhir = $user->last_update;

        // Last login
        $lastLogin = $user->last_login_at;

        return [
            'user' => $user,
            'waktu_daftar' => $waktuDaftar,
            'pengajuan_pertama' => $pengajuanPertama,
            'update_terakhir' => $updateTerakhir,
            'last_login' => $lastLogin,
            'total_pengajuan' => $user->submissions()->count(),
            'pengajuan_aktif' => $user->submissions()
                ->whereHas('status', fn($q) => $q->whereNotIn('nm_status', ['Selesai', 'Ditolak Verifikator', 'Ditolak Eksekutor']))
                ->count(),
        ];
    }

    /**
     * Get user statistics untuk admin dashboard
     * 
     * @return array
     */
    public function getUserStatistics(): array
    {
        // Admin hanya lihat statistik user dengan role "Pengguna"
        $penggunaQuery = User::whereHas('peran', function($q) {
            $q->where('nm_peran', 'Pengguna');
        });

        return [
            'total' => (clone $penggunaQuery)->count(),
            'aktif' => (clone $penggunaQuery)->where('a_aktif', true)->count(),
            'nonaktif' => (clone $penggunaQuery)->where('a_aktif', false)->count(),
            'sso' => (clone $penggunaQuery)->whereNotNull('sso_id')->count(),
            'lokal' => (clone $penggunaQuery)->whereNull('sso_id')->count(),
            'mahasiswa' => (clone $penggunaQuery)->whereNotNull('id_pd')->count(),
            'dosen_tendik' => (clone $penggunaQuery)->whereNotNull('id_sdm')->count(),
            'pending_verification' => (clone $penggunaQuery)
                ->where('a_aktif', false)
                ->whereNotNull('sso_id')
                ->count(),
        ];
    }

    /**
     * Get recent user registrations (untuk admin oversight)
     * 
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentRegistrations(int $limit = 10)
    {
        return User::with('peran')
            ->whereHas('peran', function($q) {
                $q->where('nm_peran', 'Pengguna');
            })
            ->latest('create_at')
            ->take($limit)
            ->get();
    }

    /**
     * Get users yang belum pernah login (potential inactive accounts)
     * 
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUsersNeverLoggedIn(int $perPage = 15): LengthAwarePaginator
    {
        return User::with('peran')
            ->whereNull('last_login_at')
            ->orderBy('create_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Search users dengan advanced criteria
     * 
     * @param string $keyword
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchUsers(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        return User::with('peran')
            ->where(function (Builder $q) use ($keyword) {
                $q->where('nm', 'ILIKE', "%{$keyword}%")
                  ->orWhere('usn', 'ILIKE', "%{$keyword}%")
                  ->orWhere('email', 'ILIKE', "%{$keyword}%")
                  ->orWhere('sso_id', 'ILIKE', "%{$keyword}%");
            })
            ->orderBy('create_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }
}
