import React, { useState, useEffect } from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';
import { Campaign, User, Comment, Donation } from '@/types';
import SocialShareButtons from '@/components/SocialShare/SocialShareButtons';
import ShareAnalytics from '@/components/SocialShare/ShareAnalytics';
import toast from 'react-hot-toast';
import Pagination from '@/components/ui/pagination';
import parse from 'html-react-parser';
import axios from 'axios';

interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: { url: string | null; label: string; active: boolean }[];
    first_page_url: string;
    last_page_url: string;
    next_page_url: string | null;
    prev_page_url: string | null;
    path: string;
}

interface Props {
    campaign: Campaign;
    donations: PaginatedData<Donation>;
    comments: PaginatedData<Comment>;
    activePage: number;
    error: string | null;
    auth: {
        user: User;
    };
}

export default function CampaignShow({ campaign, donations, comments, error, auth }: Props) {
    const [activeTab, setActiveTab] = useState('description');
    const [commentsData, setCommentsData] = useState(comments);

    // Handle comment added
    const handleCommentAdded = (newComment: Comment) => {
        setCommentsData(prev => ({
            ...prev,
            data: [newComment, ...prev.data],
            total: prev.total + 1
        }));
        toast.success('Komentar berhasil ditambahkan!', {
            icon: '💬',
        });
    };

    // Handle comment deleted
    const handleCommentDeleted = (commentId: number) => {
        setCommentsData(prev => ({
            ...prev,
            data: prev.data.filter(comment => comment.id !== commentId),
            total: prev.total - 1
        }));
        toast.success('Komentar berhasil dihapus!', {
            icon: '🗑️',
        });
    };

    // Handle error notifications with toast
    useEffect(() => {
        if (error) {
            toast.error(error, {
                icon: '😔',
                duration: 5000,
            });
        }
    }, [error]);

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(amount);
    };

    const progressPercentage = campaign.target_amount > 0
        ? (campaign.collected_amount / campaign.target_amount) * 100
        : 0;

    const formatDate = (date: string) => {
        return new Date(date).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    };



    const daysLeft = campaign.deadline 
        ? Math.max(0, Math.ceil((new Date(campaign.deadline).getTime() - new Date().getTime()) / (1000 * 60 * 60 * 24)))
        : null;


    return (
        <PublicLayout title={campaign.title} currentPage="campaigns">
            <Head title={campaign.title}>
                {/* Open Graph Meta Tags */}
                <meta property="og:title" content={campaign.title} />
                <meta property="og:description" content={campaign.description?.substring(0, 160) || campaign.title} />
                <meta property="og:image" content={campaign.featured_image ? `/storage/campaigns/${campaign.featured_image}` : '/images/default-campaign.jpg'} />
                <meta property="og:url" content={typeof window !== 'undefined' ? window.location.href : ''} />
                <meta property="og:type" content="website" />
                <meta property="og:site_name" content="DonationPlatform" />
                
                {/* Twitter Card Meta Tags */}
                <meta name="twitter:card" content="summary_large_image" />
                <meta name="twitter:title" content={campaign.title} />
                <meta name="twitter:description" content={campaign.description?.substring(0, 160) || campaign.title} />
                <meta name="twitter:image" content={campaign.featured_image ? `/storage/campaigns/${campaign.featured_image}` : '/images/default-campaign.jpg'} />
                
                {/* Additional Meta Tags */}
                <meta name="description" content={campaign.description?.substring(0, 160) || campaign.title} />
                <meta name="keywords" content={`donasi, kampanye, bantuan, ${campaign.title}, indonesia`} />
                <meta name="author" content={campaign.user.name} />
                
                {/* Schema.org structured data */}
                <script type="application/ld+json">
                    {JSON.stringify({
                        "@context": "https://schema.org",
                        "@type": "DonateAction",
                        "name": campaign.title,
                        "description": campaign.description || campaign.title,
                        "image": campaign.featured_image ? `/storage/campaigns/${campaign.featured_image}` : '/images/default-campaign.jpg',
                        "url": typeof window !== 'undefined' ? window.location.href : '',
                        "target": {
                            "@type": "EntryPoint",
                            "urlTemplate": `${typeof window !== 'undefined' ? window.location.origin : ''}/campaigns/${campaign.slug}/donate`
                        },
                        "recipient": {
                            "@type": "Person",
                            "name": campaign.user.name
                        },
                        "amount": {
                            "@type": "MonetaryAmount",
                            "currency": "IDR",
                            "value": campaign.target_amount
                        }
                    })}
                </script>
            </Head>
            <div className="py-6 sm:py-8">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                        {/* Main Content */}
                        <div className="lg:col-span-2">
                            {campaign.featured_image && (
                                <img
                                    src={`/storage/campaigns/${campaign.featured_image}`}
                                    alt={campaign.title}
                                    className="w-full h-48 sm:h-64 md:h-80 object-cover rounded-lg mb-4 sm:mb-6"
                                />
                            )}

                            <div className="bg-gray-800 rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
                                <div className="flex items-center justify-between mb-3 sm:mb-4 flex-wrap gap-2">
                                    <span className="bg-blue-100 text-black text-xs sm:text-sm px-2 sm:px-3 py-1 rounded">
                                        {campaign.category.name}
                                    </span>
                                    <span className="text-xs sm:text-sm text-gray-500">
                                        oleh {campaign.user.name}
                                    </span>
                                </div>

                                <h1 className="text-xl sm:text-2xl md:text-3xl font-bold mb-3 sm:mb-4 text-white">{campaign.title}</h1>
                                
                                <div className="html-content mb-4 sm:mb-6 max-w-none text-gray-300 text-sm sm:text-base">
                                    {
                                        campaign.short_desc ? parse(campaign.short_desc) : null
                                    }
                                </div>

                                {/* Progress Bar */}
                                <div className="mb-4 sm:mb-6">
                                    <div className="flex justify-between text-xs sm:text-sm mb-2 text-gray-300">
                                        <span>Terkumpul</span>
                                        <span>{progressPercentage.toFixed(1)}%</span>
                                    </div>
                                    <div className="w-full bg-gray-200 rounded-full h-3">
                                        <div
                                            className="bg-green-600 h-3 rounded-full"
                                            style={{ width: `${Math.min(progressPercentage, 100)}%` }}
                                        ></div>
                                    </div>
                                    <div className="flex justify-between mt-2">
                                        <span className="text-lg font-bold text-green-600">
                                            {formatCurrency(campaign.collected_amount)}
                                        </span>
                                        <span className="text-gray-500">
                                            dari {formatCurrency(campaign.target_amount)}
                                        </span>
                                    </div>
                                </div>

                                <div className="flex flex-wrap gap-4 text-sm text-gray-300 mb-6">
                                    <div>
                                        <span className="font-semibold">{campaign.donors_count}</span> donatur
                                    </div>
                                    {daysLeft !== null && (
                                        <div>
                                            <span className="font-semibold">{daysLeft}</span> hari lagi
                                        </div>
                                    )}
                                </div>

                                {/* Tabs */}
                                <div className="border-b border-gray-200 mb-6">
                                    <nav className="-mb-px flex space-x-8">
                                        {[
                                            { key: 'description', label: 'Deskripsi' },
                                            { key: 'donations', label: `Donasi (${donations.total})` },
                                            { key: 'comments', label: `Komentar (${comments.total})` },
                                        ].map((tab) => (
                                            <button
                                                key={tab.key}
                                                onClick={() => setActiveTab(tab.key)}
                                                className={`py-2 px-1 border-b-2 font-medium text-sm ${
                                                    activeTab === tab.key
                                                        ? 'border-blue-500 text-blue-600'
                                                        : 'border-transparent text-gray-300 hover:text-gray-700 hover:border-gray-300'
                                                }`}
                                            >
                                                {tab.label}
                                            </button>
                                        ))}
                                    </nav>
                                </div>

                                {/* Tab Content */}
                                <div>
                                    {activeTab === 'description' && (
                                        <div className="html-content max-w-none">
                                            {campaign.description ? parse(campaign.description) : null}
                                        </div>
                                    )}


                                    {activeTab === 'donations' && (
                                        <div className="space-y-4">
                                            {donations.data.map((donation) => (
                                                <div key={donation.id} className="bg-gray-800 border border-gray-500 p-4 rounded-lg">
                                                    <div className="flex justify-between items-start">
                                                        <div>
                                                            <p className="font-semibold">
                                                                {donation.donor.is_anonymous ? 'Anonim' : donation.donor.name}
                                                            </p>
                                                            <p className="text-sm text-gray-300">
                                                                {formatDate(donation.created_at)}
                                                            </p>
                                                        </div>
                                                        <span className="font-bold text-green-600">
                                                            {formatCurrency(donation.amount)}
                                                        </span>
                                                    </div>
                                                    {donation.message && (
                                                        <p className="mt-2 text-gray-300 italic">"{donation.message}"</p>
                                                    )}
                                                </div>
                                            ))}
                                            {donations.data.length === 0 && (
                                                <p className="text-gray-500">Belum ada donasi untuk kampanye ini.</p>
                                            )}
                                            
                                            {/* Donations Pagination */}
                                            {donations.last_page > 1 && (
                                                <div className="mt-6">
                                                    <Pagination
                                                        data={{
                                                            ...donations,
                                                            first_page_url: `/campaigns/${campaign.slug}?donations_page=1`,
                                                            last_page_url: `/campaigns/${campaign.slug}?donations_page=${donations.last_page}`,
                                                            next_page_url: donations.current_page < donations.last_page ? `/campaigns/${campaign.slug}?donations_page=${donations.current_page + 1}` : null,
                                                            prev_page_url: donations.current_page > 1 ? `/campaigns/${campaign.slug}?donations_page=${donations.current_page - 1}` : null,
                                                            path: `/campaigns/${campaign.slug}`
                                                        }}
                                                        preserveScroll={true}
                                                    />
                                                </div>
                                            )}
                                        </div>
                                    )}

                                    {activeTab === 'comments' && (
                                        <div className="space-y-6">
                                            {/* Comment Form */}
                                            <CommentForm 
                                                campaignSlug={campaign.slug}
                                                onCommentAdded={handleCommentAdded}
                                            />
                                            
                                            {/* Comments List */}
                                            <div className="space-y-4">
                                                {commentsData.data.map((comment) => (
                                                    <CommentItem
                                                        key={comment.id}
                                                        comment={comment}
                                                        onCommentDeleted={handleCommentDeleted}
                                                        currentUser={auth?.user}
                                                    />
                                                ))}
                                                {commentsData.data.length === 0 && (
                                                    <div className="text-center py-8">
                                                        <div className="text-gray-500 mb-2">💬</div>
                                                        <p className="text-gray-500">Belum ada komentar untuk kampanye ini.</p>
                                                        <p className="text-sm text-gray-600 mt-1">Jadilah yang pertama memberikan komentar!</p>
                                                    </div>
                                                )}
                                            </div>
                                            
                                            {/* Comments Pagination */}
                                            {commentsData.last_page > 1 && (
                                                <div className="mt-6">
                                                    <Pagination
                                                        data={{
                                                            ...commentsData,
                                                            first_page_url: `/campaigns/${campaign.slug}?comments_page=1`,
                                                            last_page_url: `/campaigns/${campaign.slug}?comments_page=${commentsData.last_page}`,
                                                            next_page_url: commentsData.current_page < commentsData.last_page ? `/campaigns/${campaign.slug}?comments_page=${commentsData.current_page + 1}` : null,
                                                            prev_page_url: commentsData.current_page > 1 ? `/campaigns/${campaign.slug}?comments_page=${commentsData.current_page - 1}` : null,
                                                            path: `/campaigns/${campaign.slug}`
                                                        }}
                                                        preserveScroll={true}
                                                    />
                                                </div>
                                            )}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Sidebar */}
                        {campaign.status === 'active' && (
                        <div className="lg:col-span-1">
                            <div className="bg-gray-800 rounded-lg shadow-md p-4 sm:p-6 sticky top-4 sm:top-8 mx-4 lg:mx-0">
                                <Link
                                    href={`/campaigns/${campaign.slug}/donate`}
                                    className="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 sm:py-3 px-4 sm:px-6 rounded-lg text-center block mb-3 sm:mb-4 text-sm sm:text-base"
                                    disabled={campaign.status !== 'active'}
                                >
                                    Donasi Sekarang
                                </Link>

                                <div className="text-center text-xs sm:text-sm text-gray-200 mb-3 sm:mb-4">
                                    Bagikan kampanye ini:
                                </div>

                                <SocialShareButtons
                                    shareData={{
                                        title: campaign.title,
                                        description: campaign.description ? campaign.description.substring(0, 200) + '...' : campaign.title,
                                        url: typeof window !== 'undefined' ? window.location.href : '',
                                        image: campaign.featured_image ? `/storage/campaigns/${campaign.featured_image}` : '/images/default-campaign.jpg',
                                        hashtags: ['donasi', 'kampanye', 'bantuan', 'indonesia'],
                                    }}
                                    campaignId={campaign.id}
                                    variant="horizontal"
                                    size="sm"
                                    showLabels={false}
                                />

                                {/* Share Analytics */}
                                <ShareAnalytics 
                                    campaignId={campaign.id}
                                    className="mt-4 pt-4 border-t border-gray-600"
                                />
                            </div>
                        </div>
                        )}
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}

// Comment Form Component
interface CommentFormProps {
    campaignSlug: string;
    onCommentAdded: (comment: Comment) => void;
}

function CommentForm({ campaignSlug, onCommentAdded }: CommentFormProps) {
    const [content, setContent] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        
        if (!content.trim()) {
            toast.error('Komentar tidak boleh kosong');
            return;
        }

        setIsSubmitting(true);

        try {
            const response = await axios.post(`/campaigns/${campaignSlug}/comments`, {
                content: content.trim(),
                is_public: true
            });

            if (response.data.success) {
                onCommentAdded(response.data.comment);
                setContent('');
                toast.success(response.data.message);
            }
        } catch (error: unknown) {
            const axiosError = error as { response?: { status?: number; data?: { message?: string } } };
            if (axiosError.response?.status === 401) {
                toast.error('Anda harus login untuk memberikan komentar');
            } else if (axiosError.response?.data?.message) {
                toast.error(axiosError.response.data.message);
            } else {
                toast.error('Terjadi kesalahan saat mengirim komentar');
            }
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <div className="bg-gray-800 border border-gray-500 rounded-lg p-4">
            <h3 className="text-lg font-semibold mb-4 text-white">💬 Berikan Komentar</h3>
            <form onSubmit={handleSubmit} className="space-y-4">
                <div>
                    <textarea
                        value={content}
                        onChange={(e) => setContent(e.target.value)}
                        placeholder="Tulis komentar Anda di sini..."
                        className="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                        rows={4}
                        maxLength={1000}
                        disabled={isSubmitting}
                    />
                    <div className="text-right text-sm text-gray-400 mt-1">
                        {content.length}/1000
                    </div>
                </div>
                <div className="flex justify-between items-center">
                    <p className="text-sm text-gray-400">
                        Komentar akan ditampilkan secara publik
                    </p>
                    <button
                        type="submit"
                        disabled={isSubmitting || !content.trim()}
                        className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        {isSubmitting ? 'Mengirim...' : 'Kirim Komentar'}
                    </button>
                </div>
            </form>
        </div>
    );
}

// Comment Item Component
interface CommentItemProps {
    comment: Comment;
    onCommentDeleted: (commentId: number) => void;
    currentUser?: User;
}

function CommentItem({ comment, onCommentDeleted, currentUser }: CommentItemProps) {
    const [isDeleting, setIsDeleting] = useState(false);

    const handleDelete = async () => {
        // Show confirmation toast with action buttons
        toast((t) => (
            <div className="flex flex-col space-y-2">
                <span className="font-medium">Hapus komentar ini?</span>
                <span className="text-sm text-gray-600">Tindakan ini tidak dapat dibatalkan</span>
                <div className="flex space-x-2 mt-2">
                    <button
                        onClick={() => {
                            toast.dismiss(t.id);
                            performDelete();
                        }}
                        className="px-3 py-1 bg-red-500 text-white text-sm rounded hover:bg-red-600 transition-colors"
                    >
                        Ya, Hapus
                    </button>
                    <button
                        onClick={() => toast.dismiss(t.id)}
                        className="px-3 py-1 bg-gray-300 text-gray-700 text-sm rounded hover:bg-gray-400 transition-colors"
                    >
                        Batal
                    </button>
                </div>
            </div>
        ), {
            duration: 10000,
            position: 'top-center',
        });
    };

    const performDelete = async () => {
        setIsDeleting(true);

        try {
            const response = await axios.delete(`/comments/${comment.id}`);
            
            if (response.data.success) {
                onCommentDeleted(comment.id);
                toast.success(response.data.message, {
                    icon: '🗑️',
                });
            }
        } catch (error: unknown) {
            const axiosError = error as { response?: { data?: { message?: string } } };
            if (axiosError.response?.data?.message) {
                toast.error(axiosError.response.data.message);
            } else {
                toast.error('Terjadi kesalahan saat menghapus komentar');
            }
        } finally {
            setIsDeleting(false);
        }
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    return (
        <div className="bg-gray-800 border border-gray-500 p-4 rounded-lg">
            <div className="flex justify-between items-start mb-3">
                <div className="flex items-center space-x-3">
                    <div className="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                        {comment.user.name.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <p className="font-semibold text-white">{comment.user.name}</p>
                        <p className="text-sm text-gray-400">
                            {formatDate(comment.created_at)}
                        </p>
                    </div>
                </div>
                {/* Show delete button for comment owner or admin */}
                {(currentUser && (currentUser.id === comment.user.id || currentUser.role === 'admin')) && (
                    <button
                        onClick={handleDelete}
                        disabled={isDeleting}
                        className="text-gray-400 cursor-pointer hover:text-red-400 transition-colors p-1"
                        title="Hapus komentar"
                    >
                        {isDeleting ? '⏳' : '🗑️'}
                    </button>
                )}
            </div>
            <div className="text-gray-300 leading-relaxed">
                {comment.content}
            </div>
        </div>
    );
}
