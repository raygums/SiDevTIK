<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Services\PimpinanService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * PimpinanController - Controller untuk fitur Pimpinan
 * 
 * Responsibilities:
 * - Dashboard dengan overview sistem
 * - Manajemen pengguna semua role
 * - View activity logs sistem
 */
class PimpinanController extends Controller
{
    protected PimpinanService $pimpinanService;

    public function __construct(PimpinanService $pimpinanService)
    {
        $this->pimpinanService = $pimpinanService;
    }

    /**
     * Dashboard Pimpinan
     */
    public function dashboard(): View
    {
        $stats = $this->pimpinanService->getDashboardStats();
        $recentLogs = $this->pimpinanService->getRecentActivityLogs(8);

        return view('pimpinan.dashboard', compact('stats', 'recentLogs'));
    }

    /**
     * Manajemen Pengguna - List semua user dengan filter
     */
    public function users(Request $request): View
    {
        $filters = [
            'role' => $request->get('role', 'all'),
            'status' => $request->get('status', 'all'),
            'tipe_akun' => $request->get('tipe_akun', 'all'),
            'search' => $request->get('search'),
            'sort_by' => $request->get('sort_by', 'create_at'),
            'sort_dir' => $request->get('sort_dir', 'desc'),
        ];

        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;

        $users = $this->pimpinanService->getAllUsers($filters, $perPage);
        $stats = $this->pimpinanService->getUserStatistics();
        $roles = $this->pimpinanService->getAllRoles();

        return view('pimpinan.users', compact('users', 'stats', 'filters', 'roles'));
    }

    /**
     * Detail pengguna
     */
    public function userDetail(string $uuid): View
    {
        $data = $this->pimpinanService->getUserDetail($uuid);
        $roles = $this->pimpinanService->getAllRoles();

        return view('pimpinan.user-detail', array_merge($data, ['roles' => $roles]));
    }

    /**
     * Toggle status user
     */
    public function toggleUserStatus(string $uuid): RedirectResponse
    {
        $success = $this->pimpinanService->toggleUserStatus($uuid);

        if ($success) {
            return redirect()->back()->with('success', 'Status pengguna berhasil diubah.');
        }

        return redirect()->back()->with('error', 'Gagal mengubah status pengguna.');
    }

    /**
     * Change user role
     */
    public function changeUserRole(Request $request, string $uuid): RedirectResponse
    {
        $request->validate([
            'role_uuid' => 'required|uuid',
        ]);

        $success = $this->pimpinanService->changeUserRole($uuid, $request->role_uuid);

        if ($success) {
            return redirect()->back()->with('success', 'Role pengguna berhasil diubah.');
        }

        return redirect()->back()->with('error', 'Gagal mengubah role pengguna.');
    }

    /**
     * Bulk activate users
     */
    public function bulkActivate(Request $request): RedirectResponse
    {
        $request->validate([
            'user_uuids' => 'required|array|min:1',
            'user_uuids.*' => 'required|uuid',
        ]);

        $count = $this->pimpinanService->bulkActivateUsers($request->user_uuids);

        if ($count > 0) {
            return redirect()->back()->with('success', "{$count} pengguna berhasil diaktifkan.");
        }

        return redirect()->back()->with('error', 'Tidak ada pengguna yang diaktifkan.');
    }

    /**
     * Bulk deactivate users
     */
    public function bulkDeactivate(Request $request): RedirectResponse
    {
        $request->validate([
            'user_uuids' => 'required|array|min:1',
            'user_uuids.*' => 'required|uuid',
        ]);

        $count = $this->pimpinanService->bulkDeactivateUsers($request->user_uuids);

        if ($count > 0) {
            return redirect()->back()->with('success', "{$count} pengguna berhasil dinonaktifkan.");
        }

        return redirect()->back()->with('error', 'Tidak ada pengguna yang dinonaktifkan.');
    }

    /**
     * Activity Logs - Semua aktivitas sistem
     */
    public function activityLogs(Request $request): View
    {
        $filters = [
            'actor_role' => $request->get('actor_role', 'all'),
            'action_type' => $request->get('action_type', 'all'),
            'service_type' => $request->get('service_type', 'all'),
            'search' => $request->get('search'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;

        $logs = $this->pimpinanService->getActivityLogs($filters, $perPage);
        $stats = $this->pimpinanService->getRecentActivityStats();

        return view('pimpinan.activity-logs', compact('logs', 'stats', 'filters'));
    }

    /**
     * Activity Detail - Detail satu log aktivitas
     */
    public function activityDetail(string $uuid): View
    {
        $log = $this->pimpinanService->getActivityDetail($uuid);

        if (!$log) {
            abort(404, 'Log aktivitas tidak ditemukan.');
        }

        return view('pimpinan.activity-detail', compact('log'));
    }
}
