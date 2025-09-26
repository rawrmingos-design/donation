import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Withdrawal, Campaign, User } from '@/types';

interface Props {
    withdrawal: Withdrawal & {
        campaign: Campaign;
        approvedBy?: User;
    };
}

export default function WithdrawalsShow({ withdrawal }: Props) {
    const getStatusBadge = (status: string) => {
        const statusConfig = {
            pending: { color: 'bg-yellow-100 text-yellow-800', text: 'Menunggu Persetujuan', icon: '⏳' },
            approved: { color: 'bg-blue-100 text-blue-800', text: 'Disetujui', icon: '✅' },
            processing: { color: 'bg-purple-100 text-purple-800', text: 'Sedang Diproses', icon: '🔄' },
            completed: { color: 'bg-green-100 text-green-800', text: 'Selesai', icon: '🎉' },
            rejected: { color: 'bg-red-100 text-red-800', text: 'Ditolak', icon: '❌' },
            cancelled: { color: 'bg-gray-100 text-gray-800', text: 'Dibatalkan', icon: '🚫' },
        };

        const config = statusConfig[status as keyof typeof statusConfig] || statusConfig.pending;
        
        return (
            <span className={`inline-flex items-center px-3 py-1 text-sm font-medium rounded-full ${config.color}`}>
                <span className="mr-1">{config.icon}</span>
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
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const getMethodDisplay = (method: string) => {
        return method === 'bank_transfer' ? '🏦 Transfer Bank' : '📱 E-Wallet';
    };

    return (
        <AppLayout>
            <Head title={`Detail Penarikan #${withdrawal.id}`} />

            <div className="py-12">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-gray-800 border-b border-gray-200">
                            <div className="mb-6">
                                <Link
                                    href="/withdrawals"
                                    className="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                >
                                    ← Kembali ke Daftar Penarikan
                                </Link>
                            </div>

                            <div className="mb-8">
                                <div className="flex justify-between items-start">
                                    <div>
                                        <h2 className="text-2xl font-bold text-gray-100">
                                            💰 Detail Penarikan #{withdrawal.id}
                                        </h2>
                                        <p className="text-gray-200 mt-1">
                                            Dari kampanye "{withdrawal.campaign.title}"
                                        </p>
                                    </div>
                                    <div>
                                        {getStatusBadge(withdrawal.status)}
                                    </div>
                                </div>
                            </div>

                            {/* Status Timeline */}
                            <div className="mb-8">
                                <h3 className="text-lg font-semibold text-gray-100 mb-4">📋 Status Timeline</h3>
                                <div className="space-y-4">
                                    <div className="flex items-center space-x-3">
                                        <div className="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        <div>
                                            <div className="font-medium">Permintaan Diajukan</div>
                                            <div className="text-sm text-gray-600">{formatDate(withdrawal.requested_at)}</div>
                                        </div>
                                    </div>
                                    
                                    {withdrawal.approved_at && (
                                        <div className="flex items-center space-x-3">
                                            <div className="w-3 h-3 bg-green-500 rounded-full"></div>
                                            <div>
                                                <div className="font-medium">Disetujui</div>
                                                <div className="text-sm text-gray-600">
                                                    {formatDate(withdrawal.approved_at)}
                                                    {withdrawal.approvedBy && (
                                                        <span className="ml-2">oleh {withdrawal.approvedBy.name}</span>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    )}
                                    
                                    {withdrawal.processed_at && (
                                        <div className="flex items-center space-x-3">
                                            <div className="w-3 h-3 bg-purple-500 rounded-full"></div>
                                            <div>
                                                <div className="font-medium">Sedang Diproses</div>
                                                <div className="text-sm text-gray-600">{formatDate(withdrawal.processed_at)}</div>
                                            </div>
                                        </div>
                                    )}
                                    
                                    {withdrawal.completed_at && (
                                        <div className="flex items-center space-x-3">
                                            <div className="w-3 h-3 bg-green-600 rounded-full"></div>
                                            <div>
                                                <div className="font-medium">Selesai</div>
                                                <div className="text-sm text-gray-600">{formatDate(withdrawal.completed_at)}</div>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>

                            {/* Financial Details */}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                                <div className="bg-blue-50 rounded-lg p-6">
                                    <h3 className="text-lg font-semibold text-blue-900 mb-4">💵 Rincian Keuangan</h3>
                                    <div className="space-y-3">
                                        <div className="flex justify-between">
                                            <span className="text-blue-700">Jumlah Penarikan:</span>
                                            <span className="font-medium text-gray-800">{formatCurrency(withdrawal.amount)}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-blue-700">Biaya Admin:</span>
                                            <span className="font-medium text-red-600">-{formatCurrency(withdrawal.fee_amount)}</span>
                                        </div>
                                        <hr className="border-blue-200" />
                                        <div className="flex justify-between font-bold">
                                            <span className="text-blue-800">Yang Anda Terima:</span>
                                            <span className="text-green-600">{formatCurrency(withdrawal.net_amount)}</span>
                                        </div>
                                    </div>
                                </div>

                                <div className="bg-gray-50 bg-white/10 rounded-lg p-6">
                                    <h3 className="text-lg font-semibold text-white mb-4">🏦 Informasi Akun</h3>
                                    <div className="space-y-3">
                                        <div>
                                            <span className="text-sm text-gray-200">Metode:</span>
                                            <div className="font-medium text-gray-200">{getMethodDisplay(withdrawal.method)}</div>
                                        </div>
                                        <div>
                                            <span className="text-sm text-gray-200">Nama Pemilik:</span>
                                            <div className="font-medium text-gray-200">{withdrawal.account_info.account_name}</div>
                                        </div>
                                        {withdrawal.method === 'bank_transfer' && (
                                            <>
                                                <div>
                                                    <span className="text-sm text-gray-200">Bank:</span>
                                                    <div className="font-medium text-gray-200">{withdrawal.account_info.bank_name}</div>
                                                </div>
                                                <div>
                                                    <span className="text-sm text-gray-200">Nomor Rekening:</span>
                                                    <div className="font-medium text-gray-200">{withdrawal.account_info.account_number}</div>
                                                </div>
                                            </>
                                        )}
                                        {withdrawal.method === 'e_wallet' && (
                                            <>
                                                <div>
                                                    <span className="text-sm text-gray-200">E-Wallet:</span>
                                                    <div className="font-medium capitalize text-gray-200">{withdrawal.account_info.wallet_type}</div>
                                                </div>
                                                <div>
                                                    <span className="text-sm text-gray-200">Nomor Telepon:</span>
                                                    <div className="font-medium text-gray-200">{withdrawal.account_info.phone_number}</div>
                                                </div>
                                            </>
                                        )}
                                    </div>
                                </div>
                            </div>

                            {/* Reference Number */}
                            {withdrawal.reference_number && (
                                <div className="bg-green-50 rounded-lg p-4 mb-8">
                                    <h4 className="text-sm font-medium text-green-800 mb-2">📋 Nomor Referensi</h4>
                                    <div className="text-green-700 font-mono">{withdrawal.reference_number}</div>
                                    <p className="text-sm text-green-600 mt-1">
                                        Simpan nomor ini sebagai bukti transaksi
                                    </p>
                                </div>
                            )}

                            {/* Notes */}
                            {withdrawal.notes && (
                                <div className="bg-yellow-50 rounded-lg p-4 mb-8">
                                    <h4 className="text-sm font-medium text-yellow-800 mb-2">📝 Catatan</h4>
                                    <div className="text-yellow-700">{withdrawal.notes}</div>
                                </div>
                            )}

                            {/* Actions */}
                            <div className="flex justify-end space-x-4">
                                {(withdrawal.status === 'pending' || withdrawal.status === 'approved') && (
                                    <Link
                                        href={`/withdrawals/${withdrawal.id}/cancel`}
                                        method="patch"
                                        as="button"
                                        className="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                        onClick={(e) => {
                                            if (!confirm('Apakah Anda yakin ingin membatalkan penarikan ini?')) {
                                                e.preventDefault();
                                            }
                                        }}
                                    >
                                        Batalkan Penarikan
                                    </Link>
                                )}
                                <Link
                                    href="/withdrawals"
                                    className="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
                                >
                                    Kembali
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
