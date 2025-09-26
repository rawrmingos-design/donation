<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Mail\DonationConfirmation;
use App\Mail\CampaignMilestone;
use App\Mail\CampaignCompleted;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Donation;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        $this->updateTransactionCache($transaction);
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        Log::info('TransactionObserver.updated triggered', [
            'transaction_id' => $transaction->id,
            'ref_id' => $transaction->ref_id,
            'status' => $transaction->status,
            'is_dirty_status' => $transaction->isDirty('status'),
            'original_status' => $transaction->getOriginal('status')
        ]);

        $this->updateTransactionCache($transaction);
        
        // If transaction status changed to completed, update campaign stats
        if ($transaction->isDirty('status') && $transaction->status === 'completed') {
            Log::info('Scheduling campaign stats update via afterCommit', [
                'transaction_id' => $transaction->id,
                'ref_id' => $transaction->ref_id
            ]);
            
            // Use dispatch to run after current transaction commits
            DB::afterCommit(function () use ($transaction) {
                Log::info('afterCommit callback executing', [
                    'transaction_id' => $transaction->id,
                    'ref_id' => $transaction->ref_id
                ]);
                $this->updateCampaignStats($transaction);
            });
        } else {
            Log::info('Skipping campaign stats update', [
                'transaction_id' => $transaction->id,
                'status' => $transaction->status,
                'is_dirty' => $transaction->isDirty('status')
            ]);
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        $this->clearTransactionCache($transaction);
    }

    /**
     * Update transaction cache with full relationship data
     */
    private function updateTransactionCache(Transaction $transaction): void
    {
        try {
            // Load all necessary relationships
            $transaction->load([
                'donation.campaign',
                'donation.donor',
                'paymentChannel',
                'paymentProvider'
            ]);

            $cacheKey = "transaction:{$transaction->ref_id}";
            $cacheData = [
                'id' => $transaction->id,
                'ref_id' => $transaction->ref_id,
                'status' => $transaction->status,
                'amount' => $transaction->amount,
                'total_paid' => $transaction->total_paid,
                'total_received' => $transaction->total_received,
                'payment_url' => $transaction->payment_url,
                'snap_token' => $transaction->snap_token,
                'qr_code' => $transaction->qr_code,
                'payment_type' => $transaction->payment_type,
                'fraud_status' => $transaction->fraud_status,
                'settlement_time' => $transaction->settlement_time,
                'provider_response' => $transaction->provider_response,
                'expired_at' => $transaction->expired_at,
                'paid_at' => $transaction->paid_at,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
                'donation' => [
                    'id' => $transaction->donation->id,
                    'amount' => $transaction->donation->amount,
                    'message' => $transaction->donation->message,
                    'campaign' => [
                        'id' => $transaction->donation->campaign->id,
                        'title' => $transaction->donation->campaign->title,
                        'slug' => $transaction->donation->campaign->slug,
                        'collected_amount' => $transaction->donation->campaign->collected_amount,
                        'target_amount' => $transaction->donation->campaign->target_amount,
                    ],
                    'donor' => [
                        'id' => $transaction->donation->donor->id,
                        'name' => $transaction->donation->donor->name,
                        'email' => $transaction->donation->donor->email,
                        'phone' => $transaction->donation->donor->phone,
                        'is_anonymous' => $transaction->donation->donor->is_anonymous,
                    ]
                ],
                'payment_channel' => $transaction->paymentChannel ? [
                    'id' => $transaction->paymentChannel->id,
                    'name' => $transaction->paymentChannel->name,
                    'code' => $transaction->paymentChannel->code,
                ] : null,
                'payment_provider' => [
                    'id' => $transaction->paymentProvider->id,
                    'name' => $transaction->paymentProvider->name,
                    'code' => $transaction->paymentProvider->code,
                ]
            ];

            // Cache for 24 hours (or until updated)
            Cache::put($cacheKey, $cacheData, now()->addHours(24));

            Log::info('Transaction cache updated', [
                'ref_id' => $transaction->ref_id,
                'cache_key' => $cacheKey
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update transaction cache: ' . $e->getMessage(), [
                'ref_id' => $transaction->ref_id ?? 'unknown',
                'transaction_id' => $transaction->id ?? 'unknown'
            ]);
        }
    }

    /**
     * Clear transaction cache
     */
    private function clearTransactionCache(Transaction $transaction): void
    {
        try {
            $cacheKey = "transaction:{$transaction->ref_id}";
            Cache::forget($cacheKey);

            Log::info('Transaction cache cleared', [
                'ref_id' => $transaction->ref_id,
                'cache_key' => $cacheKey
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to clear transaction cache: ' . $e->getMessage(), [
                'ref_id' => $transaction->ref_id ?? 'unknown'
            ]);
        }
    }

    /**
     * Update campaign statistics when transaction is completed
     */
    private function updateCampaignStats(Transaction $transaction): void
    {
        try {
            // Reload transaction with fresh data and lock for update
            $transaction = Transaction::with(['donation.campaign'])
                ->lockForUpdate()
                ->find($transaction->id);
                
            if (!$transaction) {
                Log::error('Transaction not found when updating campaign stats', ['ref_id' => $transaction->ref_id ?? 'unknown']);
                return;
            }
            
            $campaign = $transaction->donation->campaign;
            $donation = $transaction->donation;
            
            if (!$campaign || !$donation) {
                Log::warning('Campaign or donation not found for transaction', ['ref_id' => $transaction->ref_id]);
                return;
            }

            // Check if this donation was already processed (prevent double counting)
            if ($donation->status === 'success') {
                Log::info('Donation already processed, skipping campaign update', [
                    'donation_id' => $donation->id,
                    'transaction_ref' => $transaction->ref_id
                ]);
                return;
            }

            Log::info('Processing donation for campaign stats update', [
                'donation_id' => $donation->id,
                'donation_status' => $donation->status,
                'donation_amount' => $donation->amount,
                'campaign_id' => $campaign->id,
                'transaction_ref' => $transaction->ref_id
            ]);

            // Update donation status first, then increment campaign stats
            $updateDonationStatus = Donation::where('id', $donation->id)->update(['status' => 'success']);
            
            // Increment campaign stats with this donation amount
            $campaign->increment('collected_amount', $donation->amount);
            $campaign->increment('donors_count', 1);
            
            // Refresh campaign to get updated values
            $campaign->refresh();

            // Check if campaign reached target and update status
            if ($campaign->collected_amount >= $campaign->target_amount && $campaign->status === 'active') {
                $campaign->update(['status' => 'completed']);
                
                // Send campaign completion email
                try {
                    Mail::send(new CampaignCompleted($campaign));
                    
                    Log::info('Campaign completion email sent', [
                        'campaign_id' => $campaign->id,
                        'creator_email' => $campaign->user->email,
                        'collected_amount' => $campaign->collected_amount,
                        'target_amount' => $campaign->target_amount
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send campaign completion email: ' . $e->getMessage(), [
                        'campaign_id' => $campaign->id,
                        'creator_email' => $campaign->user->email ?? 'unknown'
                    ]);
                }
                
                Log::info('Campaign completed - target reached', [
                    'campaign_id' => $campaign->id,
                    'collected_amount' => $campaign->collected_amount,
                    'target_amount' => $campaign->target_amount
                ]);
            }

            // Clear campaign cache to ensure fresh data
            Cache::forget("campaign_detail_{$campaign->id}");
            Cache::forget("campaigns_*"); // Clear campaigns list cache
            
            // Send donation confirmation email
            try {
                Mail::send(new DonationConfirmation($donation));
                
                Log::info('Donation confirmation email sent', [
                    'donation_id' => $donation->id,
                    'donor_email' => $donation->donor->email,
                    'campaign_id' => $campaign->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send donation confirmation email: ' . $e->getMessage(), [
                    'donation_id' => $donation->id,
                    'donor_email' => $donation->donor->email ?? 'unknown'
                ]);
            }

            // Check for milestone achievements and send notifications
            $this->checkAndSendMilestoneNotifications($campaign);

            Log::info('Campaign stats updated via transaction observer', [
                'campaign_id' => $campaign->id,
                'transaction_ref' => $transaction->ref_id,
                'donation_id' => $donation->id,
                'donation_status_after_update' => $donation->fresh()->status,
                'new_collected_amount' => $campaign->collected_amount,
                'new_donors_count' => $campaign->donors_count
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update campaign stats: ' . $e->getMessage(), [
                'ref_id' => $transaction->ref_id ?? 'unknown',
                'transaction_id' => $transaction->id ?? 'unknown'
            ]);
        }
    }

    /**
     * Check and send milestone notifications for campaign progress
     */
    private function checkAndSendMilestoneNotifications($campaign): void
    {
        try {
            if ($campaign->target_amount <= 0) {
                return; // Skip if no target set
            }

            $progressPercentage = ($campaign->collected_amount / $campaign->target_amount) * 100;
            $milestones = [50, 75, 100];

            foreach ($milestones as $milestone) {
                if ($progressPercentage >= $milestone) {
                    // Check if milestone notification already sent
                    $cacheKey = "milestone_sent_{$campaign->id}_{$milestone}";
                    
                    if (!Cache::has($cacheKey)) {
                        // Send milestone notification
                        Mail::send(new CampaignMilestone($campaign, $milestone));
                        
                        // Cache that this milestone notification has been sent
                        Cache::put($cacheKey, true, now()->addDays(30));
                        
                        Log::info('Campaign milestone notification sent', [
                            'campaign_id' => $campaign->id,
                            'milestone' => $milestone,
                            'progress_percentage' => round($progressPercentage, 2),
                            'creator_email' => $campaign->user->email
                        ]);
                        
                        // Only send one milestone notification per transaction
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send milestone notification: ' . $e->getMessage(), [
                'campaign_id' => $campaign->id ?? 'unknown'
            ]);
        }
    }
}
