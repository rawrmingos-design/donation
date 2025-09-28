import { Head, Link, router } from '@inertiajs/react';
import { Campaign, Category } from '@/types';
import { useState, useEffect } from 'react';
import PublicLayout from '@/layouts/PublicLayout';
import Pagination from '@/components/ui/pagination';

interface Props {
    campaigns: {
        data: Campaign[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
        links: {
            first: string;
            last: string;
            prev: string | null;
            next: string | null;
        };
    };
    categories: Category[];
    filters: {
        search?: string;
        category?: string;
        status?: string;
    };
}

export default function CampaignsExplore({ campaigns, categories, filters }: Props) {
    const [searchQuery, setSearchQuery] = useState(filters.search || '');
    const [selectedCategory, setSelectedCategory] = useState(filters.category || 'all');
    const [selectedStatus, setSelectedStatus] = useState(filters.status || 'all');
    const [isInitialized, setIsInitialized] = useState(false);

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(amount);
    };

    const calculateProgress = (collected: number, target: number) => {
        return target > 0 ? (collected / target) * 100 : 0;
    };

    // Initialize component
    useEffect(() => {
        setIsInitialized(true);
    }, []);

    // Handle filter changes with debounce for search
    useEffect(() => {
        if (!isInitialized) return; // Don't trigger on initial mount
        
        const timeoutId = setTimeout(() => {
            handleFilterChange();
        }, 500);
        return () => clearTimeout(timeoutId);
    }, [searchQuery, isInitialized]);

