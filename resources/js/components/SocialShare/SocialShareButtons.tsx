import React, { useState } from 'react';
import { useSocialShare, ShareData } from '../../hooks/useSocialShare';

interface SocialShareButtonsProps {
    shareData: ShareData;
    campaignId?: number;
    variant?: 'horizontal' | 'vertical' | 'grid';
    size?: 'sm' | 'md' | 'lg';
    showLabels?: boolean;
    className?: string;
}

const SocialShareButtons: React.FC<SocialShareButtonsProps> = ({
    shareData,
    campaignId,
    variant = 'horizontal',
    size = 'md',
    showLabels = true,
    className = '',
}) => {
    const {
        shareToFacebook,
        shareToTwitter,
        shareToWhatsApp,
        shareToLinkedIn,
        shareToTelegram,
        copyToClipboard,
        nativeShare,
    } = useSocialShare();

    const [copied, setCopied] = useState(false);
    const [showMore, setShowMore] = useState(false);

    const handleCopyToClipboard = async () => {
        const success = await copyToClipboard(shareData, campaignId);
        if (success) {
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        }
    };

    const handleNativeShare = async () => {
        const success = await nativeShare(shareData, campaignId);
        if (!success) {
            // Fallback to copy to clipboard
            handleCopyToClipboard();
        }
    };

    const sizeClasses = {
        sm: 'p-2 text-sm',
        md: 'p-3 text-base',
        lg: 'p-4 text-lg',
    };

    const iconSizes = {
        sm: 'w-4 h-4',
        md: 'w-5 h-5',
        lg: 'w-6 h-6',
    };

    const containerClasses = {
        horizontal: 'flex flex-wrap gap-2',
        vertical: 'flex flex-col gap-2',
        grid: 'grid grid-cols-2 sm:grid-cols-3 gap-2',
    };

    const primaryPlatforms = [
        {
            name: 'Facebook',
            icon: '📘',
            color: 'bg-blue-600 hover:bg-blue-700',
            action: () => shareToFacebook(shareData, campaignId),
        },
        {
            name: 'WhatsApp',
            icon: '💬',
            color: 'bg-green-600 hover:bg-green-700',
            action: () => shareToWhatsApp(shareData, campaignId),
        },
        {
            name: 'Twitter',
            icon: '🐦',
            color: 'bg-blue-400 hover:bg-blue-500',
            action: () => shareToTwitter(shareData, campaignId),
        },
    ];

    const secondaryPlatforms = [
        {
            name: 'LinkedIn',
            icon: '💼',
            color: 'bg-blue-700 hover:bg-blue-800',
            action: () => shareToLinkedIn(shareData, campaignId),
        },
        {
            name: 'Telegram',
            icon: '✈️',
            color: 'bg-blue-500 hover:bg-blue-600',
            action: () => shareToTelegram(shareData, campaignId),
        },
        {
            name: copied ? 'Tersalin!' : 'Salin Link',
            icon: copied ? '✅' : '📋',
            color: copied ? 'bg-green-600' : 'bg-gray-600 hover:bg-gray-700',
            action: handleCopyToClipboard,
        },
    ];

    // Check if native share is available (mobile)
    const isNativeShareAvailable = typeof navigator !== 'undefined' && navigator.share;

    return (
        <div className={`${className}`}>
            {/* Native Share Button (Mobile) */}
            {isNativeShareAvailable && (
                <button
                    onClick={handleNativeShare}
                    className={`w-full mb-3 ${sizeClasses[size]} bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-semibold rounded-lg transition-all duration-200 flex items-center justify-center gap-2`}
                >
                    <span className="text-xl">📤</span>
                    {showLabels && <span>Bagikan</span>}
                </button>
            )}

            {/* Primary Platforms */}
            <div className={containerClasses[variant]}>
                {primaryPlatforms.map((platform) => (
                    <button
                        key={platform.name}
                        onClick={platform.action}
                        className={`${sizeClasses[size]} ${platform.color} text-white font-semibold rounded-lg transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 active:scale-95`}
                        title={`Bagikan ke ${platform.name}`}
                    >
                        <span className={iconSizes[size]}>{platform.icon}</span>
                        {showLabels && <span className="hidden sm:inline">{platform.name}</span>}
                    </button>
                ))}
            </div>

            {/* Show More Button */}
            {!showMore && (
                <button
                    onClick={() => setShowMore(true)}
                    className="mt-3 text-sm text-gray-600 hover:text-gray-800 underline"
                >
                    Lihat opsi lainnya
                </button>
            )}

            {/* Secondary Platforms */}
            {showMore && (
                <div className={`mt-3 ${containerClasses[variant]}`}>
                    {secondaryPlatforms.map((platform) => (
                        <button
                            key={platform.name}
                            onClick={platform.action}
                            className={`${sizeClasses[size]} ${platform.color} text-white font-semibold rounded-lg transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 active:scale-95`}
                            title={platform.name}
                        >
                            <span className={iconSizes[size]}>{platform.icon}</span>
                            {showLabels && <span className="hidden sm:inline">{platform.name}</span>}
                        </button>
                    ))}
                </div>
            )}

            {/* Share Stats (if available) */}
            <div className="mt-4 text-center">
                <p className="text-xs text-gray-500">
                    Bantu sebarkan kampanye ini untuk mencapai lebih banyak orang
                </p>
            </div>
        </div>
    );
};

export default SocialShareButtons;
