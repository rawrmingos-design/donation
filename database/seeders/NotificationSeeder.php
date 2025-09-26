<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use App\Models\Withdrawal;
use App\Services\NotificationService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->command->info('🔔 Seeding notifications...');

        // Get users for notifications
        $creators = User::where('role', 'creator')->get();
        $admins = User::where('role', 'admin')->get();

        if ($creators->isEmpty()) {
            $this->command->warn('No creator users found. Skipping notification seeding.');
            return;
        }

        // Get withdrawals for realistic notifications
        $withdrawals = Withdrawal::with(['campaign', 'campaign.user'])->get();

        if ($withdrawals->isEmpty()) {
            $this->command->warn('No withdrawals found. Creating sample notifications without withdrawals.');
            $this->createSampleNotifications($creators, $admins);
            return;
        }

        $notificationService = app(NotificationService::class);
        $notificationCount = 0;

        // Create notifications for each withdrawal
        foreach ($withdrawals->take(5) as $withdrawal) {
            $creator = $withdrawal->campaign->user;

            // Create different types of notifications based on withdrawal status
            switch ($withdrawal->status) {
                case 'pending':
                    // Admin notification for new request
                    foreach ($admins as $admin) {
                        $notificationService->createNotification($admin, 'new_withdrawal_request', [
                            'title' => '🔔 Permintaan Penarikan Baru',
                            'message' => "Permintaan penarikan dana baru dari {$creator->name} untuk kampanye \"{$withdrawal->campaign->title}\".",
                            'icon' => '🔔',
                            'color' => 'warning',
                            'withdrawal_id' => $withdrawal->id,
                            'campaign_title' => $withdrawal->campaign->title,
                            'creator_name' => $creator->name,
                            'amount' => $withdrawal->amount,
                            'formatted_amount' => 'Rp ' . number_format($withdrawal->amount, 0, ',', '.'),
                            'action_url' => "/admin/withdrawals/{$withdrawal->id}",
                        ]);
                        $notificationCount++;
                    }
                    break;

                case 'approved':
                    $notificationService->createNotification($creator, 'withdrawal_approved', [
                        'title' => '✅ Penarikan Dana Disetujui',
                        'message' => "Permintaan penarikan dana Anda untuk kampanye \"{$withdrawal->campaign->title}\" telah disetujui.",
                        'icon' => '✅',
                        'color' => 'success',
                        'withdrawal_id' => $withdrawal->id,
                        'campaign_title' => $withdrawal->campaign->title,
                        'amount' => $withdrawal->amount,
                        'formatted_amount' => 'Rp ' . number_format($withdrawal->amount, 0, ',', '.'),
                        'action_url' => "/withdrawals/{$withdrawal->id}",
                    ]);
                    $notificationCount++;
                    break;

                case 'completed':
                    $notificationService->createNotification($creator, 'withdrawal_completed', [
                        'title' => '🎉 Penarikan Dana Selesai',
                        'message' => "Penarikan dana Anda untuk kampanye \"{$withdrawal->campaign->title}\" telah berhasil diselesaikan.",
                        'icon' => '🎉',
                        'color' => 'success',
                        'withdrawal_id' => $withdrawal->id,
                        'campaign_title' => $withdrawal->campaign->title,
                        'amount' => $withdrawal->amount,
                        'formatted_amount' => 'Rp ' . number_format($withdrawal->amount, 0, ',', '.'),
                        'reference_number' => $withdrawal->reference_number,
                        'action_url' => "/withdrawals/{$withdrawal->id}",
                    ]);
                    $notificationCount++;
                    break;

                case 'rejected':
                    $notificationService->createNotification($creator, 'withdrawal_rejected', [
                        'title' => '❌ Penarikan Dana Ditolak',
                        'message' => "Permintaan penarikan dana Anda untuk kampanye \"{$withdrawal->campaign->title}\" telah ditolak.",
                        'icon' => '❌',
                        'color' => 'danger',
                        'withdrawal_id' => $withdrawal->id,
                        'campaign_title' => $withdrawal->campaign->title,
                        'amount' => $withdrawal->amount,
                        'formatted_amount' => 'Rp ' . number_format($withdrawal->amount, 0, ',', '.'),
                        'notes' => $withdrawal->notes ?: 'Dokumen tidak lengkap atau tidak memenuhi syarat.',
                        'action_url' => "/withdrawals/{$withdrawal->id}",
                    ]);
                    $notificationCount++;
                    break;
            }
        }

        // Create some additional sample notifications
        $this->createAdditionalNotifications($creators, $admins);
        $notificationCount += 6;

        $this->command->info("✅ Created {$notificationCount} sample notifications");
    }

    private function createSampleNotifications($creators, $admins)
    {
        $notificationService = app(NotificationService::class);

        foreach ($creators->take(2) as $creator) {
            // Sample withdrawal notifications
            $notificationService->createNotification($creator, 'withdrawal_approved', [
                'title' => '✅ Penarikan Dana Disetujui',
                'message' => 'Permintaan penarikan dana Anda telah disetujui dan sedang diproses.',
                'icon' => '✅',
                'color' => 'success',
                'formatted_amount' => 'Rp 1.500.000',
                'action_url' => '/withdrawals/1',
            ]);

            $notificationService->createNotification($creator, 'withdrawal_completed', [
                'title' => '🎉 Penarikan Dana Selesai',
                'message' => 'Dana telah berhasil ditransfer ke rekening Anda.',
                'icon' => '🎉',
                'color' => 'success',
                'formatted_amount' => 'Rp 2.750.000',
                'reference_number' => 'WD' . strtoupper(uniqid()),
                'action_url' => '/withdrawals/2',
            ]);
        }

        foreach ($admins->take(1) as $admin) {
            $notificationService->createNotification($admin, 'new_withdrawal_request', [
                'title' => '🔔 Permintaan Penarikan Baru',
                'message' => 'Ada permintaan penarikan dana baru yang perlu ditinjau.',
                'icon' => '🔔',
                'color' => 'warning',
                'formatted_amount' => 'Rp 3.200.000',
                'action_url' => '/admin/withdrawals',
            ]);
        }
    }

    private function createAdditionalNotifications($creators, $admins)
    {
        $notificationService = app(NotificationService::class);

        // Campaign milestone notifications
        foreach ($creators->take(2) as $creator) {
            $notificationService->createNotification($creator, 'campaign_milestone', [
                'title' => '🎯 Kampanye Mencapai 75%',
                'message' => 'Selamat! Kampanye Anda telah mencapai 75% dari target.',
                'icon' => '🎯',
                'color' => 'info',
                'action_url' => '/campaigns/1',
            ]);

            $notificationService->createNotification($creator, 'campaign_completed', [
                'title' => '🎉 Kampanye Berhasil!',
                'message' => 'Kampanye Anda telah mencapai 100% dari target. Terima kasih!',
                'icon' => '🎉',
                'color' => 'success',
                'action_url' => '/campaigns/2',
            ]);
        }

        // System notifications
        foreach ($admins->take(1) as $admin) {
            $notificationService->createNotification($admin, 'system_report', [
                'title' => '📊 Laporan Harian',
                'message' => 'Laporan aktivitas platform hari ini telah tersedia.',
                'icon' => '📊',
                'color' => 'info',
                'action_url' => '/admin/reports',
            ]);

            $notificationService->createNotification($admin, 'new_campaign_review', [
                'title' => '👀 Kampanye Perlu Review',
                'message' => 'Ada kampanye baru yang perlu ditinjau dan disetujui.',
                'icon' => '👀',
                'color' => 'warning',
                'action_url' => '/admin/campaigns',
            ]);
        }
    }
}
