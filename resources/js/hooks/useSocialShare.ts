import { useCallback } from 'react';

export interface ShareData {
    title: string;
    description: string;
    url: string;
    image?: string;
    hashtags?: string[];
}

export const useSocialShare = () => {
    // Track share events
    const trackShare = useCallback(async (platform: string, campaignId: number) => {
        try {
            await fetch('/api/campaigns/share-track', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    platform,
                    campaign_id: campaignId,
                }),
            });
        } catch (error) {
            console.error('Failed to track share:', error);
        }
    }, []);

    // Facebook Share
    const shareToFacebook = useCallback((data: ShareData, campaignId?: number) => {
        const params = new URLSearchParams({
            u: data.url,
            quote: `${data.title} - ${data.description}`,
        });

        const shareUrl = `https://www.facebook.com/sharer/sharer.php?${params.toString()}`;
        
        if (campaignId) trackShare('facebook', campaignId);
        
        window.open(shareUrl, 'facebook-share', 'width=600,height=400,scrollbars=yes,resizable=yes');
    }, [trackShare]);

    // Twitter Share
    const shareToTwitter = useCallback((data: ShareData, campaignId?: number) => {
        const hashtags = data.hashtags?.join(',') || 'donasi,kampanye,bantuan';
        const text = `${data.title}\n\n${data.description}\n\n${data.url}`;
        
        const params = new URLSearchParams({
            text: text.length > 280 ? `${data.title}\n\n${data.url}` : text,
            hashtags,
        });

        const shareUrl = `https://twitter.com/intent/tweet?${params.toString()}`;
        
        if (campaignId) trackShare('twitter', campaignId);
        
        window.open(shareUrl, 'twitter-share', 'width=600,height=400,scrollbars=yes,resizable=yes');
    }, [trackShare]);

    // WhatsApp Share
    const shareToWhatsApp = useCallback((data: ShareData, campaignId?: number) => {
        const message = `*${data.title}*\n\n${data.description}\n\n${data.url}`;
        
        const params = new URLSearchParams({
            text: message,
        });

        const shareUrl = `https://wa.me/?${params.toString()}`;
        
        if (campaignId) trackShare('whatsapp', campaignId);
        
        window.open(shareUrl, '_blank');
    }, [trackShare]);

    // LinkedIn Share
    const shareToLinkedIn = useCallback((data: ShareData, campaignId?: number) => {
        const params = new URLSearchParams({
            url: data.url,
            title: data.title,
            summary: data.description,
        });

        const shareUrl = `https://www.linkedin.com/sharing/share-offsite/?${params.toString()}`;
        
        if (campaignId) trackShare('linkedin', campaignId);
        
        window.open(shareUrl, 'linkedin-share', 'width=600,height=400,scrollbars=yes,resizable=yes');
    }, [trackShare]);

    // Telegram Share
    const shareToTelegram = useCallback((data: ShareData, campaignId?: number) => {
        const text = `${data.title}\n\n${data.description}\n\n${data.url}`;
        
        const params = new URLSearchParams({
            url: data.url,
            text: text,
        });

        const shareUrl = `https://t.me/share/url?${params.toString()}`;
        
        if (campaignId) trackShare('telegram', campaignId);
        
        window.open(shareUrl, '_blank');
    }, [trackShare]);

    // Copy to Clipboard
    const copyToClipboard = useCallback(async (data: ShareData, campaignId?: number) => {
        const text = `${data.title}\n\n${data.description}\n\n${data.url}`;
        
        try {
            await navigator.clipboard.writeText(text);
            
            if (campaignId) trackShare('clipboard', campaignId);
            
            return true;
        } catch (error) {
            console.error('Failed to copy to clipboard:', error);
            return false;
        }
    }, [trackShare]);

    // Native Web Share API (for mobile)
    const nativeShare = useCallback(async (data: ShareData, campaignId?: number) => {
        if (!navigator.share) {
            return false;
        }

        try {
            await navigator.share({
                title: data.title,
                text: data.description,
                url: data.url,
            });

            if (campaignId) trackShare('native', campaignId);
            
            return true;
        } catch (error) {
            console.error('Native share failed:', error);
            return false;
        }
    }, [trackShare]);

    return {
        shareToFacebook,
        shareToTwitter,
        shareToWhatsApp,
        shareToLinkedIn,
        shareToTelegram,
        copyToClipboard,
        nativeShare,
        trackShare,
    };
};
