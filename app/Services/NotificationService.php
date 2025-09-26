<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send withdrawal status notification
     */
    public function sendWithdrawalNotification(Withdrawal $withdrawal, string $type): void
    {
        $user = $withdrawal->campaign->user;
        
        // Create in-app notification
        $this->createNotification($user, $type, $this->getWithdrawalNotificationData($withdrawal, $type));
        
        // Send email notification
        $this->sendWithdrawalEmail($withdrawal, $type);
    }

    /**
     * Create in-app notification
     */
    public function createNotification(User $user, string $type, array $data): Notification
    {
        return $user->notifications()->create([
            'type' => $type,
            'data' => $data,
        ]);
    }

    /**
     * Send withdrawal email notification
     */
    private function sendWithdrawalEmail(Withdrawal $withdrawal, string $type): void
    {
        $user = $withdrawal->campaign->user;
        $data = $this->getWithdrawalNotificationData($withdrawal, $type);

        try {
            Mail::send('emails.withdrawal-notification', compact('withdrawal', 'data', 'type'), function ($message) use ($user, $data) {
                $message->to($user->email, $user->name)
                        ->subject($data['title']);
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send withdrawal email: ' . $e->getMessage());
        }
    }

    /**
     * Get notification data based on withdrawal status
     */
    private function getWithdrawalNotificationData(Withdrawal $withdrawal, string $type): array
    {
        $baseData = [
            'withdrawal_id' => $withdrawal->id,
            'campaign_title' => $withdrawal->campaign->title,
            'amount' => $withdrawal->amount,
            'formatted_amount' => 'Rp ' . number_format($withdrawal->amount, 0, ',', '.'),
        ];

        return match ($type) {
            'withdrawal_approved' => array_merge($baseData, [
                'title' => '✅ Penarikan Dana Disetujui',
                'message' => "Permintaan penarikan dana Anda untuk kampanye \"{$withdrawal->campaign->title}\" telah disetujui.",
                'icon' => '✅',
                'color' => 'success',
                'action_url' => "/withdrawals/{$withdrawal->id}",
            ]),
            
            'withdrawal_rejected' => array_merge($baseData, [
                'title' => '❌ Penarikan Dana Ditolak',
                'message' => "Permintaan penarikan dana Anda untuk kampanye \"{$withdrawal->campaign->title}\" telah ditolak.",
                'icon' => '❌',
                'color' => 'danger',
                'action_url' => "/withdrawals/{$withdrawal->id}",
                'notes' => $withdrawal->notes,
            ]),
            
            'withdrawal_processing' => array_merge($baseData, [
                'title' => '🔄 Penarikan Dana Diproses',
                'message' => "Penarikan dana Anda untuk kampanye \"{$withdrawal->campaign->title}\" sedang diproses.",
                'icon' => '🔄',
                'color' => 'info',
                'action_url' => "/withdrawals/{$withdrawal->id}",
            ]),
            
            'withdrawal_completed' => array_merge($baseData, [
                'title' => '🎉 Penarikan Dana Selesai',
                'message' => "Penarikan dana Anda untuk kampanye \"{$withdrawal->campaign->title}\" telah berhasil diselesaikan.",
                'icon' => '🎉',
                'color' => 'success',
                'action_url' => "/withdrawals/{$withdrawal->id}",
                'reference_number' => $withdrawal->reference_number,
            ]),
            
            'withdrawal_cancelled' => array_merge($baseData, [
                'title' => '🚫 Penarikan Dana Dibatalkan',
                'message' => "Penarikan dana Anda untuk kampanye \"{$withdrawal->campaign->title}\" telah dibatalkan.",
                'icon' => '🚫',
                'color' => 'secondary',
                'action_url' => "/withdrawals/{$withdrawal->id}",
            ]),
            
            default => array_merge($baseData, [
                'title' => '📋 Update Penarikan Dana',
                'message' => "Ada update pada penarikan dana Anda untuk kampanye \"{$withdrawal->campaign->title}\".",
                'icon' => '📋',
                'color' => 'primary',
                'action_url' => "/withdrawals/{$withdrawal->id}",
            ]),
        };
    }

    /**
     * Send admin notification for new withdrawal request
     */
    public function sendAdminWithdrawalNotification(Withdrawal $withdrawal): void
    {
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            $this->createNotification($admin, 'new_withdrawal_request', [
                'title' => '🔔 Permintaan Penarikan Baru',
                'message' => "Permintaan penarikan dana baru dari {$withdrawal->campaign->user->name} untuk kampanye \"{$withdrawal->campaign->title}\".",
                'icon' => '🔔',
                'color' => 'warning',
                'withdrawal_id' => $withdrawal->id,
                'campaign_title' => $withdrawal->campaign->title,
                'creator_name' => $withdrawal->campaign->user->name,
                'amount' => $withdrawal->amount,
                'formatted_amount' => 'Rp ' . number_format($withdrawal->amount, 0, ',', '.'),
                'action_url' => "/admin/withdrawals/{$withdrawal->id}",
            ]);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId, User $user): bool
    {
        $notification = $user->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        
        return false;
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead(User $user): void
    {
        $user->unreadNotifications()->update(['read_at' => now()]);
    }

    /**
     * Get unread notification count for user
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }
}
