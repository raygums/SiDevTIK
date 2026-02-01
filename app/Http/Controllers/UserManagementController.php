<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * UserManagementController - Thin Controller Pattern
 * 
 * Responsibilities:
 * - Handle HTTP request/response
 * - Validate input
 * - Delegate business logic ke UserService
 * - Return view atau redirect dengan appropriate response
 * 
 * Business logic ada di UserService (Fat Service, Thin Controller).
 */
class UserManagementController extends Controller
{
    /**
     * UserService instance
     */
    protected UserService $userService;

    /**
     * Constructor - Dependency Injection
     * 
     * Laravel secara otomatis resolve UserService dari service container.
     * Ini memudahkan testing (dapat di-mock) dan mengikuti Dependency Inversion Principle.
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display filtered user list dengan advanced filtering.
     * 
     * Route: GET /verifikator/users
     * Middleware: auth, role:admin,verifikator
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        // Extract filters dari request
        $filters = [
            'tipe_akun' => $request->query('tipe_akun', 'all'),
            'identity' => $request->query('identity', 'all'),
            'status' => $request->query('status', 'tidak_aktif'), // Default: tampilkan user yang belum aktif
            'search' => $request->query('search', ''),
        ];

        // Get paginated users via service
        $users = $this->userService->getFilteredUsers($filters, 15);

        // Get statistics untuk dashboard cards
        $stats = $this->userService->getUserStatistics();

        return view('verifikator.users.index', compact('users', 'stats', 'filters'));
    }

    /**
     * Toggle user activation status (aktif <-> tidak aktif).
     * 
     * Route: POST /verifikator/users/{uuid}/toggle
     * Middleware: auth, role:admin,verifikator
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(Request $request, string $uuid): RedirectResponse
    {
        // Delegate ke service
        $result = $this->userService->toggleUserStatus($uuid, Auth::id());

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Bulk activate selected users.
     * 
     * Route: POST /verifikator/users/bulk-activate
     * Middleware: auth, role:admin,verifikator
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkActivate(Request $request): RedirectResponse
    {
        // Validate request
        $validated = $request->validate([
            'user_uuids' => 'required|array|min:1',
            'user_uuids.*' => 'required|string|uuid',
        ]);

        // Delegate ke service
        $result = $this->userService->bulkActivateUsers(
            $validated['user_uuids'],
            Auth::id()
        );

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
