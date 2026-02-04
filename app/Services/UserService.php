<?php

namespace App\Services;

use App\Models\User;
use App\Models\Peran;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * UserService - Centralized business logic untuk manajemen user
 * 
 * Service Pattern Implementation:
 * - Memisahkan business logic dari controller (Thin Controller, Fat Service)
 * - Reusable: Dapat dipanggil dari berbagai controller
 * - Testable: Mudah di-unit test
 * - Maintainable: Single source of truth untuk user operations
 * 
 * Design Philosophy:
 * Efektivitas = Keterbacaan + Kesederhanaan + Konsistensi + Reusabilitas
 */
class UserService
{
    /**
     * Register user baru (Registrasi Mandiri).
     * 
     * Business Logic:
     * - User baru default role 'Pengguna' (bukan admin)
     * - Status awal a_aktif = false (menunggu verifikasi)
     * - id_creator diisi dengan UUID user itu sendiri (self-reference)
     * - Password di-hash menggunakan bcrypt
     * - Upload file KTP/KTM disimpan di storage/app/public/verifikasi
     * 
     * Security:
     * - Semua data sudah divalidasi di RegisterRequest
     * - Transaction untuk memastikan atomicity
     * - Audit log untuk tracking
     * 
     * @param  array  $data
     * @return array{success: bool, user: User|null, message: string}
     */
    public function register(array $data): array
    {
        try {
            DB::beginTransaction();

            // 1. Dapatkan UUID role 'Pengguna' dari tabel akun.peran
            $peranPengguna = Peran::where('nm_peran', 'Pengguna')->first();

            if (!$peranPengguna) {
                DB::rollBack();
                return [
                    'success' => false,
                    'user' => null,
                    'message' => 'Role Pengguna tidak ditemukan dalam sistem.',
                ];
            }

            // 2. Generate UUID untuk user baru
            $userUuid = (string) Str::uuid();

            // 3. Handle file upload KTP/KTM (jika ada)
            $fileKtpKtmPath = null;
            if (!empty($data['file_ktp_ktm'])) {
                $file = $data['file_ktp_ktm'];
                $filename = $userUuid . '_' . time() . '.' . $file->getClientOriginalExtension();
                $fileKtpKtmPath = $file->storeAs('verifikasi', $filename, 'public');
            }

            // 4. Prepare data untuk insert
            $userData = [
                'UUID' => $userUuid,
                'nm' => $data['nm'],
                'usn' => $data['usn'],
                'email' => $data['email'],
                'kata_sandi' => Hash::make($data['kata_sandi']),
                'peran_uuid' => $peranPengguna->UUID,
                'ktp' => $data['nomor_identitas'] ?? null, // Store nomor identitas di field ktp
                'tgl_lahir' => $data['tgl_lahir'] ?? null,
                'a_aktif' => false, // User baru wajib non-aktif (menunggu verifikasi)
                'create_at' => now(),
                'last_update' => now(),
                'id_creator' => $userUuid, // Self-reference untuk registrasi mandiri
                'id_updater' => $userUuid,
            ];

            // 5. Simpan custom attribute untuk file path (jika ada)
            if ($fileKtpKtmPath) {
                $userData['file_ktp_ktm_path'] = $fileKtpKtmPath;
            }

            // 6. Create user
            $user = User::create($userData);

            // 7. Audit log
            Log::info('User Registered (Self-Registration)', [
                'user_uuid' => $user->UUID,
                'username' => $user->usn,
                'email' => $user->email,
                'peran' => $data['peran'] ?? 'Tidak disebutkan',
                'role' => 'Pengguna',
                'status' => 'Menunggu Verifikasi',
                'has_file' => !empty($fileKtpKtmPath),
            ]);

            DB::commit();

            return [
                'success' => true,
                'user' => $user,
                'message' => 'Registrasi berhasil! Akun Anda menunggu verifikasi dari admin.',
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus file yang sudah diupload jika ada error
            if (!empty($fileKtpKtmPath)) {
                Storage::disk('public')->delete($fileKtpKtmPath);
            }

            Log::error('User Registration Failed', [
                'data' => [
                    'username' => $data['usn'] ?? 'unknown',
                    'email' => $data['email'] ?? 'unknown',
                ],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'user' => null,
                'message' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.',
            ];
        }
    }

    /**
     * Get filtered and paginated user list with advanced filters.
     * 
     * Filters yang didukung:
     * - tipe_akun: 'sso' atau 'lokal'
     * - identity: 'mahasiswa', 'dosen_tendik', atau 'all'
     * - status: 'aktif', 'tidak_aktif', atau 'all'
     * - search: Search by nama, username, atau email
     * 
     * @param  array  $filters
     * @param  int    $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getFilteredUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query()->with('peran');

        // Filter: Tipe Akun (SSO vs Lokal)
        $this->applyAccountTypeFilter($query, $filters['tipe_akun'] ?? 'all');

        // Filter: Identity (Mahasiswa vs Dosen/Tendik)
        $this->applyIdentityFilter($query, $filters['identity'] ?? 'all');

        // Filter: Status Aktivasi
        $this->applyStatusFilter($query, $filters['status'] ?? 'all');

        // Filter: Search (Nama, Username, Email)
        if (!empty($filters['search'])) {
            $this->applySearchFilter($query, $filters['search']);
        }

        // Default ordering: User terbaru dulu
        $query->orderBy('create_at', 'desc');

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Apply filter berdasarkan tipe akun (SSO atau Lokal).
     * 
     * Logic:
     * - SSO: sso_id IS NOT NULL
     * - Lokal: sso_id IS NULL
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return void
     */
    protected function applyAccountTypeFilter(Builder $query, string $type): void
    {
        if ($type === 'sso') {
            $query->whereNotNull('sso_id');
        } elseif ($type === 'lokal') {
            $query->whereNull('sso_id');
        }
        // 'all' atau value lain: no filter applied
    }

    /**
     * Apply filter berdasarkan identity (Mahasiswa vs Dosen/Tendik).
     * 
     * Logic berdasarkan metadata SSO:
     * - Mahasiswa: id_pd (ID Perguruan Tinggi/Data Mahasiswa) terisi
     * - Dosen/Tendik: id_sdm (ID SDM/Pegawai) terisi
     * 
     * Note: Possible edge case dimana keduanya terisi atau keduanya kosong,
     * ini akan di-handle oleh OR condition.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $identity
     * @return void
     */
    protected function applyIdentityFilter(Builder $query, string $identity): void
    {
        if ($identity === 'mahasiswa') {
            $query->whereNotNull('id_pd');
        } elseif ($identity === 'dosen_tendik') {
            $query->whereNotNull('id_sdm');
        }
        // 'all' atau value lain: no filter applied
    }

    /**
     * Apply filter berdasarkan status aktivasi akun.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return void
     */
    protected function applyStatusFilter(Builder $query, string $status): void
    {
        if ($status === 'aktif') {
            $query->where('a_aktif', true);
        } elseif ($status === 'tidak_aktif') {
            $query->where('a_aktif', false);
        }
        // 'all' atau value lain: no filter applied
    }

    /**
     * Apply search filter untuk nama, username, atau email.
     * 
     * Menggunakan ILIKE untuk case-insensitive search (PostgreSQL).
     * Search di multiple kolom dengan OR condition.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $searchTerm
     * @return void
     */
    protected function applySearchFilter(Builder $query, string $searchTerm): void
    {
        $searchTerm = trim($searchTerm);
        
        if (empty($searchTerm)) {
            return;
        }

        $query->where(function ($q) use ($searchTerm) {
            $q->whereRaw('LOWER(nm) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
              ->orWhereRaw('LOWER(usn) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
              ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
        });
    }

    /**
     * Toggle status aktivasi user (aktif <-> tidak aktif).
     * 
     * Business Rules:
     * - Hanya Verifikator/Admin yang dapat toggle status (handled di controller/middleware)
     * - Log setiap perubahan untuk audit trail
     * - Update id_updater dan last_update
     * 
     * @param  string  $userUuid
     * @param  string  $updaterUuid
     * @return array{success: bool, user: User|null, message: string}
     */
    public function toggleUserStatus(string $userUuid, string $updaterUuid): array
    {
        try {
            DB::beginTransaction();

            $user = User::where('UUID', $userUuid)->first();

            if (!$user) {
                DB::rollBack();
                return [
                    'success' => false,
                    'user' => null,
                    'message' => 'User tidak ditemukan.',
                ];
            }

            // Toggle status
            $oldStatus = $user->a_aktif;
            $newStatus = !$oldStatus;

            $user->update([
                'a_aktif' => $newStatus,
                'last_update' => now(),
                'id_updater' => $updaterUuid,
            ]);

            // Audit log
            Log::info('User Status Toggled', [
                'user_uuid' => $user->UUID,
                'username' => $user->usn,
                'old_status' => $oldStatus ? 'aktif' : 'tidak_aktif',
                'new_status' => $newStatus ? 'aktif' : 'tidak_aktif',
                'updated_by' => $updaterUuid,
            ]);

            DB::commit();

            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

            return [
                'success' => true,
                'user' => $user->fresh(),
                'message' => "User {$user->nm} berhasil {$statusText}.",
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('User Status Toggle Failed', [
                'user_uuid' => $userUuid,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'user' => null,
                'message' => 'Terjadi kesalahan saat mengubah status user.',
            ];
        }
    }

    /**
     * Get user statistics untuk dashboard.
     * 
     * @return array{total: int, aktif: int, tidak_aktif: int, sso: int, lokal: int, mahasiswa: int, dosen_tendik: int}
     */
    public function getUserStatistics(): array
    {
        return [
            'total' => User::count(),
            'aktif' => User::where('a_aktif', true)->count(),
            'tidak_aktif' => User::where('a_aktif', false)->count(),
            'sso' => User::whereNotNull('sso_id')->count(),
            'lokal' => User::whereNull('sso_id')->count(),
            'mahasiswa' => User::whereNotNull('id_pd')->count(),
            'dosen_tendik' => User::whereNotNull('id_sdm')->count(),
        ];
    }

    /**
     * Bulk activate users by UUIDs.
     * 
     * Use case: Verifikator ingin activate multiple users sekaligus.
     * 
     * @param  array  $userUuids
     * @param  string $updaterUuid
     * @return array{success: bool, affected: int, message: string}
     */
    public function bulkActivateUsers(array $userUuids, string $updaterUuid): array
    {
        try {
            DB::beginTransaction();

            $affected = User::whereIn('UUID', $userUuids)
                ->where('a_aktif', false)
                ->update([
                    'a_aktif' => true,
                    'last_update' => now(),
                    'id_updater' => $updaterUuid,
                ]);

            Log::info('Bulk User Activation', [
                'affected_count' => $affected,
                'user_uuids' => $userUuids,
                'updated_by' => $updaterUuid,
            ]);

            DB::commit();

            return [
                'success' => true,
                'affected' => $affected,
                'message' => "{$affected} user berhasil diaktifkan.",
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Bulk User Activation Failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'affected' => 0,
                'message' => 'Terjadi kesalahan saat aktivasi bulk user.',
            ];
        }
    }

    /**
     * Get identity label dari user.
     * 
     * Helper method untuk display di UI.
     * 
     * @param  User  $user
     * @return string
     */
    public function getUserIdentityLabel(User $user): string
    {
        if ($user->id_pd) {
            return 'Mahasiswa';
        }

        if ($user->id_sdm) {
            return 'Dosen/Tendik';
        }

        return 'Umum';
    }

    /**
     * Get account type label dari user.
     * 
     * @param  User  $user
     * @return string
     */
    public function getAccountTypeLabel(User $user): string
    {
        return $user->sso_id ? 'Akun SSO' : 'Akun Lokal';
    }

}
