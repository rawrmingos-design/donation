<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'short_desc',
        'description',
        'featured_image',
        'target_amount',
        'collected_amount',
        'currency',
        'goal_type',
        'deadline',
        'status',
        'allow_anonymous',
    ];

    protected $casts = [
        'deadline' => 'date',
        'target_amount' => 'integer',
        'collected_amount' => 'integer',
        'allow_anonymous' => 'boolean',
    ];

    // Accessor for Filament FileUpload component
    public function getFeaturedImageAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        // If the value already contains 'campaigns/', remove it for Filament
        if (str_starts_with($value, 'campaigns/')) {
            return str_replace('campaigns/', '', $value);
        }
        
        return $value;
    }

    // Mutator to ensure the path is stored with 'campaigns/' prefix
    public function setFeaturedImageAttribute($value)
    {
        if (!$value) {
            $this->attributes['featured_image'] = null;
            return;
        }
        
        // If the value doesn't start with 'campaigns/', add it
        if (!str_starts_with($value, 'campaigns/')) {
            $this->attributes['featured_image'] = 'campaigns/' . $value;
        } else {
            $this->attributes['featured_image'] = $value;
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }


    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(CampaignShare::class);
    }

    // Helper methods
    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount == 0) {
            return 0;
        }
        return min(100, ($this->collected_amount / $this->target_amount) * 100);
    }

    public function getDonorsCountAttribute(): int
    {
        return $this->donations()->distinct('donor_id')->count();
    }

    /**
     * Check if campaign has reached its target
     */
    public function hasReachedTarget(): bool
    {
        return $this->collected_amount >= $this->target_amount;
    }

    /**
     * Get remaining amount to reach target
     */
    public function getRemainingAmountAttribute(): int
    {
        return max(0, $this->target_amount - $this->collected_amount);
    }

    /**
     * Check if campaign is still active and accepting donations
     */
    public function isAcceptingDonations(): bool
    {
        return $this->status === 'active' && 
               (!$this->deadline || $this->deadline->isFuture()) &&
               !$this->hasReachedTarget();
    }
}
