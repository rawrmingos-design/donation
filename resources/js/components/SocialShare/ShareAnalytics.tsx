import React, { useState, useEffect, useCallback } from 'react';

interface ShareStats {
    total_shares: number;
    shares_by_platform: Record<string, number>;
    recent_shares: number;
    daily_trend: Record<string, number>;
    popular_platforms: string[];
}

interface ShareAnalyticsProps {
    campaignId: number;
    className?: string;
}

const ShareAnalytics: React.FC<ShareAnalyticsProps> = ({ campaignId, className = '' }) => {
    const [stats, setStats] = useState<ShareStats | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const fetchShareStats = useCallback(async () => {
        try {
            setLoading(true);
            const response = await fetch(`/api/campaigns/${campaignId}/share-stats`);
            
            if (!response.ok) {
                throw new Error('Failed to fetch share stats');
            }

            const data = await response.json();
            setStats(data);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'An error occurred');
        } finally {
            setLoading(false);
        }
    }, [campaignId]);

    useEffect(() => {
        fetchShareStats();
    }, [fetchShareStats]);

    const getPlatformIcon = (platform: string) => {
        const icons: Record<string, string> = {
            facebook: '📘',
            twitter: '🐦',
            whatsapp: '💬',
            linkedin: '💼',
            telegram: '✈️',
            clipboard: '📋',
            native: '📤',
        };
        return icons[platform] || '🔗';
    };

    const getPlatformName = (platform: string) => {
        const names: Record<string, string> = {
            facebook: 'Facebook',
            twitter: 'Twitter',
            whatsapp: 'WhatsApp',
            linkedin: 'LinkedIn',
            telegram: 'Telegram',
            clipboard: 'Copy Link',
            native: 'Native Share',
        };
        return names[platform] || platform;
    };

    if (loading) {
        return (
            <div className={`${className} animate-pulse`}>
                <div className="bg-gray-200 h-4 rounded mb-2"></div>
                <div className="bg-gray-200 h-4 rounded w-3/4"></div>
            </div>
        );
    }

    if (error || !stats) {
        return (
            <div className={`${className} text-gray-500 text-sm`}>
                <p>📊 Share analytics tidak tersedia</p>
            </div>
        );
    }

    return (
        <div className={`${className}`}>
            {/* Total Shares */}
            <div className="mb-4">
                <div className="flex items-center gap-2 text-sm text-gray-600 mb-2">
                    <span className="text-lg">📊</span>
                    <span>Total dibagikan: <strong>{stats.total_shares}</strong> kali</span>
                </div>
                
                {stats.recent_shares > 0 && (
                    <div className="text-xs text-green-600">
                        +{stats.recent_shares} dalam 7 hari terakhir
                    </div>
                )}
            </div>

            {/* Platform Breakdown */}
            {Object.keys(stats.shares_by_platform).length > 0 && (
                <div className="mb-4">
                    <h4 className="text-sm font-medium text-gray-700 mb-2">Platform populer:</h4>
                    <div className="space-y-1">
                        {Object.entries(stats.shares_by_platform)
                            .sort(([,a], [,b]) => b - a)
                            .slice(0, 3)
                            .map(([platform, count]) => (
                                <div key={platform} className="flex items-center justify-between text-xs">
                                    <div className="flex items-center gap-1">
                                        <span>{getPlatformIcon(platform)}</span>
                                        <span>{getPlatformName(platform)}</span>
                                    </div>
                                    <span className="font-medium">{count}</span>
                                </div>
                            ))
                        }
                    </div>
                </div>
            )}

            {/* Engagement Message */}
            <div className="text-xs text-gray-500 italic">
                {stats.total_shares === 0 
                    ? "Jadilah yang pertama membagikan kampanye ini!" 
                    : "Terima kasih telah membantu menyebarkan kampanye ini! 🙏"
                }
            </div>
        </div>
    );
};

export default ShareAnalytics;
