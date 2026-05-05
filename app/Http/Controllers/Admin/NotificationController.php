<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    /**
     * Daftar notifikasi untuk admin
     */
    public function index(Request $request): View
    {
        $notifications = AdminNotification::with(['relatedUser', 'relatedSubmission'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = AdminNotification::unread()->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Detail notifikasi
     */
    public function show(AdminNotification $notification): View
    {
        $notification->markAsRead();
        
        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * Mark as read
     */
    public function markAsRead(AdminNotification $notification): RedirectResponse
    {
        $notification->markAsRead();
        
        return back()->with('success', 'Notifikasi ditandai sudah dibaca');
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead(): RedirectResponse
    {
        AdminNotification::unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca');
    }

    /**
     * Delete notification
     */
    public function destroy(AdminNotification $notification): RedirectResponse
    {
        $notification->delete();
        
        return back()->with('success', 'Notifikasi dihapus');
    }

    /**
     * Get unread count for header
     */
    public function getUnreadCount()
    {
        return response()->json([
            'count' => AdminNotification::unread()->count(),
        ]);
    }
}
