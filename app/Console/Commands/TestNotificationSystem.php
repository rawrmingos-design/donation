<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Withdrawal;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class TestNotificationSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notifications {--create-sample : Create sample notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the notification system functionality';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔔 Testing Notification System');
        $this->newLine();

        if ($this->option('create-sample')) {
            $this->createSampleNotifications();
        }

        $this->testNotificationCounts();
        $this->testRecentNotifications();
        $this->testNotificationsByType();
        
        $this->newLine();
        $this->info('✅ Notification system test completed!');
    }

    private function createSampleNotifications()
    {
        $this->info('📝 Creating sample notifications...');
        
        // Get a test user (creator)
        $user = User::where('role', 'creator')->first();
        if (!$user) {
            $this->error('No creator user found. Please create a creator user first.');
            return;
        }

        // Get a withdrawal for testing
        $withdrawal = Withdrawal::with('campaign')->first();
        if (!$withdrawal) {
            $this->error('No withdrawal found. Please create a withdrawal first.');
            return;
        }

        // Create different types of notifications
        $notificationTypes = [
            'withdrawal_approved',
            'withdrawal_rejected', 
            'withdrawal_processing',
            'withdrawal_completed',
        ];

        foreach ($notificationTypes as $type) {
            $this->notificationService->sendWithdrawalNotification($withdrawal, $type);
            $this->line("✓ Created {$type} notification");
        }

        $this->info('✅ Sample notifications created successfully!');
        $this->newLine();
    }

    private function testNotificationCounts()
    {
        $this->info('📊 Testing notification counts...');
        
        $users = User::whereIn('role', ['creator', 'admin'])->get();
        
        foreach ($users as $user) {
            $totalCount = $user->notifications()->count();
            $unreadCount = $this->notificationService->getUnreadCount($user);
            
            $this->line("User: {$user->name} ({$user->role})");
            $this->line("  Total notifications: {$totalCount}");
            $this->line("  Unread notifications: {$unreadCount}");
            $this->newLine();
        }
    }

    private function testRecentNotifications()
    {
        $this->info('📋 Recent notifications (last 5):');
        
        $recentNotifications = \App\Models\Notification::with('notifiable')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        if ($recentNotifications->isEmpty()) {
            $this->warn('No notifications found.');
            return;
        }

        foreach ($recentNotifications as $notification) {
            $user = $notification->notifiable;
            $status = $notification->read_at ? '✓ Read' : '● Unread';
            
            $this->line("ID: {$notification->id} | {$user->name} | {$notification->type} | {$status}");
            $this->line("  Title: {$notification->data['title']}");
            $this->line("  Created: {$notification->created_at->format('Y-m-d H:i:s')}");
            $this->newLine();
        }
    }

    private function testNotificationsByType()
    {
        $this->info('📈 Notifications by type:');
        
        $notificationsByType = \App\Models\Notification::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->get();

        if ($notificationsByType->isEmpty()) {
            $this->warn('No notifications found.');
            return;
        }

        foreach ($notificationsByType as $typeCount) {
            $this->line("{$typeCount->type}: {$typeCount->count}");
        }
    }
}