    const handleFilterChange = () => {
        const params: any = {};
        if (searchQuery) params.search = searchQuery;
        if (selectedCategory !== 'all') params.category = selectedCategory;
        if (selectedStatus !== 'all') params.status = selectedStatus;
        // Reset to page 1 when filters change
        params.page = 1;
        
        router.get('/campaigns', params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleCategoryChange = (category: string) => {
        setSelectedCategory(category);
        const params: any = {};
        if (searchQuery) params.search = searchQuery;
        if (category !== 'all') params.category = category;
        if (selectedStatus !== 'all') params.status = selectedStatus;
        // Reset to page 1 when category changes
        params.page = 1;
        
        router.get('/campaigns', params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleStatusChange = (status: string) => {
        setSelectedStatus(status);
        const params: any = {};
        if (searchQuery) params.search = searchQuery;
        if (selectedCategory !== 'all') params.category = selectedCategory;
        if (status !== 'all') params.status = status;
        // Reset to page 1 when status changes
        params.page = 1;
        
        router.get('/campaigns', params, {
            preserveState: true,
            preserveScroll: true,
        });
    };


    return (
        <PublicLayout title='Jelajahi Kampanye Donasi' currentPage='campaigns'>
            <div className="py-6 sm:py-8">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Hero Section */}
                    <div className="text-center mb-8 sm:mb-12">
                        <h1 className="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-3 sm:mb-4 px-4">
                            Jelajahi Kampanye Donasi
                        </h1>
                        <p className="text-lg sm:text-xl text-gray-300 max-w-3xl mx-auto px-4 sm:px-0">
                            Temukan berbagai kampanye donasi yang membutuhkan bantuan Anda. 
                            Setiap kontribusi, sekecil apapun, dapat membuat perbedaan besar.
                        </p>
                    </div>

                    {/* Filters */}
                    <div className="bg-gray-800 rounded-lg p-4 sm:p-6 mb-6 sm:mb-8 mx-4 sm:mx-0">
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                            {/* Search */}
                            <div className="sm:col-span-2 lg:col-span-1">
                                <label className="block text-sm font-medium text-gray-300 mb-2">
                                    Cari Kampanye
                                </label>
                                <input
                                    type="text"
                                    placeholder="Masukkan kata kunci..."
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                    className="w-full px-3 sm:px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                                />
                            </div>

                            {/* Category Filter */}
                            <div>
                                <label className="block text-sm font-medium text-gray-300 mb-2">
                                    Kategori
                                </label>
                                <select
                                    value={selectedCategory}
                                    onChange={(e) => handleCategoryChange(e.target.value)}
                                    className="w-full px-3 sm:px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                                >
                                    <option value="all">Semua Kategori</option>
                                    {categories.map((category) => (
                                        <option key={category.id} value={category.id.toString()}>
                                            {category.name}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {/* Status Filter */}
                            <div>
                                <label className="block text-sm font-medium text-gray-300 mb-2">
                                    Status
                                </label>
                                <select
                                    value={selectedStatus}
                                    onChange={(e) => handleStatusChange(e.target.value)}
                                    className="w-full px-3 sm:px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                                >
                                    <option value="all">Semua Status</option>
                                    <option value="active">Aktif</option>
                                    <option value="completed">Selesai</option>
                                    <option value="paused">Dijeda</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {/* Results Count */}
                    <div className="mb-4 sm:mb-6 px-4 sm:px-0">
                        <p className="text-gray-300 text-sm sm:text-base">
                            Menampilkan {campaigns.from || 0} - {campaigns.to || 0} dari {campaigns.total || 0} kampanye
                        </p>
                    </div>

                    {/* Campaigns Grid */}
                    {campaigns.data && campaigns.data.length > 0 ? (
                        <>
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8" id={campaigns.data.length > 0 ? `${campaigns.data.length}` : ''}>
                                {campaigns.data.map((campaign) => {
                                const progress = calculateProgress(campaign.collected_amount, campaign.target_amount);
                                const category = categories.find(cat => cat.id === campaign.category_id);
                                
                                return (
                                    <div key={campaign.id} className="bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 mx-4 sm:mx-0">
                                        {/* Campaign Image */}
                                        <div className="relative h-40 sm:h-48 bg-gray-700">
                                            {campaign.featured_image ? (
                                                <img
                                                    src={`/storage/campaigns/${campaign.featured_image}`}
                                                    alt={campaign.title}
                                                    className="w-full h-full object-cover"
                                                />
                                            ) : (
                                                <div className="w-full h-full flex items-center justify-center">
                                                    <svg className="w-12 sm:w-16 h-12 sm:h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            )}
                                            
                                            {/* Category Badge */}
                                            {category && (
                                                <div className="absolute top-2 sm:top-4 left-2 sm:left-4">
                                                    <span className="bg-blue-600 text-white px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium">
                                                        {category.name}
                                                    </span>
                                                </div>
                                            )}

                                            {/* Status Badge */}
                                            <div className="absolute top-2 sm:top-4 right-2 sm:right-4">
                                                <span className={`px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium ${
                                                    campaign.status === 'active' 
                                                        ? 'bg-green-600 text-white' 
                                                        : 'bg-gray-600 text-white'
                                                }`}>
                                                    {campaign.status === 'active' ? 'Aktif' : 'Selesai'}
                                                </span>
                                            </div>
                                        </div>

                                        {/* Campaign Content */}
                                        <div className="p-6">
                                            <h3 className="text-xl font-semibold text-white mb-2 line-clamp-2">
                                                {campaign.title}
                                            </h3>
                                            
                                            <p className="text-gray-300 mb-4 line-clamp-3">
                                                {campaign.short_desc}
                                            </p>

                                            {/* Progress Bar */}
                                            <div className="mb-4">
                                                <div className="flex justify-between text-sm mb-2">
                                                    <span className="text-gray-300">Terkumpul</span>
                                                    <span className="text-white font-medium">{progress.toFixed(1)}%</span>
                                                </div>
                                                <div className="w-full bg-gray-700 rounded-full h-2">
                                                    <div
                                                        className="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-300"
                                                        style={{ width: `${Math.min(progress, 100)}%` }}
                                                    ></div>
                                                </div>
                                                <div className="flex justify-between mt-2">
                                                    <span className="text-green-400 font-semibold">
                                                        {formatCurrency(campaign.collected_amount)}
                                                    </span>
                                                    <span className="text-gray-400">
                                                        dari {formatCurrency(campaign.target_amount)}
                                                    </span>
                                                </div>
                                            </div>

                                            {/* Campaign Stats */}
                                            <div className="flex justify-between text-sm text-gray-400 mb-4">
                                                <span>{(campaign as any).donors_count || 0} donatur</span>
                                                <span>
                                                    {(campaign as any).deadline && new Date((campaign as any).deadline) > new Date() 
                                                        ? `${Math.ceil((new Date((campaign as any).deadline).getTime() - new Date().getTime()) / (1000 * 60 * 60 * 24))} hari lagi`
                                                        : 'Tidak terbatas'
                                                    }
                                                </span>
                                            </div>

                                            {/* Action Buttons */}
                                            <div className="flex gap-2">
                                                <Link
                                                    href={`/campaigns/${campaign.slug}`}
                                                    className="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg font-medium transition-colors duration-200"
                                                >
                                                    Lihat Detail
                                                </Link>
                                                <Link
                                                    href={`/campaigns/${campaign.slug}/donate`}
                                                    className="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded-lg font-medium transition-colors duration-200"
                                                >
                                                    Donasi Sekarang
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                );
                                })}
                            </div>
                            
                            {/* Pagination */}
                            {campaigns.last_page > 1 && (
                                <div className="mt-8">
                                    <Pagination
                                        data={{
                                            current_page: campaigns.current_page,
                                            last_page: campaigns.last_page,
                                            per_page: campaigns.per_page,
                                            total: campaigns.total,
                                            from: campaigns.from,
                                            to: campaigns.to,
                                            data: campaigns.data,
                                            links: [],
                                            first_page_url: `/campaigns?page=1${searchQuery ? `&search=${searchQuery}` : ''}${selectedCategory !== 'all' ? `&category=${selectedCategory}` : ''}${selectedStatus !== 'all' ? `&status=${selectedStatus}` : ''}`,
                                            last_page_url: `/campaigns?page=${campaigns.last_page}${searchQuery ? `&search=${searchQuery}` : ''}${selectedCategory !== 'all' ? `&category=${selectedCategory}` : ''}${selectedStatus !== 'all' ? `&status=${selectedStatus}` : ''}`,
                                            next_page_url: campaigns.current_page < campaigns.last_page ? `/campaigns?page=${campaigns.current_page + 1}${searchQuery ? `&search=${searchQuery}` : ''}${selectedCategory !== 'all' ? `&category=${selectedCategory}` : ''}${selectedStatus !== 'all' ? `&status=${selectedStatus}` : ''}` : null,
                                            prev_page_url: campaigns.current_page > 1 ? `/campaigns?page=${campaigns.current_page - 1}${searchQuery ? `&search=${searchQuery}` : ''}${selectedCategory !== 'all' ? `&category=${selectedCategory}` : ''}${selectedStatus !== 'all' ? `&status=${selectedStatus}` : ''}` : null,
                                            path: '/campaigns'
                                        }}
                                        preserveScroll={false}
                                    />
                                </div>
                            )}
                        </>
                    ) : (
                        <div className="text-center py-12">
                            <svg className="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.291-1.1-5.291-2.709M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <h3 className="text-xl font-semibold text-white mb-2">Tidak ada kampanye ditemukan</h3>
                            <p className="text-gray-400">
                                Coba ubah filter atau kata kunci pencarian Anda
                            </p>
                        </div>
                    )}

                    {/* Call to Action */}
                    <div className="mt-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-8 text-center">
                        <h2 className="text-3xl font-bold text-white mb-4">
                            Ingin Membuat Kampanye Sendiri?
                        </h2>
                        <p className="text-blue-100 mb-6 max-w-2xl mx-auto">
                            Bergabunglah dengan platform kami dan mulai galang dana untuk tujuan yang Anda pedulikan. 
                            Proses mudah, aman, dan terpercaya.
                        </p>
                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            <Link
                                href="/register"
                                className="bg-white text-blue-600 hover:bg-gray-100 font-semibold py-3 px-6 rounded-lg transition-colors"
                            >
                                Daftar Sekarang
                            </Link>
                            <Link
                                href="/about"
                                className="bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-600 font-semibold py-3 px-6 rounded-lg transition-colors"
                            >
                                Pelajari Lebih Lanjut
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            
    </PublicLayout>
);
}
