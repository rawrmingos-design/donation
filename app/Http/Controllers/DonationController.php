<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\PaymentChannel;
use App\Models\Transaction;
use App\Services\TokopayService;
use App\Services\MidtransService;
use App\Services\TransactionCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    protected $tokopayService;
    protected $midtransService;
    protected $transactionCacheService;

    public function __construct(
        TokopayService $tokopayService, 
        MidtransService $midtransService,
        TransactionCacheService $transactionCacheService
    ) {
        $this->tokopayService = $tokopayService;
        $this->midtransService = $midtransService;
        $this->transactionCacheService = $transactionCacheService;
    }

    public function create(Campaign $campaign)
    {
        if ($campaign->status == 'draft') {
            return redirect()->route('campaigns.show', $campaign->slug)->with('error', 'Campaign is draft. please wait for the campaign to be accepted or rejected.');
        }
        // Get payment providers and their channels
        $paymentProviders = \App\Models\PaymentProvider::where('active', true)
            ->with(['paymentChannels' => function($query) {
                $query->where('active', true);
            }])
            ->get()
            ->map(function($provider) {
                return [
                    'id' => $provider->id,
                    'name' => $provider->name,
                    'code' => $provider->code,
                    'channels' => $provider->paymentChannels->map(function($channel) {
                        return [
                            'id' => $channel->id,
                            'code' => $channel->code,
                            'name' => $channel->name,
                            'fee_fixed' => $channel->fee_fixed,
                            'fee_percentage' => $channel->fee_percentage,
                        ];
                    }),
                    'has_channels' => $provider->paymentChannels->count() > 0
                ];
            });
        return Inertia::render('Donations/Create', [
            'campaign' => $campaign,
            'paymentProviders' => $paymentProviders,
        ]);
    }

    public function store(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10000',
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'required|email|max:255',
            'donor_phone' => 'nullable|string|max:50',
            'message' => 'nullable|string|max:500',
            'is_anonymous' => 'boolean',
            'payment_provider' => 'required|exists:payment_providers,id',
            'payment_channel' => 'nullable|exists:payment_channels,id', // Only required for non-Midtrans
        ]);

        // Get payment provider and validate early
        $paymentProvider = \App\Models\PaymentProvider::findOrFail($validated['payment_provider']);
        
        // Validate payment channel for non-Midtrans providers
        if ($paymentProvider->code !== 'midtrans' && !$validated['payment_channel']) {
            return back()->withErrors(['payment_channel' => 'Payment channel is required for this provider.']);
        }

        DB::beginTransaction();
        
        try {
            // Create or find donor
            $donor = Donor::firstOrCreate(
                ['email' => $validated['donor_email']],
                [
                    'user_id' => auth()->id() ?? null,
                    'name' => $validated['donor_name'],
                    'phone' => $validated['donor_phone'],
                    'is_anonymous' => $validated['is_anonymous'] ?? false,
                ]
            );

            // Create donation
            $donation = Donation::create([
                'campaign_id' => $campaign->id,
                'donor_id' => $donor->id,
                'amount' => $validated['amount'],
                'message' => $validated['message'],
                'status' => 'pending',
            ]);

            // Create transaction
            $refId = 'DON-' . time() . '-' . Str::random(6);
            
            $transaction = Transaction::create([
                'donation_id' => $donation->id,
                'provider_id' => $paymentProvider->id,
                'channel_id' => $validated['payment_channel'] ?? null, // Null for Midtrans
                'ref_id' => $refId,
                'total_paid' => $validated['amount'],
                'amount' => $validated['amount'],
                'status' => 'pending',
            ]);
            // Process payment gateway integration
            if ($paymentProvider->code === 'midtrans') {
                // Use Midtrans
                $paymentData = $this->midtransService->createTransaction([
                    'order_id' => $refId,
                    'amount' => $validated['amount'],
                    'customer_name' => $validated['donor_name'],
                    'customer_email' => $validated['donor_email'],
                    'customer_phone' => $validated['donor_phone'] ?? '',
                    'campaign_id' => $campaign->id,
                    'campaign_title' => $campaign->title,
                    'transaction_id' => $transaction->id,
                    'donor_message' => $validated['message'] ?? '',
                    'redirect_url' => route('donations.success', $transaction),
                ]);

                $transaction->update([
                    'payment_url' => $paymentData['redirect_url'],
                    'snap_token' => $paymentData['snap_token'] ?? null,
                    'provider_response' => json_encode($paymentData),
                    'expired_at' => now()->addHours(24),
                ]);


                Log::info('Midtrans Transaction Data:', $paymentData);

                // Commit transaction before returning response
                DB::commit();

                // For Midtrans, always return JSON for frontend handling
                return response()->json([
                    'success' => true,
                    'snap_token' => $paymentData['snap_token'] ?? null,
                    'redirect_url' => $paymentData['redirect_url'] ?? null,
                    'ref_id' => $refId,
                    'transaction_id' => $transaction->id,
                    'message' => 'Transaction created successfully'
                ]);

            } else {
                // Use Duitku/Tokopay - payment channel already validated above
                $paymentChannel = PaymentChannel::findOrFail($validated['payment_channel']);
                
                $paymentData = $this->tokopayService->createOrder([
                    'ref_id' => $refId,
                    'amount' => $validated['amount'],
                    'channel' => $paymentChannel->code,
                    'customer_name' => $validated['donor_name'],
                    'customer_email' => $validated['donor_email'],
                    'customer_phone' => $validated['donor_phone'] ?? '',
                    'redirect_url' => route('donations.success', $transaction),
                    'expired_ts' => now()->addHours(24)->timestamp,
                ]);

                $results = $paymentData['data'];

                $transaction->update([
                    'payment_url' => $results['pay_url'],
                    'instruction' => $results['panduan_pembayaran'] ?? null,
                    'qr_code' => $results['qr_link'] ?? null,
                    'total_received' => $validated['total_dibayar'],
                    'provider_response' => json_encode($paymentData),
                    'status' => $paymentData['status'],
                    'fraud_status' => $paymentData['status'],
                    'expired_at' => now()->addHours(24),
                ]);

                // Commit transaction before redirect
                DB::commit();

                return redirect($paymentData['pay_url'] ?? route('donations.show', $transaction));
            }

        } catch (\Exception $e) {
            // Rollback transaction on any error
            DB::rollback();
            
            Log::error('Donation creation failed: ' . $e->getMessage(), [
                'campaign_id' => $campaign->id,
                'donor_email' => $validated['donor_email'],
                'amount' => $validated['amount'],
                'provider' => $paymentProvider->code ?? 'unknown'
            ]);
            
            return back()->withErrors(['payment' => 'Terjadi kesalahan saat memproses donasi. Silakan coba lagi.']);
        }
    }

    public function show(Transaction $transaction)
    {
        // Try to get from cache first using ref_id
        $cachedData = $this->transactionCacheService->getTransactionByRefId($transaction->ref_id);
        
        if ($cachedData) {
            return Inertia::render('Donations/Show', [
                'transaction' => $cachedData,
            ]);
        }

        // Fallback to database query if cache miss
        $transaction->load([
            'donation.campaign',
            'donation.donor',
            'paymentChannel',
            'paymentProvider'
        ]);

        // Cache the data for future requests
        $this->transactionCacheService->cacheTransactionData($transaction);

        return Inertia::render('Donations/Show', [
            'transaction' => $transaction,
        ]);
    }

    public function success(Transaction $transaction)
    {
        // Try to get from cache first using ref_id
        $cachedData = $this->transactionCacheService->getTransactionByRefId($transaction->ref_id);
        
        if ($cachedData) {
            return Inertia::render('Donations/Success', [
                'transaction' => $cachedData,
            ]);
        }

        // Fallback to database query if cache miss
        $transaction->load([
            'donation.campaign',
            'donation.donor'
        ]);

        // Cache the data for future requests
        $this->transactionCacheService->cacheTransactionData($transaction);

        return Inertia::render('Donations/Success', [
            'transaction' => $transaction,
        ]);
    }

    public function webhook(Request $request)
    {
        // Verify webhook signature
        if (!$this->tokopayService->verifyWebhook($request)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->all();
        $transaction = Transaction::where('ref_id', $data['ref_id'])->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        DB::beginTransaction();
        
        try {
            // Update transaction status
            $transaction->update([
                'status' => $data['status'],
                'total_received' => $data['total_diterima'] ?? null,
                'paid_at' => $data['status'] === 'success' ? now() : null,
            ]);

            // Update campaign collected amount if payment successful
            if ($data['status'] === 'success') {
                $campaign = $transaction->donation->campaign;
                $campaign->increment('collected_amount', $transaction->donation->amount);
            }

            DB::commit();
            
            Log::info('Webhook processed successfully', [
                'ref_id' => $data['ref_id'],
                'status' => $data['status'],
                'transaction_id' => $transaction->id
            ]);

            return response()->json(['status' => 'success']);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Webhook processing failed: ' . $e->getMessage(), [
                'ref_id' => $data['ref_id'] ?? 'unknown',
                'transaction_id' => $transaction->id ?? 'unknown',
                'webhook_data' => $data
            ]);
            
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }
}
