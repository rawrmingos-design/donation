import { Head } from '@inertiajs/react';
import { Transaction } from '@/types';
import PublicLayout from '@/layouts/PublicLayout';
import { CheckCircleIcon } from '@heroicons/react/24/solid';
import toast from 'react-hot-toast';
import { useEffect } from 'react';

interface Props {
    transaction: Transaction & {
        donation: {
            campaign: {
                id: number;
                title: string;
                slug: string;
            };
            donor: {
                name: string;
                email: string;
            };
            amount: number;
            message?: string;
        };
    };
}

export default function DonationSuccess({ transaction }: Props) {
    // Show success toast notification on page load
    useEffect(() => {
        const isSuccessful = transaction.status === 'success' || 
                           transaction.status === 'completed' || 
                           transaction.status === 'settlement';
        
        if (isSuccessful) {
            toast.success(`Terima kasih! Donasi sebesar ${formatCurrency(transaction.donation.amount)} berhasil diproses! 💝`, {
                duration: 6000,
                icon: '🎉',
            });
        } else if (transaction.status === 'pending') {
            toast(`Donasi Anda sedang diproses. Silakan tunggu konfirmasi pembayaran 🕐`, {
                duration: 5000,
                icon: '⏳',
            });
        }
    }, [transaction.status]);

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(amount);
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    return (
        <PublicLayout title="Donasi Berhasil" currentPage="donations">
            <Head title="Donasi Berhasil" />

            <div className="bg-gray-800 min-h-screen pt-12 pb-4">
                <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="bg-white rounded-lg shadow-lg overflow-hidden">
                        {/* Success Header */}
                        <div className="bg-green-600 px-6 py-8 text-center">
                            <CheckCircleIcon className="mx-auto h-16 w-16 text-white mb-4" />
                            <h1 className="text-2xl font-bold text-white mb-2">
                                Terima Kasih atas Donasi Anda!
                            </h1>
                            <p className="text-green-100">
                                Donasi Anda telah berhasil diproses
                            </p>
                        </div>

                        {/* Transaction Details */}
                        <div className="px-6 py-8">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div>
                                    <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                        Detail Transaksi
                                    </h3>
                                    <dl className="space-y-3">
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">ID Transaksi</dt>
                                            <dd className="text-sm text-gray-900 font-mono">{transaction.ref_id}</dd>
                                        </div>
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Jumlah Donasi</dt>
                                            <dd className="text-lg font-semibold text-green-600">
                                                {formatCurrency(transaction.donation.amount)}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Status</dt>
                                            <dd>
                                                <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                                                    transaction.status === 'success' || transaction.status === 'completed' || transaction.status === 'settlement'
                                                        ? 'bg-green-100 text-green-800'
                                                        : transaction.status === 'pending' || transaction.status === 'failed'
                                                        ? 'bg-yellow-100 text-yellow-800'
                                                        : 'bg-red-100 text-red-800'
                                                }`}>
                                                    {transaction.status === 'success' || transaction.status === 'completed' || transaction.status === 'settlement' ? 'Berhasil' : 
                                                     transaction.status === 'pending' || transaction.status === 'failed' ? 'Menunggu' : 'Gagal'}
                                                </span>
                                            </dd>
                                        </div>
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Tanggal</dt>
                                            <dd className="text-sm text-gray-900">
                                                {formatDate(transaction.created_at)}
                                            </dd>
                                        </div>
                                    </dl>
                                </div>

                                <div>
                                    <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                        Detail Donatur
                                    </h3>
                                    <dl className="space-y-3">
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Nama</dt>
                                            <dd className="text-sm text-gray-900">{transaction.donation.donor.name}</dd>
                                        </div>
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Email</dt>
                                            <dd className="text-sm text-gray-900">{transaction.donation.donor.email}</dd>
                                        </div>
                                        {transaction.donation.message && (
                                            <div>
                                                <dt className="text-sm font-medium text-gray-500">Pesan</dt>
                                                <dd className="text-sm text-gray-900">{transaction.donation.message}</dd>
                                            </div>
                                        )}
                                    </dl>
                                </div>
                            </div>

                            {/* Campaign Info */}
                            <div className="border-t pt-6">
                                <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                    Kampanye yang Didukung
                                </h3>
                                <div className="bg-gray-50 rounded-lg p-4">
                                    <h4 className="font-medium text-gray-900 mb-2">
                                        {transaction.donation.campaign.title}
                                    </h4>
                                    <p className="text-sm text-gray-600 mb-4">
                                        Donasi Anda akan membantu kesuksesan kampanye ini.
                                    </p>
                                    <a
                                        href={`/campaigns/${transaction.donation.campaign.slug}`}
                                        className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200"
                                    >
                                        Lihat Kampanye
                                    </a>
                                </div>
                            </div>

                            {/* Actions */}
                            <div className="border-t pt-6 mt-6">
                                <div className="flex flex-col sm:flex-row gap-4">
                                    <a
                                        href="/campaigns"
                                        className="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-3 px-6 rounded-lg font-medium transition-colors duration-200"
                                    >
                                        Lihat Kampanye Lainnya
                                    </a>
                                    <a
                                        href="/"
                                        className="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 text-center py-3 px-6 rounded-lg font-medium transition-colors duration-200"
                                    >
                                        Kembali ke Beranda
                                    </a>
                                </div>
                            </div>

                            {/* Additional Info */}
                            <div className="border-t pt-6 mt-6">
                                <div className="bg-blue-50 rounded-lg p-4">
                                    <h4 className="font-medium text-blue-900 mb-2">
                                        Informasi Penting
                                    </h4>
                                    <ul className="text-sm text-blue-800 space-y-1">
                                        <li>• Bukti donasi telah dikirim ke email Anda</li>
                                        <li>• Donasi Anda akan digunakan sesuai dengan tujuan kampanye</li>
                                        <li>• Anda akan mendapat update perkembangan kampanye via email</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
