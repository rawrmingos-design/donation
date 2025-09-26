<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'platform',
        'ip_address',
        'user_agent',
        'referrer',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the campaign that was shared.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get share count by platform for a campaign
     */
    public static function getShareCountByPlatform(int $campaignId): array
    {
        return self::where('campaign_id', $campaignId)
            ->selectRaw('platform, count(*) as count')
            ->groupBy('platform')
            ->pluck('count', 'platform')
            ->toArray();
    }

    /**
     * Get total share count for a campaign
     */
    public static function getTotalShareCount(int $campaignId): int
    {
        return self::where('campaign_id', $campaignId)->count();
    }

    /**
     * Get popular platforms for sharing
     */
    public static function getPopularPlatforms(int $limit = 5): array
    {
        return self::selectRaw('platform, count(*) as count')
            ->groupBy('platform')
            ->orderByDesc('count')
            ->limit($limit)
            ->pluck('count', 'platform')
            ->toArray();
    }
}
