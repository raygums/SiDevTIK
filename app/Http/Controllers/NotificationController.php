<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Daftar notifikasi/aktivitas untuk user
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        $notifications = AdminNotification::where('related_user_uuid', $user->UUID)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $unreadCount = AdminNotification::where('related_user_uuid', $user->UUID)
            ->unread()
            ->count();

        return view('user.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Detail notifikasi
     */
    public function show(AdminNotification $notification): View
    {
        $user = Auth::user();
        
        // Pastikan user hanya bisa lihat notifikasi miliknya
        if ($notification->related_user_uuid !== $user->UUID) {
            abort(403, 'Unauthorized');
        }

        $notification->markAsRead();
        
        return view('user.notifications.show', compact('notification'));
    }

    /**
     * Mark as read
     */
    public function markAsRead(AdminNotification $notification)
    {
        $user = Auth::user();
        
        if ($notification->related_user_uuid !== $user->UUID) {
            abort(403, 'Unauthorized');
        }

        $notification->markAsRead();
        
        return back()->with('success', 'Notifikasi ditandai sudah dibaca');
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        AdminNotification::where('related_user_uuid', $user->UUID)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca');
    }
}
