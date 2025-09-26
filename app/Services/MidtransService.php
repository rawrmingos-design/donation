<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = config('services.midtrans.is_sanitized', true);
        Config::$is3ds = config('services.midtrans.is_3ds', true);
    }

    public function createTransaction(array $data)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $data['order_id'],
                'gross_amount' => (int) $data['amount'],
            ],
            'customer_details' => [
                'first_name' => $data['customer_name'],
                'email' => $data['customer_email'],
                'phone' => $data['customer_phone'] ?? '',
            ],
            'item_details' => [
                [
                    'id' => 'donation-' . $data['campaign_id'],
                    'price' => (int) $data['amount'],
                    'quantity' => 1,
                    'name' => 'Donasi untuk ' . $data['campaign_title'],
                    'category' => 'donation'
                ]
            ],
            'callbacks' => [
                'finish' => $data['redirect_url'] ?? route('donations.success'),
            ],
            'expiry' => [
                'start_time' => date('Y-m-d H:i:s O'),
                'unit' => 'hours',
                'duration' => 24
            ]
        ];

        // Add custom fields for donation tracking
        $params['campaign_id'] = $data['campaign_id'];
        $params['transaction_id'] = $data['transaction_id'] ?? null;
        $params['donor_message'] = $data['donor_message'] ?? '';

        try {
            Log::info('Midtrans Transaction Params:', $params);
            
            $snapToken = Snap::getSnapToken($params);
            $redirectUrl = Snap::createTransaction($params)->redirect_url;

            return [
                'snap_token' => $snapToken,
                'redirect_url' => $redirectUrl,
                'order_id' => $data['order_id']
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans Transaction Error: ' . $e->getMessage());
            throw new \Exception('Failed to create Midtrans transaction: ' . $e->getMessage());
        }
    }

    public function getTransactionStatus(string $orderId)
    {
        try {
            $status = Transaction::status($orderId);
            Log::info('Midtrans Transaction Status:', (array) $status);
            return $status;
        } catch (\Exception $e) {
            Log::error('Midtrans Status Check Error: ' . $e->getMessage());
            throw new \Exception('Failed to get transaction status: ' . $e->getMessage());
        }
    }

    public function handleNotification()
    {
        try {
            $notification = new Notification();
            
            Log::info('Midtrans Notification Received:', [
                'order_id' => $notification->order_id,
                'transaction_status' => $notification->transaction_status,
                'fraud_status' => $notification->fraud_status ?? null,
                'payment_type' => $notification->payment_type,
                'gross_amount' => $notification->gross_amount
            ]);

            return [
                'order_id' => $notification->order_id,
                'transaction_status' => $notification->transaction_status,
                'fraud_status' => $notification->fraud_status ?? null,
                'payment_type' => $notification->payment_type,
                'gross_amount' => $notification->gross_amount,
                'transaction_time' => $notification->transaction_time ?? null,
                'settlement_time' => $notification->settlement_time ?? null,
                'signature_key' => $notification->signature_key ?? null,
                'status_code' => $notification->status_code ?? null,
                'status_message' => $notification->status_message ?? null,
                'raw_notification' => $notification
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            throw new \Exception('Failed to process notification: ' . $e->getMessage());
        }
    }

    public function getPaymentStatus(array $notification)
    {
        $transactionStatus = $notification['transaction_status'];
        $fraudStatus = $notification['fraud_status'] ?? null;

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                return 'pending'; // Transaction is challenged, need manual review
            } else if ($fraudStatus == 'accept') {
                return 'completed'; // Transaction is successful
            }
        } else if ($transactionStatus == 'settlement') {
            return 'completed'; // Transaction is successful
        } else if ($transactionStatus == 'pending') {
            return 'pending'; // Transaction is pending
        } else if ($transactionStatus == 'deny') {
            return 'failed'; // Transaction is denied
        } else if ($transactionStatus == 'expire') {
            return 'expired'; // Transaction is expired
        } else if ($transactionStatus == 'cancel') {
            return 'cancelled'; // Transaction is cancelled
        }

        return 'pending'; // Default status
    }

    public function cancelTransaction(string $orderId)
    {
        try {
            $result = Transaction::cancel($orderId);
            Log::info('Midtrans Transaction Cancelled:', (array) $result);
            return $result;
        } catch (\Exception $e) {
            Log::error('Midtrans Cancel Error: ' . $e->getMessage());
            throw new \Exception('Failed to cancel transaction: ' . $e->getMessage());
        }
    }

    public function refundTransaction(string $orderId, int $amount = null, string $reason = null)
    {
        try {
            $params = [];
            if ($amount) {
                $params['amount'] = $amount;
            }
            if ($reason) {
                $params['reason'] = $reason;
            }

            $result = Transaction::refund($orderId, $params);
            Log::info('Midtrans Transaction Refunded:', (array) $result);
            return $result;
        } catch (\Exception $e) {
            Log::error('Midtrans Refund Error: ' . $e->getMessage());
            throw new \Exception('Failed to refund transaction: ' . $e->getMessage());
        }
    }
}
