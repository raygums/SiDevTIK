<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    protected AuditLogService $auditLogService;

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    /**
     * Display all activity logs (login + submission)
     * 
     * @return \Illuminate\View\View
     */
    public function loginLogs(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'user_uuid' => $request->get('user_uuid'),
            'log_type' => $request->get('log_type', 'all'),
        ];

        // Get per page from request (default: 20, allowed: 10, 20, 50, 100)
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;

        // Detect view mode - has any active filter
        $hasFilters = !empty($filters['search']) || 
                      !empty($filters['status']) || 
                      !empty($filters['date_from']) || 
                      !empty($filters['date_to']) || 
                      !empty($filters['user_uuid']) || 
                      (!empty($filters['log_type']) && $filters['log_type'] !== 'all');

        $stats = $this->auditLogService->getLoginStatisticsNew();
        $user = null;

        // MODE 1: Show user list (default, no filters)
        if (!$hasFilters) {
            $users = $this->auditLogService->getUsersWithLastActivity(1, $perPage);
            
            return view('admin.audit.aktivitas', compact('users', 'stats', 'filters'));
        }

        // MODE 2: Show activity list (with filters/search)
        $currentPage = $request->get('page', 1);
        
        // Get login logs as Collection (not paginated) - untuk di-merge
        $loginLogs = collect();
        if (in_array($filters['log_type'], ['all', 'login'])) {
            $loginLogs = $this->auditLogService->getLoginHistoryCollection($filters);
        }
        
        // Get submission logs as Collection (not paginated) - untuk di-merge
        $submissionLogs = collect();
        if (in_array($filters['log_type'], ['all', 'submission'])) {
            $submissionLogs = $this->auditLogService->getSubmissionLogsCollection($filters);
        }

        // Merge and sort
        $allLogs = $loginLogs->merge($submissionLogs)
            ->sortByDesc(function($log) {
                return $log->create_at;
            })
            ->values();

        // Manual pagination
        $logs = new \Illuminate\Pagination\LengthAwarePaginator(
            $allLogs->forPage($currentPage, $perPage),
            $allLogs->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Get user info if filtering by specific user
        if (!empty($filters['user_uuid'])) {
            $user = \App\Models\User::with('peran')->where('UUID', $filters['user_uuid'])->first();
        }

        return view('admin.audit.aktivitas', compact('logs', 'stats', 'filters', 'user'));
    }

    /**
     * Display submission status change logs
     * 
     * @return \Illuminate\View\View
     */
    public function submissionLogs(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'service_type' => $request->get('service_type'),
            'status' => $request->get('status'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        // Get per page from request (default: 20, allowed: 10, 20, 50, 100)
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;

        $logs = $this->auditLogService->getSubmissionLogs($filters, $perPage);

        $stats = $this->auditLogService->getSubmissionStatistics();

        return view('admin.audit.submissions', compact('logs', 'stats', 'filters'));
    }

    /**
     * Display user detail activity timeline
     * 
     * @param string $userUuid
     * @return \Illuminate\View\View
     */
    public function userDetail(string $userUuid)
    {
        $data = $this->auditLogService->getUserActivityTimeline($userUuid, 20);

        return view('admin.audit.user-detail', [
            'user' => $data['user'],
            'timeline' => $data['timeline']
        ]);
    }
}
