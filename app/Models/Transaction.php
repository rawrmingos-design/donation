<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'channel_id',
        'provider_id',
        'ref_id',
        'snap_token',
        'payment_url',
        'pay_url',
        'va_number',
        'qr_code',
        'instruction',
        'provider_response',
        'total_paid',
        'amount',
        'total_received',
        'fee_amount',
        'payment_type',
        'fraud_status',
        'status',
        'paid_at',
        'settlement_time',
        'expired_at',
    ];

    protected $casts = [
        'total_paid' => 'integer',
        'amount' => 'integer',
        'total_received' => 'integer',
        'fee_amount' => 'integer',
        'provider_response' => 'array',
        'paid_at' => 'datetime',
        'settlement_time' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function paymentChannel(): BelongsTo
    {
        return $this->belongsTo(PaymentChannel::class, 'channel_id');
    }

    public function paymentProvider(): BelongsTo
    {
        return $this->belongsTo(PaymentProvider::class, 'provider_id');
    }
}
