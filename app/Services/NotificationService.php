<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Kirim notifikasi user registration ke admin
     */
    public static function notifyUserRegistration(User $user): void
    {
        try {
            $message = "User baru '{$user->nm}' ({$user->email}) telah mendaftar dan menunggu aktivasi.";
            
            // Simpan notifikasi di database untuk dashboard admin
            AdminNotification::create([
                'type' => 'user_registered',
                'title' => 'User Baru Terdaftar',
                'message' => $message,
                'related_user_uuid' => $user->UUID,
            ]);

            // Kirim email ke admin (jika diperlukan, konfigurasi admin email di .env)
            $adminEmail = config('app.admin_email', 'admin@sidevtik.com');
            // Mail::to($adminEmail)->send(new \App\Mail\UserRegistrationNotification($user));
        } catch (\Exception $e) {
            Log::error('Error in notifyUserRegistration: ' . $e->getMessage());
        }
    }

    /**
     * Kirim notifikasi user activation ke user
     */
    public static function notifyUserActivation(User $user): void
    {
        try {
            $message = "Akun Anda telah diaktifkan oleh administrator. Anda sekarang dapat login ke sistem.";
            
            // Simpan notifikasi di database
            AdminNotification::create([
                'type' => 'user_activated',
                'title' => 'Akun Diaktifkan',
                'message' => $message,
                'related_user_uuid' => $user->UUID,
            ]);

            // Kirim email ke user
            try {
                Mail::send(
                    'emails.user-activated',
                    [
                        'user' => $user,
                        'message' => $message,
                    ],
                    function ($message) use ($user) {
                        $message->to($user->email)
                            ->subject('Akun Anda Telah Diaktifkan');
                    }
                );
            } catch (\Exception $e) {
                Log::warning('Email sending failed but notification saved: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('Error in notifyUserActivation: ' . $e->getMessage());
        }
    }

    /**
     * Kirim notifikasi submission status change
     */
    public static function notifySubmissionStatusChange($submission, $oldStatus, $newStatus, $notes = null): void
    {
        try {
            $title = "Status Pengajuan Berubah";
            $message = "Status pengajuan #{$submission->no_tiket} berubah dari '{$oldStatus}' menjadi '{$newStatus}'.";
            
            if ($notes) {
                $message .= "\n\nCatatan: {$notes}";
            }

            // Simpan notifikasi di database
            AdminNotification::create([
                'type' => 'submission_status_changed',
                'title' => $title,
                'message' => $message,
                'related_submission_uuid' => $submission->UUID,
                'related_user_uuid' => $submission->pengguna_uuid,
            ]);

            // Kirim email ke pemohon jika diperlukan
            if ($submission->pengguna && $submission->pengguna->email) {
                try {
                    Mail::send(
                        'emails.submission-status-changed',
                        [
                            'user' => $submission->pengguna,
                            'submission' => $submission,
                            'oldStatus' => $oldStatus,
                            'newStatus' => $newStatus,
                            'notes' => $notes,
                        ],
                        function ($message) use ($submission) {
                            $message->to($submission->pengguna->email)
                                ->subject("Pembaruan Status Pengajuan #{$submission->no_tiket}");
                        }
                    );
                } catch (\Exception $e) {
                    Log::warning('Email sending failed: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in notifySubmissionStatusChange: ' . $e->getMessage());
        }
    }

    /**
     * Get unread notifications count untuk admin
     */
    public static function getUnreadNotificationsCount(): int
    {
        return AdminNotification::unread()->count();
    }

    /**
     * Get recent notifications untuk admin
     */
    public static function getRecentNotifications($limit = 10)
    {
        return AdminNotification::with(['relatedUser', 'relatedSubmission'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
