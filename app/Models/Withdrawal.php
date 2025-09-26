<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\NotificationService;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'amount',
        'fee_amount',
        'net_amount',
        'method',
        'account_info',
        'status',
        'notes',
        'reference_number',
        'approved_by',
        'requested_at',
        'approved_at',
        'processed_at',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'fee_amount' => 'integer',
        'net_amount' => 'integer',
        'account_info' => 'array',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    // Withdrawal methods
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_E_WALLET = 'e_wallet';

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }

    // Calculate platform fee (2.5% + Rp 2,500)
    public static function calculateFee(int $amount): int
    {
        return (int) ($amount * 0.025) + 2500; // 2.5% + Rp 2,500 in rupiah
    }

    // Format amount to Rupiah
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getFormattedFeeAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->fee_amount, 0, ',', '.');
    }

    public function getFormattedNetAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->net_amount, 0, ',', '.');
    }

    // Generate reference number for completed withdrawals
    public static function generateReferenceNumber(): string
    {
        $prefix = 'WD';
        $date = now()->format('ymd'); // YYMMDD
        $random = strtoupper(substr(uniqid(), -6)); // 6 char unique
        
        return $prefix . $date . $random; // Format: WD241024ABC123
    }

    // Auto-generate reference when marking as completed
    public function markAsCompleted(?string $referenceNumber = null): void
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'reference_number' => $referenceNumber ?: self::generateReferenceNumber(),
        ]);
        
        // Send notification if status changed
        if ($oldStatus !== self::STATUS_COMPLETED) {
            app(NotificationService::class)->sendWithdrawalNotification($this, 'withdrawal_completed');
        }
    }

    // Update status with notification
    public function updateStatus(string $status, ?string $notes = null): void
    {
        $oldStatus = $this->status;
        
        $updateData = ['status' => $status];
        
        // Set timestamps based on status
        switch ($status) {
            case self::STATUS_APPROVED:
                $updateData['approved_at'] = now();
                $updateData['approved_by'] = auth()->id();
                break;
            case self::STATUS_PROCESSING:
                $updateData['processed_at'] = now();
                break;
            case self::STATUS_COMPLETED:
                $updateData['completed_at'] = now();
                if (!$this->reference_number) {
                    $updateData['reference_number'] = self::generateReferenceNumber();
                }
                break;
        }
        
        if ($notes) {
            $updateData['notes'] = $notes;
        }
        
        $this->update($updateData);
        
        // Send notification if status changed
        if ($oldStatus !== $status) {
            $notificationType = match ($status) {
                self::STATUS_APPROVED => 'withdrawal_approved',
                self::STATUS_REJECTED => 'withdrawal_rejected',
                self::STATUS_PROCESSING => 'withdrawal_processing',
                self::STATUS_COMPLETED => 'withdrawal_completed',
                self::STATUS_CANCELLED => 'withdrawal_cancelled',
                default => null,
            };
            
            if ($notificationType) {
                app(NotificationService::class)->sendWithdrawalNotification($this, $notificationType);
            }
        }
    }
}
