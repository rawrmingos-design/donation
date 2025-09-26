import { Link } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';

interface Campaign {
    id: number;
    title: string;
    short_desc: string;
    description: string;
    featured_image: string;
    target_amount: number;
    collected_amount: number;
    deadline: string;
    slug: string;
    category: {
        id: number;
        name: string;
    };
    user: {
        id: number;
        name: string;
    };
    donations_count: number;
}

interface HomeProps {
    urgentCampaigns: Campaign[];
    featuredCampaigns: Campaign[];
    stats: {
        total_campaigns: number;
        total_raised: number;
        total_donors: number;
        campaigns_funded: number;
    };
}

export default function Home({ urgentCampaigns = [], featuredCampaigns = [], stats }: HomeProps) {
    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(amount);
    };

    const calculateProgress = (collected: number, target: number) => {
        return target > 0 ? Math.min((collected / target) * 100, 100) : 0;
    };

    // Determine which campaigns to display and section content
    const displayCampaigns = urgentCampaigns.length >= 3 ? urgentCampaigns : featuredCampaigns;
    const isShowingUrgent = urgentCampaigns.length >= 3;

    return (
        <PublicLayout title="Platform Donasi Terpercaya" currentPage="home">
            {/* Hero Section */}
            <section className="relative bg-gradient-to-br from-blue-900 via-purple-900 to-gray-900 overflow-hidden">
                <div className="absolute inset-0 bg-black/20"></div>
                <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-32">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                        <div className="text-center lg:text-left order-2 lg:order-1">
                            <h1 className="text-3xl sm:text-4xl lg:text-6xl font-bold text-white mb-4 sm:mb-6 leading-tight">
                                Wujudkan
                                <span className="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400"> Kebaikan </span>
                                Bersama
                            </h1>
                            <p className="text-lg sm:text-xl text-gray-300 mb-6 sm:mb-8 leading-relaxed px-2 sm:px-0">
                                Platform donasi terpercaya yang menghubungkan hati mulia Anda dengan mereka yang membutuhkan bantuan. 
                                Setiap donasi adalah harapan baru.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center lg:justify-start px-4 sm:px-0">
                                <Link
                                    href="/campaigns"
                                    className="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 sm:py-4 px-6 sm:px-8 rounded-full transition-all duration-300 transform hover:scale-105 shadow-lg text-center"
                                >
                                    Mulai Berdonasi
                                </Link>
                                <Link
                                    href="/register"
                                    className="bg-transparent border-2 border-white text-white hover:bg-white hover:text-gray-900 font-semibold py-3 sm:py-4 px-6 sm:px-8 rounded-full transition-all duration-300 text-center"
                                >
                                    Buat Kampanye
                                </Link>
                            </div>
                        </div>
                        
                        <div className="relative order-1 lg:order-2">
                            <div className="bg-white/10 backdrop-blur-sm rounded-2xl p-4 sm:p-6 lg:p-8 border border-white/20 mx-4 sm:mx-0">
                                <div className="grid grid-cols-2 gap-3 sm:gap-4 lg:gap-6">
                                    <div className="text-center">
                                        <div className="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-400 mb-1 sm:mb-2">
                                            {stats?.total_campaigns || '1,000+'}
                                        </div>
                                        <div className="text-xs sm:text-sm lg:text-base text-gray-300">Kampanye Aktif</div>
                                    </div>
                                    <div className="text-center">
                                        <div className="text-xl sm:text-2xl lg:text-3xl font-bold text-green-400 mb-1 sm:mb-2 truncate">
                                            {stats?.total_raised ? formatCurrency(stats.total_raised).replace('Rp', 'Rp ').replace(',00', '') : 'Rp 5M+'}
                                        </div>
                                        <div className="text-xs sm:text-sm lg:text-base text-gray-300">Dana Terkumpul</div>
                                    </div>
                                    <div className="text-center">
                                        <div className="text-xl sm:text-2xl lg:text-3xl font-bold text-purple-400 mb-1 sm:mb-2">
                                            {stats?.total_donors || '10,000+'}
                                        </div>
                                        <div className="text-xs sm:text-sm lg:text-base text-gray-300">Donatur</div>
                                    </div>
                                    <div className="text-center">
                                        <div className="text-xl sm:text-2xl lg:text-3xl font-bold text-yellow-400 mb-1 sm:mb-2">
                                            {stats?.campaigns_funded || '500+'}
                                        </div>
                                        <div className="text-xs sm:text-sm lg:text-base text-gray-300">Kampanye Berhasil</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Campaigns Section */}
            <section className="py-12 sm:py-16 bg-gray-900">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center mb-8 sm:mb-12">
                        <h2 className="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-3 sm:mb-4 px-4">
                            {isShowingUrgent ? 'Kampanye Mendesak' : 'Kampanye Pilihan'}
                        </h2>
                        <p className="text-lg sm:text-xl text-gray-300 max-w-3xl mx-auto px-4 sm:px-0">
                            {isShowingUrgent 
                                ? 'Kampanye-kampanye ini hampir mencapai target atau waktu habis. Bantuan Anda sangat dibutuhkan sekarang!'
                                : 'Kampanye-kampanye terbaru yang membutuhkan dukungan Anda untuk mencapai tujuan mulia mereka.'
                            }
                        </p>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                        {displayCampaigns.length > 0 ? displayCampaigns.map((campaign) => {
                            const progress = calculateProgress(campaign.collected_amount, campaign.target_amount);
                            const isAlmostFull = progress >= 80;
                            const daysLeft = Math.max(0, Math.ceil((new Date(campaign.deadline).getTime() - new Date().getTime()) / (1000 * 60 * 60 * 24)));
                            const isAlmostExpired = daysLeft <= 7;
                            
                            return (
                                <div key={campaign.id} className="bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 sm:hover:-translate-y-2 mx-4 sm:mx-0">
                                    <div className="relative">
                                        <img 
                                            src={campaign.featured_image ? `/storage/${campaign.featured_image}` : '/placeholder-campaign.jpg'} 
                                            alt={campaign.title}
                                            className="w-full h-40 sm:h-48 object-cover"
                                        />
                                        <div className="absolute top-2 sm:top-4 left-2 sm:left-4">
                                            <span className={`text-white px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-semibold ${
                                                isShowingUrgent 
                                                    ? 'bg-red-600' 
                                                    : 'bg-blue-600'
                                            }`}>
                                                {isShowingUrgent 
                                                    ? (isAlmostExpired ? `${daysLeft} hari lagi` : `${Math.round(progress)}%`)
                                                    : `${Math.round(progress)}%`
                                                }
                                            </span>
                                        </div>
                                        <div className="absolute top-2 sm:top-4 right-2 sm:right-4">
                                            <span className="bg-blue-600 text-white px-2 sm:px-3 py-1 rounded-full text-xs">
                                                {campaign.category.name}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div className="p-4 sm:p-6">
                                        <h3 className="text-lg sm:text-xl font-semibold text-white mb-2 sm:mb-3 line-clamp-2">
                                            {campaign.title}
                                        </h3>
                                        <p className="text-gray-300 mb-3 sm:mb-4 line-clamp-2 sm:line-clamp-3 text-sm sm:text-base">
                                            {campaign.short_desc}
                                        </p>
                                        
                                        <div className="mb-3 sm:mb-4">
                                            <div className="flex justify-between text-xs sm:text-sm text-gray-400 mb-2">
                                                <span>Terkumpul</span>
                                                <span>{Math.round(progress)}%</span>
                                            </div>
                                            <div className="w-full bg-gray-700 rounded-full h-2">
                                                <div 
                                                    className={`h-2 rounded-full transition-all duration-300 ${
                                                        isAlmostFull ? 'bg-gradient-to-r from-green-500 to-green-400' : 
                                                        'bg-gradient-to-r from-blue-500 to-purple-500'
                                                    }`}
                                                    style={{ width: `${progress}%` }}
                                                ></div>
                                            </div>
                                            <div className="flex justify-between text-xs sm:text-sm text-gray-300 mt-2">
                                                <span className="truncate pr-2">{formatCurrency(campaign.collected_amount).replace(',00', '')}</span>
                                                <span className="truncate pl-2">{formatCurrency(campaign.target_amount).replace(',00', '')}</span>
                                            </div>
                                        </div>
                                        
                                        <Link
                                            href={`/campaigns/${campaign.slug}`}
                                            className="block w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-center font-semibold py-2.5 sm:py-3 rounded-lg transition-all duration-300 text-sm sm:text-base"
                                        >
                                            Donasi Sekarang
                                        </Link>
                                    </div>
                                </div>
                            );
                        }) : (
                            // Placeholder campaigns when no data available
                            [...Array(3)].map((_, index) => (
                                <div key={index} className="bg-gray-800 rounded-xl overflow-hidden shadow-lg">
                                    <div className="relative">
                                        <div className="w-full h-48 bg-gradient-to-br from-gray-700 to-gray-600"></div>
                                        <div className="absolute top-4 left-4">
                                            <span className="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                                {index === 0 ? '3 hari lagi' : index === 1 ? '95% terkumpul' : '5 hari lagi'}
                                            </span>
                                        </div>
                                    </div>
                                    <div className="p-6">
                                        <h3 className="text-xl font-semibold text-white mb-3">
                                            {index === 0 ? 'Bantuan Operasi Jantung Anak' : 
                                             index === 1 ? 'Pembangunan Sekolah Desa' : 
                                             'Bantuan Korban Bencana Alam'}
                                        </h3>
                                        <p className="text-gray-300 mb-4">
                                            {index === 0 ? 'Seorang anak membutuhkan operasi jantung segera untuk menyelamatkan nyawanya.' :
                                             index === 1 ? 'Membangun sekolah untuk anak-anak di daerah terpencil agar mendapat pendidikan.' :
                                             'Memberikan bantuan darurat untuk korban bencana alam yang kehilangan tempat tinggal.'}
                                        </p>
                                        <div className="mb-4">
                                            <div className="flex justify-between text-sm text-gray-400 mb-2">
                                                <span>Terkumpul</span>
                                                <span>{index === 0 ? '75' : index === 1 ? '95' : '60'}%</span>
                                            </div>
                                            <div className="w-full bg-gray-700 rounded-full h-2">
                                                <div 
                                                    className={`h-2 rounded-full ${
                                                        index === 1 ? 'bg-gradient-to-r from-green-500 to-green-400' : 
                                                        'bg-gradient-to-r from-blue-500 to-purple-500'
                                                    }`}
                                                    style={{ width: `${index === 0 ? 75 : index === 1 ? 95 : 60}%` }}
                                                ></div>
                                            </div>
                                            <div className="flex justify-between text-sm text-gray-300 mt-2">
                                                <span>
                                                    {index === 0 ? 'Rp 75,000,000' : 
                                                     index === 1 ? 'Rp 190,000,000' : 
                                                     'Rp 30,000,000'}
                                                </span>
                                                <span>
                                                    {index === 0 ? 'Rp 100,000,000' : 
                                                     index === 1 ? 'Rp 200,000,000' : 
                                                     'Rp 50,000,000'}
                                                </span>
                                            </div>
                                        </div>
                                        <Link
                                            href="/campaigns"
                                            className="block w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-center font-semibold py-3 rounded-lg transition-all duration-300"
                                        >
                                            Donasi Sekarang
                                        </Link>
                                    </div>
                                </div>
                            ))
                        )}
                    </div>

                    <div className="text-center mt-8 sm:mt-12 px-4">
                        <Link
                            href="/campaigns"
                            className="inline-flex items-center bg-transparent border-2 border-blue-500 text-blue-400 hover:bg-blue-500 hover:text-white font-semibold py-2.5 sm:py-3 px-6 sm:px-8 rounded-lg transition-all duration-300 text-sm sm:text-base"
                        >
                            Lihat Semua Kampanye
                            <svg className="ml-2 w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </Link>
                    </div>
                </div>
            </section>

            {/* Features Section */}
            <section className="py-12 sm:py-16 bg-gray-800">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center mb-8 sm:mb-12">
                        <h2 className="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-3 sm:mb-4 px-4">
                            Mengapa Memilih Platform Kami?
                        </h2>
                        <p className="text-lg sm:text-xl text-gray-300 px-4 sm:px-0">
                            Kepercayaan dan transparansi adalah prioritas utama kami
                        </p>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 sm:gap-8 px-4 sm:px-0">
                        <div className="text-center group">
                            <div className="w-16 sm:w-20 h-16 sm:h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg className="w-8 sm:w-10 h-8 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <h3 className="text-xl sm:text-2xl font-semibold text-white mb-3 sm:mb-4">Aman & Terpercaya</h3>
                            <p className="text-gray-300 text-sm sm:text-base">
                                Sistem keamanan berlapis dan verifikasi ketat untuk melindungi setiap transaksi donasi Anda.
                            </p>
                        </div>

                        <div className="text-center group">
                            <div className="w-16 sm:w-20 h-16 sm:h-20 bg-gradient-to-br from-green-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg className="w-8 sm:w-10 h-8 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h3 className="text-xl sm:text-2xl font-semibold text-white mb-3 sm:mb-4">100% Transparan</h3>
                            <p className="text-gray-300 text-sm sm:text-base">
                                Laporan penggunaan dana real-time dan update berkala dari setiap kampanye donasi.
                            </p>
                        </div>

                        <div className="text-center group sm:col-span-2 md:col-span-1">
                            <div className="w-16 sm:w-20 h-16 sm:h-20 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg className="w-8 sm:w-10 h-8 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 className="text-xl sm:text-2xl font-semibold text-white mb-3 sm:mb-4">Dampak Nyata</h3>
                            <p className="text-gray-300 text-sm sm:text-base">
                                Setiap donasi menciptakan perubahan nyata dan membantu mereka yang benar-benar membutuhkan.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {/* CTA Section */}
            <section className="py-12 sm:py-16 bg-gradient-to-r from-blue-900 to-purple-900">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h2 className="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-4 sm:mb-6 px-4">
                        Mulai Berbagi Kebaikan Hari Ini
                    </h2>
                    <p className="text-lg sm:text-xl text-blue-100 mb-6 sm:mb-8 px-4 sm:px-0">
                        Bergabunglah dengan ribuan donatur yang telah mempercayai platform kami untuk menyalurkan kebaikan mereka.
                    </p>
                    <div className="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center px-4 sm:px-0">
                        <Link
                            href="/campaigns"
                            className="bg-white text-blue-900 hover:bg-gray-100 font-semibold py-3 sm:py-4 px-6 sm:px-8 rounded-full transition-all duration-300 transform hover:scale-105 text-center"
                        >
                            Jelajahi Kampanye
                        </Link>
                        <Link
                            href="/how-it-works"
                            className="bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-900 font-semibold py-3 sm:py-4 px-6 sm:px-8 rounded-full transition-all duration-300 text-center"
                        >
                            Pelajari Cara Kerja
                        </Link>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
