<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Donation;
use App\Models\Campaign;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MidtransWebhookController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function handle(Request $request)
    {
        try {
            // Handle Midtrans notification
            $notification = $this->midtransService->handleNotification();

            Log::info('Notification received', $notification);
            Log::info('Request headers', $request->headers->all());
            
            // Find transaction with all related data in single query
            $transaction = Transaction::with([
                'donation.campaign',
                'donation.donor'
            ])->where('ref_id', $notification['order_id'])->first();

            if (!$transaction) {
                Log::warning('Transaction not found for Midtrans notification', [
                    'order_id' => $notification['order_id']
                ]);
                return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
            }

            $donation = $transaction->donation;
            $campaign = $donation->campaign;

            // Get payment status from notification
            $paymentStatus = $this->midtransService->getPaymentStatus($notification);
            
            DB::beginTransaction();
            
            try {
                Log::info('Before transaction update', [
                    'transaction_id' => $transaction->id,
                    'current_status' => $transaction->status,
                    'new_status' => $paymentStatus,
                    'donation_id' => $donation->id,
                    'donation_status' => $donation->status,
                    'campaign_id' => $campaign->id
                ]);

                // Update transaction status
                $transaction->update([
                    'status' => $paymentStatus,
                    'provider_response' => json_encode($notification),
                    'payment_type' => $notification['payment_type'] ?? null,
                    'fraud_status' => $notification['fraud_status'] ?? null,
                    'paid_at' => in_array($paymentStatus, ['completed', 'settlement']) ? now() : null,
                    'settlement_time' => $notification['settlement_time'] ?? null,
                ]);

                Log::info('After transaction update', [
                    'transaction_id' => $transaction->id,
                    'updated_status' => $transaction->status,
                    'donation_status' => $donation->fresh()->status
                ]);

                DB::commit();

                Log::info('Midtrans webhook processed successfully', [
                    'transaction_id' => $transaction->id,
                    'order_id' => $notification['order_id'],
                    'status' => $paymentStatus,
                    'donation_status' => $donation->status,
                ]);

                return response()->json(['status' => 'success']);
                
            } catch (\Exception $e) {
                DB::rollback();
                
                Log::error('Database transaction failed in Midtrans webhook: ' . $e->getMessage(), [
                    'transaction_id' => $transaction->id ?? 'unknown',
                    'order_id' => $notification['order_id'] ?? 'unknown',
                    'notification' => $notification,
                    'donation' => $donation,
                ]);
                
                throw $e; // Re-throw to be caught by outer catch block
            }

        } catch (\Exception $e) {
            Log::error('Midtrans webhook error: ' . $e->getMessage(), [
                'request_body' => $request->getContent(),
                'headers' => $request->headers->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'error', 'message' => 'Webhook processing failed'], 500);
        }
    }
}
