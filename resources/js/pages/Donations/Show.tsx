import { Head } from '@inertiajs/react';
import { Transaction } from '@/types';
import PublicLayout from '@/layouts/PublicLayout';
import { ClockIcon, CreditCardIcon, ExclamationTriangleIcon } from '@heroicons/react/24/outline';

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
        payment_channel?: {
            name: string;
            code: string;
        };
        payment_provider: {
            name: string;
            code: string;
        };
        expired_at: string;
        payment_url: string;
        snap_token: string;
        qr_code: string;
    };
}

export default function DonationShow({ transaction }: Props) {
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

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'success':
                return 'bg-green-100 text-green-800';
            case 'completed':
                return 'bg-green-100 text-green-800';
            case 'settlement':
                return 'bg-green-100 text-green-800';
            case 'pending':
                return 'bg-yellow-100 text-yellow-800';
            case 'failed':
                return 'bg-red-100 text-red-800';
            case 'expired':
                return 'bg-gray-100 text-gray-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getStatusText = (status: string) => {
        switch (status) {
            case 'success':
                return 'Berhasil';
            case 'completed':
                return 'Berhasil';
            case 'settlement':
                return 'Berhasil';
            case 'pending':
                return 'Menunggu Pembayaran';
            case 'failed':
                return 'Gagal';
            case 'expired':
                return 'Kedaluwarsa';
            default:
                return status;
        }
    };

    const isExpired = transaction.expired_at && new Date(transaction.expired_at) < new Date();

    return (
        <PublicLayout title="Detail Transaksi" currentPage="donations">
            <Head title="Detail Transaksi" />

            <div className="min-h-screen bg-gray-800 py-12">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="bg-white rounded-lg shadow-lg overflow-hidden">
                        {/* Header */}
                        <div className={`bg-gray-200 px-6 py-8 ${transaction.status === 'success' || transaction.status === 'completed' || transaction.status === 'settlement' ? 'bg-green-200' : ''}`}>
                            <div className="flex items-center justify-between">
                                <div>
                                    <h1 className="text-2xl font-bold text-gray-800 mb-2">
                                        Detail Transaksi
                                    </h1>
                                    <p className="text-gray-600">
                                        ID: {transaction.ref_id}
                                    </p>
                                </div>
                                <div className="text-right">
                                    <span className={`inline-flex px-3 py-1 text-sm font-semibold rounded-full ${getStatusColor(transaction.status)}`}>
                                        {getStatusText(transaction.status)}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div className="px-6 py-8">
                            {/* Status Alert */}
                            {transaction.status === 'pending' && (
                                <div className="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div className="flex items-start">
                                        <ClockIcon className="h-5 w-5 text-yellow-400 mt-0.5 mr-3" />
                                        <div>
                                            <h3 className="text-sm font-medium text-yellow-800">
                                                Menunggu Pembayaran
                                            </h3>
                                            <p className="text-sm text-yellow-700 mt-1">
                                                Silakan lakukan pembayaran sebelum {transaction.expired_at && formatDate(transaction.expired_at)}
                                            </p>
                                            {transaction.payment_url && (
                                                <a
                                                    href={transaction.payment_url}
                                                    className="inline-flex items-center mt-3 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-md transition-colors duration-200"
                                                >
                                                    <CreditCardIcon className="h-4 w-4 mr-2" />
                                                    Lanjutkan Pembayaran
                                                </a>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            )}

                            {isExpired && transaction.status === 'pending' && (
                                <div className="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div className="flex items-start">
                                        <ExclamationTriangleIcon className="h-5 w-5 text-red-400 mt-0.5 mr-3" />
                                        <div>
                                            <h3 className="text-sm font-medium text-red-800">
                                                Transaksi Kedaluwarsa
                                            </h3>
                                            <p className="text-sm text-red-700 mt-1">
                                                Transaksi ini telah kedaluwarsa. Silakan buat transaksi baru.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* Transaction Details */}
                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <div>
                                    <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                        Informasi Transaksi
                                    </h3>
                                    <dl className="space-y-4">
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">ID Transaksi</dt>
                                            <dd className="text-sm text-gray-900 font-mono">{transaction.ref_id}</dd>
                                        </div>
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Jumlah Donasi</dt>
                                            <dd className="text-xl font-semibold text-green-600">
                                                {formatCurrency(transaction.donation.amount)}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Provider Pembayaran</dt>
                                            <dd className="text-sm text-gray-900">{transaction.payment_provider.name}</dd>
                                        </div>
                                        {transaction.payment_channel && (
                                            <div>
                                                <dt className="text-sm font-medium text-gray-500">Metode Pembayaran</dt>
                                                <dd className="text-sm text-gray-900">{transaction.payment_channel.name}</dd>
                                            </div>
                                        )}
                                        <div>
                                            <dt className="text-sm font-medium text-gray-500">Tanggal Dibuat</dt>
                                            <dd className="text-sm text-gray-900">
                                                {formatDate(transaction.created_at)}
                                            </dd>
                                        </div>
                                        {transaction.expired_at && (
                                            <div>
                                                <dt className="text-sm font-medium text-gray-500">Batas Waktu</dt>
                                                <dd className="text-sm text-gray-900">
                                                    {formatDate(transaction.expired_at)}
                                                </dd>
                                            </div>
                                        )}
                                        {transaction.paid_at && (
                                            <div>
                                                <dt className="text-sm font-medium text-gray-500">Tanggal Dibayar</dt>
                                                <dd className="text-sm text-gray-900">
                                                    {formatDate(transaction.paid_at)}
                                                </dd>
                                            </div>
                                        )}
                                    </dl>
                                </div>

                                <div>
                                    <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                        Informasi Donatur
                                    </h3>
                                    <dl className="space-y-4">
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
                                                <dd className="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">
                                                    {transaction.donation.message}
                                                </dd>
                                            </div>
                                        )}
                                    </dl>
                                </div>
                            </div>

                            {/* Campaign Info */}
                            <div className="border-t pt-8 mt-8">
                                <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                    Kampanye yang Didukung
                                </h3>
                                <div className="bg-gray-50 rounded-lg p-6">
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

                            {/* QR Code or Instructions */}
                            {transaction.qr_code && (
                                <div className="border-t pt-8 mt-8">
                                    <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                        QR Code Pembayaran
                                    </h3>
                                    <div className="bg-white border rounded-lg p-6 text-center">
                                        <img
                                            src={transaction.qr_code}
                                            alt="QR Code Pembayaran"
                                            className="mx-auto max-w-xs"
                                        />
                                        <p className="text-sm text-gray-600 mt-4">
                                            Scan QR code di atas untuk melakukan pembayaran
                                        </p>
                                    </div>
                                </div>
                            )}

                            {/* Actions */}
                            <div className="border-t pt-8 mt-8">
                                <div className="flex flex-col sm:flex-row gap-4">
                                    <a
                                        href={`/campaigns/${transaction.donation.campaign.slug}`}
                                        className="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-3 px-6 rounded-lg font-medium transition-colors duration-200"
                                    >
                                        Kembali ke Kampanye
                                    </a>
                                    <a
                                        href="/campaigns"
                                        className="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 text-center py-3 px-6 rounded-lg font-medium transition-colors duration-200"
                                    >
                                        Lihat Kampanye Lainnya
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
