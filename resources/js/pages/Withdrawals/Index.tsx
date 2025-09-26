import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem, Campaign, Withdrawal } from '@/types';

interface Props {
    campaigns: (Campaign & { withdrawals: Withdrawal[] })[];
}

export default function WithdrawalsIndex({ campaigns }: Props) {
    const [selectedCampaign, setSelectedCampaign] = useState<string>('all');

    const filteredCampaigns = selectedCampaign === 'all' 
        ? campaigns 
        : campaigns.filter(campaign => campaign.id.toString() === selectedCampaign);

    const getStatusBadge = (status: string) => {
        const statusConfig = {
            pending: { color: 'bg-yellow-100 text-yellow-800', text: 'Menunggu' },
            approved: { color: 'bg-blue-100 text-blue-800', text: 'Disetujui' },
            processing: { color: 'bg-purple-100 text-purple-800', text: 'Diproses' },
            completed: { color: 'bg-green-100 text-green-800', text: 'Selesai' },
            rejected: { color: 'bg-red-100 text-red-800', text: 'Ditolak' },
            cancelled: { color: 'bg-gray-100 text-gray-800', text: 'Dibatalkan' },
        };

        const config = statusConfig[status as keyof typeof statusConfig] || statusConfig.pending;
        
        return (
            <span className={`px-2 py-1 text-xs font-medium rounded-full ${config.color}`}>
                {config.text}
            </span>
        );
    };

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(amount); // Amount already in rupiah
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    };

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Penarikan Dana',
            href: '/withdrawals',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Penarikan Dana" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-gray-800 border-b border-gray-200">
                            <div className="flex justify-between items-center mb-6">
                                <div>
                                    <h2 className="text-2xl font-bold text-gray-100">💰 Penarikan Dana</h2>
                                    <p className="text-gray-400 mt-1">Kelola permintaan penarikan dana dari kampanye Anda</p>
                                </div>
                            </div>

                            {/* Filter */}
                            <div className="mb-6">
                                <label htmlFor="campaign-filter" className="block text-sm font-medium text-gray-400 mb-2">
                                    Filter berdasarkan kampanye:
                                </label>
                                <select
                                    id="campaign-filter"
                                    value={selectedCampaign}
                                    onChange={(e) => setSelectedCampaign(e.target.value)}
                                    className="block w-full max-w-xs px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="all">Semua Kampanye</option>
                                    {campaigns.map((campaign) => (
                                        <option key={campaign.id} value={campaign.id.toString()}>
                                            {campaign.title}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {/* Campaigns List */}
                            <div className="space-y-6">
                                {filteredCampaigns.map((campaign) => (
                                    <div key={campaign.id} className="border border-gray-200 rounded-lg p-6">
                                        <div className="flex justify-between items-start mb-4">
                                            <div>
                                                <h3 className="text-lg font-semibold text-gray-100">{campaign.title}</h3>
                                                <p className="text-sm text-gray-400 mt-1">
                                                    Dana Terkumpul: <span className="font-medium text-green-600">
                                                        {formatCurrency(campaign.collected_amount)}
                                                    </span>
                                                </p>
                                            </div>
                                            {campaign.collected_amount > 0 && (
                                                <Link
                                                    href={`/campaigns/${campaign.id}/withdrawals/create`}
                                                    className="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                >
                                                    Ajukan Penarikan
                                                </Link>
                                            )}
                                        </div>

                                        {/* Withdrawals List */}
                                        {campaign.withdrawals.length > 0 ? (
                                            <div className="space-y-3">
                                                <h4 className="text-sm font-medium text-gray-100">Riwayat Penarikan:</h4>
                                                {campaign.withdrawals.map((withdrawal) => (
                                                    <div key={withdrawal.id} className="bg-white/10 rounded-lg p-4">
                                                        <div className="flex justify-between items-start">
                                                            <div className="flex-1">
                                                                <div className="flex items-center space-x-3 mb-2">
                                                                    {getStatusBadge(withdrawal.status)}
                                                                    <span className="text-sm text-gray-100">
                                                                        {formatDate(withdrawal.requested_at)}
                                                                    </span>
                                                                </div>
                                                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                                                    <div>
                                                                        <span className="text-gray-100">Jumlah:</span>
                                                                        <div className="font-medium">{formatCurrency(withdrawal.amount)}</div>
                                                                    </div>
                                                                    <div>
                                                                        <span className="text-gray-100">Biaya Admin:</span>
                                                                        <div className="font-medium text-red-600">-{formatCurrency(withdrawal.fee_amount)}</div>
                                                                    </div>
                                                                    <div>
                                                                        <span className="text-gray-100">Diterima:</span>
                                                                        <div className="font-medium text-green-600">{formatCurrency(withdrawal.net_amount)}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div className="flex space-x-2 ml-4">
                                                                <Link
                                                                    href={`/withdrawals/${withdrawal.id}`}
                                                                    className="px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
                                                                >
                                                                    Detail
                                                                </Link>
                                                                {(withdrawal.status === 'pending' || withdrawal.status === 'approved') && (
                                                                    <Link
                                                                        href={`/withdrawals/${withdrawal.id}/cancel`}
                                                                        method="patch"
                                                                        as="button"
                                                                        className="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200"
                                                                        onClick={(e) => {
                                                                            if (!confirm('Apakah Anda yakin ingin membatalkan penarikan ini?')) {
                                                                                e.preventDefault();
                                                                            }
                                                                        }}
                                                                    >
                                                                        Batalkan
                                                                    </Link>
                                                                )}
                                                            </div>
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        ) : (
                                            <div className="text-center py-8 text-gray-400">
                                                <div className="text-4xl mb-2">💸</div>
                                                <p>Belum ada permintaan penarikan untuk kampanye ini</p>
                                                {campaign.collected_amount > 0 && (
                                                    <p className="text-sm mt-1">Klik "Ajukan Penarikan" untuk memulai</p>
                                                )}
                                            </div>
                                        )}
                                    </div>
                                ))}
                            </div>

                            {campaigns.length === 0 && (
                                <div className="text-center py-12">
                                    <div className="text-6xl mb-4">🎯</div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-2">Belum Ada Kampanye</h3>
                                    <p className="text-gray-600 mb-4">Anda belum memiliki kampanye yang dapat ditarik dananya.</p>
                                    <Link
                                        href="/campaign/create"
                                        className="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700"
                                    >
                                        Buat Kampanye Baru
                                    </Link>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
