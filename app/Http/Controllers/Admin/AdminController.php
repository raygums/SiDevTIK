<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use App\Services\UnitSyncService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * AdminController - Thin Controller untuk fitur Admin
 * 
 * Responsibilities:
 * - Handle HTTP request/response
 * - Validate input
 * - Delegate business logic ke AdminService
 * - Return view atau redirect
 * 
 * Design Pattern: Thin Controller, Fat Service
 */
class AdminController extends Controller
{
    /**
     * AdminService instance
     */
    protected AdminService $adminService;
    protected UnitSyncService $unitSyncService;

    /**
     * Constructor - Dependency Injection
     */
    public function __construct(AdminService $adminService, UnitSyncService $unitSyncService)
    {
        $this->adminService = $adminService;
        $this->unitSyncService = $unitSyncService;
    }

    /**
     * Halaman verifikasi akun pengguna
     * 
     * Route: GET /admin/users/verification
     * Middleware: auth, role:admin
     */
    public function userVerification(Request $request): View
    {
        $filters = [
            'tipe_akun' => $request->get('tipe_akun', 'all'),
            'status' => $request->get('status', 'all'),
            'search' => $request->get('search'),
            'sort_by' => $request->get('sort_by', 'create_at'),
            'sort_dir' => $request->get('sort_dir', 'desc'),
        ];

        // Get per page from request (default: 20, allowed: 10, 20, 50, 100)
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;

        $users = $this->adminService->getUsersForVerification($filters, $perPage);
        $stats = $this->adminService->getUserStatistics();
        $roles = $this->adminService->getAssignableRoles();

        return view('admin.user-verification', compact('users', 'stats', 'filters', 'roles'));
    }

    /**
     * Toggle status aktif user
     * 
     * Route: POST /admin/users/{uuid}/toggle-status
     * Middleware: auth, role:admin
     */
    public function toggleUserStatus(string $uuid): RedirectResponse
    {
        $success = $this->adminService->toggleUserStatus($uuid);

        if ($success) {
            return redirect()->back()
                ->with('success', 'Status aktivasi user berhasil diubah.');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengubah status user. Silakan coba lagi.');
    }

    /**
     * Bulk activate users
     * 
     * Route: POST /admin/users/bulk-activate
     * Middleware: auth, role:admin
     */
    public function bulkActivate(Request $request): RedirectResponse
    {
        $request->validate([
            'user_uuids' => 'required|array|min:1',
            'user_uuids.*' => 'required|uuid',
        ], [
            'user_uuids.required' => 'Pilih minimal 1 user untuk diaktifkan.',
            'user_uuids.min' => 'Pilih minimal 1 user untuk diaktifkan.',
        ]);

        $count = $this->adminService->bulkActivateUsers($request->input('user_uuids'));

        if ($count > 0) {
            return redirect()->back()
                ->with('success', "{$count} user berhasil diaktifkan.");
        }

        return redirect()->back()
            ->with('error', 'Tidak ada user yang diaktifkan. Pastikan user dalam status non-aktif.');
    }

    /**
     * Halaman daftar user yang belum pernah login
     * 
     * Route: GET /admin/users/never-logged-in
     * Middleware: auth, role:admin
     */
    public function usersNeverLoggedIn(): View
    {
        $users = $this->adminService->getUsersNeverLoggedIn(15);

        return view('admin.users-never-logged-in', compact('users'));
    }

    /**
     * Change role user from admin panel.
     */
    public function changeUserRole(Request $request, string $uuid): RedirectResponse
    {
        $request->validate([
            'role_uuid' => ['required', Rule::exists(\App\Models\Peran::class, 'UUID')],
        ]);

        $result = $this->adminService->changeUserRole($uuid, $request->input('role_uuid'));

        return redirect()->back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Create local account by admin.
     */
    public function createUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nm' => 'required|string|max:125',
            'usn' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9._]+$/',
                Rule::unique(\App\Models\User::class, 'usn'),
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:125',
                Rule::unique(\App\Models\User::class, 'email'),
            ],
            'kata_sandi' => 'required|string|min:8|max:100',
            'peran_uuid' => ['required', Rule::exists(\App\Models\Peran::class, 'UUID')],
            'a_aktif' => 'nullable|boolean',
        ], [
            'usn.regex' => 'Username hanya boleh huruf kecil, angka, titik, dan underscore.',
            'kata_sandi.min' => 'Password minimal 8 karakter.',
        ]);

        $validated['a_aktif'] = $request->boolean('a_aktif', true);

        $result = $this->adminService->createLocalUser($validated);

        return redirect()->back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Import units dari file CSV.
     *
     * Route: POST /admin/units/sync
     * Middleware: auth, role:admin
     */
    public function syncUnits(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
            'mode'     => 'nullable|in:upsert,insert',
        ], [
            'csv_file.required' => 'Pilih file CSV terlebih dahulu.',
            'csv_file.mimes'    => 'File harus berformat CSV (.csv).',
            'csv_file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $filePath = $request->file('csv_file')->getRealPath();
        $mode     = $request->input('mode', 'upsert');

        $result = $this->unitSyncService->syncFromCsv($filePath, $mode);

        return redirect()->back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Download template CSV untuk import unit.
     *
     * Route: GET /admin/units/csv-template
     */
    public function downloadCsvTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_unit.csv"',
        ];

        $callback = function () {
            $h = fopen('php://output', 'w');
            fputs($h, "\xEF\xBB\xBF"); // BOM agar Excel baca UTF-8 dengan benar
            fputcsv($h, ['nm_lmbg', 'kode_unit', 'nm_kategori', 'a_aktif']);
            fputcsv($h, ['Fakultas Teknik', 'FT', 'Fakultas', '1']);
            fputcsv($h, ['Teknik Informatika', 'TI', 'Jurusan/Prodi', '1']);
            fputcsv($h, ['Lembaga Penelitian', 'LP', 'Lembaga', '1']);
            fputcsv($h, ['UPA Teknologi Informasi', 'TIK', 'UPA', '1']);
            fputcsv($h, ['Rektorat', 'RKT', 'Biro/Rektorat', '1']);
            fclose($h);
        };

        return response()->stream($callback, 200, $headers);
    }
}
