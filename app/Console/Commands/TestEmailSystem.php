<?php

namespace App\Console\Commands;

use App\Models\Donation;
use App\Models\Campaign;
use App\Mail\DonationConfirmation;
use App\Mail\CampaignMilestone;
use App\Mail\CampaignCompleted;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {type=donation : Type of email to test (donation, milestone, completed)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email system with sample data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');

        switch ($type) {
            case 'donation':
                $this->testDonationEmail();
                break;
            case 'milestone':
                $this->testMilestoneEmail();
                break;
            case 'completed':
                $this->testCompletedEmail();
                break;
            default:
                $this->error('Invalid email type. Use: donation, milestone, completed');
                return 1;
        }

        return 0;
    }

    private function testDonationEmail()
    {
        $this->info('Testing donation confirmation email...');

        // Get a sample donation with relations
        $donation = Donation::with(['donor', 'campaign.user', 'campaign.category'])
            ->where('status', 'success')
            ->first();

        if (!$donation) {
            $this->error('No successful donations found. Please create some test data first.');
            return;
        }

        try {
            Mail::send(new DonationConfirmation($donation));
            
            $this->info('✅ Donation confirmation email sent successfully!');
            $this->line('Sent to: ' . $donation->donor->email);
            $this->line('Campaign: ' . $donation->campaign->title);
            $this->line('Amount: Rp ' . number_format($donation->amount, 0, ',', '.'));
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
        }
    }

    private function testMilestoneEmail()
    {
        $this->info('Testing milestone notification email...');

        // Get a sample campaign
        $campaign = Campaign::with(['user', 'category'])
            ->where('collected_amount', '>', 0)
            ->first();

        if (!$campaign) {
            $this->error('No campaigns with donations found. Please create some test data first.');
            return;
        }

        $milestone = $this->choice(
            'Which milestone to test?',
            ['50', '75', '100'],
            0
        );

        try {
            Mail::send(new CampaignMilestone($campaign, (int)$milestone));
            
            $this->info('✅ Milestone notification email sent successfully!');
            $this->line('Sent to: ' . $campaign->user->email);
            $this->line('Campaign: ' . $campaign->title);
            $this->line('Milestone: ' . $milestone . '%');
            $this->line('Current Progress: ' . number_format(($campaign->collected_amount / $campaign->target_amount) * 100, 1) . '%');
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
        }
    }

    private function testCompletedEmail()
    {
        $this->info('Testing campaign completion email...');

        // Get a sample campaign
        $campaign = Campaign::with(['user', 'category'])
            ->where('collected_amount', '>', 0)
            ->first();

        if (!$campaign) {
            $this->error('No campaigns with donations found. Please create some test data first.');
            return;
        }

        try {
            Mail::send(new CampaignCompleted($campaign));
            
            $this->info('✅ Campaign completion email sent successfully!');
            $this->line('Sent to: ' . $campaign->user->email);
            $this->line('Campaign: ' . $campaign->title);
            $this->line('Collected: Rp ' . number_format($campaign->collected_amount, 0, ',', '.'));
            $this->line('Target: Rp ' . number_format($campaign->target_amount, 0, ',', '.'));
            $this->line('Progress: ' . number_format(($campaign->collected_amount / $campaign->target_amount) * 100, 1) . '%');
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
        }
    }
}
