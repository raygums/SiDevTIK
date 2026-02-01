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
     * Display login activity logs
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
            'has_login' => $request->get('has_login', 'yes'), // default: yang sudah login
        ];

        $logs = $this->auditLogService->getLoginLogs($filters, 20);

        $stats = $this->auditLogService->getLoginStatistics();

        return view('admin.audit.login', compact('logs', 'stats', 'filters'));
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

        $logs = $this->auditLogService->getSubmissionLogs($filters, 20);

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
