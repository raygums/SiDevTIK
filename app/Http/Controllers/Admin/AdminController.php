<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use Illuminate\Http\Request;
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

    /**
     * Constructor - Dependency Injection
     */
    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
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

        $users = $this->adminService->getUsersForVerification($filters, 15);
        $stats = $this->adminService->getUserStatistics();

        return view('admin.user-verification', compact('users', 'stats', 'filters'));
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
     * Halaman audit logs user
     * 
     * Route: GET /admin/users/{uuid}/logs
     * Middleware: auth, role:admin
     */
    public function userLogs(string $uuid): View
    {
        $auditData = $this->adminService->getUserAuditLogs($uuid);

        return view('admin.user-logs', $auditData);
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
}
