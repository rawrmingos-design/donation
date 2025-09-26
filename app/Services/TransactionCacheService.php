<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TransactionCacheService
{
    /**
     * Get transaction data from cache or database
     */
    public function getTransactionByRefId(string $refId): ?array
    {
        $cacheKey = "transaction:{$refId}";
        
        // Try to get from cache first
        $cachedData = Cache::get($cacheKey);
        
        if ($cachedData) {
            Log::info('Transaction data retrieved from cache', [
                'ref_id' => $refId,
                'cache_key' => $cacheKey
            ]);
            return $cachedData;
        }

        // If not in cache, get from database and cache it
        $transaction = Transaction::where('ref_id', $refId)
            ->with([
                'donation.campaign',
                'donation.donor',
                'paymentChannel',
                'paymentProvider'
            ])
            ->first();

        if (!$transaction) {
            return null;
        }

        // Cache the data
        $this->cacheTransactionData($transaction);
        
        Log::info('Transaction data retrieved from database and cached', [
            'ref_id' => $refId,
            'cache_key' => $cacheKey
        ]);

        return $this->formatTransactionData($transaction);
    }

    /**
     * Cache transaction data
     */
    public function cacheTransactionData(Transaction $transaction): void
    {
        $cacheKey = "transaction:{$transaction->ref_id}";
        $cacheData = $this->formatTransactionData($transaction);
        
        // Cache for 24 hours
        Cache::put($cacheKey, $cacheData, now()->addHours(24));
    }

    /**
     * Clear transaction cache
     */
    public function clearTransactionCache(string $refId): void
    {
        $cacheKey = "transaction:{$refId}";
        Cache::forget($cacheKey);
        
        Log::info('Transaction cache cleared manually', [
            'ref_id' => $refId,
            'cache_key' => $cacheKey
        ]);
    }

    /**
     * Format transaction data for caching
     */
    private function formatTransactionData(Transaction $transaction): array
    {
        return [
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
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        // This would require Redis commands to get actual stats
        // For now, return basic info
        return [
            'cache_driver' => config('cache.default'),
            'redis_connection' => config('database.redis.default.host') ?? 'not configured'
        ];
    }
}
