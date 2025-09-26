import { Head, Link, useForm } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Campaign, Withdrawal } from '@/types';

interface Props {
    campaign: Campaign & { withdrawals: Withdrawal[] };
}

interface WithdrawalForm {
    amount: string;
    method: string;
    account_info: {
        account_name: string;
        bank_name?: string;
        account_number?: string;
        wallet_type?: string;
        phone_number?: string;
    };
}

export default function WithdrawalsCreate({ campaign }: Props) {
    const [calculatedFee, setCalculatedFee] = useState(0);
    const [netAmount, setNetAmount] = useState(0);

    const { data, setData, post, processing, errors } = useForm<WithdrawalForm>({
        amount: '',
        method: 'bank_transfer',
        account_info: {
            account_name: '',
            bank_name: '',
            account_number: '',
            wallet_type: 'gopay',
            phone_number: '',
        }
    });

    // Calculate fees and net amount (amount already in rupiah)
    const calculateFees = (amount: number) => {
        const feePercentage = 0.025; // 2.5%
        const fixedFee = 2500; // Rp 2,500
        const percentageFee = amount * feePercentage;
        const totalFee = percentageFee + fixedFee;
        return {
            percentageFee,
            fixedFee,
            totalFee,
            netAmount: amount - totalFee
        };
    };

    useEffect(() => {
        const amount = parseFloat(data.amount) || 0;
        if (amount > 0) {
            const fees = calculateFees(amount);
            setCalculatedFee(fees.totalFee);
            setNetAmount(fees.netAmount);
        } else {
            setCalculatedFee(0);
            setNetAmount(0);
        }
    }, [data.amount]);

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(amount);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(`/campaigns/${campaign.id}/withdrawals`);
    };

    const availableAmount = campaign.collected_amount; // Convert from cents to rupiah

    return (
        <AppLayout>
            <Head title={`Ajukan Penarikan - ${campaign.title}`} />

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
                                <h2 className="text-2xl font-bold text-gray-100">💰 Ajukan Penarikan Dana</h2>
                                <p className="text-gray-400 mt-1">Tarik dana dari kampanye "{campaign.title}"</p>
                            </div>

                            {/* Campaign Info */}
                            <div className="bg-gray-700 rounded-lg p-6 mb-8">
                                <h3 className="text-lg font-semibold text-gray-100 mb-4">Informasi Kampanye</h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <span className="text-sm text-gray-100">Dana Terkumpul:</span>
                                        <div className="text-xl font-bold text-green-600">
                                            {formatCurrency(availableAmount)}
                                        </div>
                                    </div>
                                    <div>
                                        <span className="text-sm text-gray-100">Target Kampanye:</span>
                                        <div className="text-lg font-medium text-gray-100">
                                            {formatCurrency(campaign.target_amount)}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Withdrawal Form */}
                            <form onSubmit={handleSubmit} className="space-y-6">
                                {/* Amount */}
                                <div>
                                    <label htmlFor="amount" className="block text-sm font-medium text-gray-100 mb-2">
                                        Jumlah Penarikan *
                                    </label>
                                    <div className="relative">
                                        <span className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                        <input
                                            type="number"
                                            id="amount"
                                            value={data.amount}
                                            onChange={(e) => setData('amount', e.target.value)}
                                            className="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500"
                                            placeholder="0"
                                            min="50000"
                                            max={availableAmount}
                                            step="1000"
                                        />
                                    </div>
                                    {errors.amount && <p className="mt-1 text-sm text-red-600">{errors.amount}</p>}
                                    <p className="mt-1 text-sm text-gray-400">
                                        Minimum penarikan: Rp 50.000 | Maksimum: {formatCurrency(availableAmount)}
                                    </p>
                                </div>

                                {/* Fee Calculation */}
                                {data.amount && (
                                    <div className="bg-yellow-50 rounded-lg p-4">
                                        <h4 className="text-sm font-medium text-yellow-800 mb-3">Rincian Biaya</h4>
                                        <div className="space-y-2 text-sm">
                                            <div className="flex justify-between">
                                                <span className="text-yellow-700">Jumlah Penarikan:</span>
                                                <span className="font-medium">{formatCurrency(parseFloat(data.amount) || 0)}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-yellow-700">Biaya Admin (2.5% + Rp 2.500):</span>
                                                <span className="font-medium text-red-600">-{formatCurrency(calculatedFee)}</span>
                                            </div>
                                            <hr className="border-yellow-200" />
                                            <div className="flex justify-between font-bold">
                                                <span className="text-yellow-800">Yang Anda Terima:</span>
                                                <span className="text-green-600">{formatCurrency(netAmount)}</span>
                                            </div>
                                        </div>
                                    </div>
                                )}

                                {/* Withdrawal Method */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-100 mb-3">
                                        Metode Penarikan *
                                    </label>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <label className="relative">
                                            <input
                                                type="radio"
                                                name="method"
                                                value="bank_transfer"
                                                checked={data.method === 'bank_transfer'}
                                                onChange={(e) => setData('method', e.target.value)}
                                                className="sr-only"
                                            />
                                            <div className={`p-4 border-2 rounded-lg cursor-pointer transition-colors ${
                                                data.method === 'bank_transfer' 
                                                    ? 'border-blue-500 bg-white/10' 
                                                    : 'border-gray-200 hover:border-gray-300'
                                            }`}>
                                                <div className="flex items-center space-x-3">
                                                    <div className="text-2xl">🏦</div>
                                                    <div>
                                                        <div className="font-medium">Transfer Bank</div>
                                                        <div className="text-sm text-gray-400">Ke rekening bank Anda</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                        <label className="relative">
                                            <input
                                                type="radio"
                                                name="method"
                                                value="e_wallet"
                                                checked={data.method === 'e_wallet'}
                                                onChange={(e) => setData('method', e.target.value)}
                                                className="sr-only"
                                            />
                                            <div className={`p-4 border-2 rounded-lg cursor-pointer transition-colors ${
                                                data.method === 'e_wallet' 
                                                    ? 'border-blue-500 bg-white/10' 
                                                    : 'border-gray-200 hover:border-gray-300'
                                            }`}>
                                                <div className="flex items-center space-x-3">
                                                    <div className="text-2xl">📱</div>
                                                    <div>
                                                        <div className="font-medium">E-Wallet</div>
                                                        <div className="text-sm text-gray-400">GoPay, OVO, DANA, LinkAja</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    {errors.method && <p className="mt-1 text-sm text-red-600">{errors.method}</p>}
                                </div>

                                {/* Account Information */}
                                <div className="space-y-4">
                                    <h3 className="text-lg font-medium text-gray-100">Informasi Akun</h3>
                                    
                                    {/* Account Name */}
                                    <div>
                                        <label htmlFor="account_name" className="block text-sm font-medium text-gray-100 mb-2">
                                            Nama Pemilik Akun *
                                        </label>
                                        <input
                                            type="text"
                                            id="account_name"
                                            value={data.account_info.account_name}
                                            onChange={(e) => setData('account_info', {
                                                ...data.account_info,
                                                account_name: e.target.value
                                            })}
                                            className="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Masukkan nama sesuai rekening/akun"
                                        />
                                        {errors['account_info.account_name'] && (
                                            <p className="mt-1 text-sm text-red-600">{errors['account_info.account_name']}</p>
                                        )}
                                    </div>

                                    {/* Bank Transfer Fields */}
                                    {data.method === 'bank_transfer' && (
                                        <>
                                            <div>
                                                <label htmlFor="bank_name" className="block text-sm font-medium text-gray-100 mb-2">
                                                    Nama Bank *
                                                </label>
                                                <select
                                                    id="bank_name"
                                                    value={data.account_info.bank_name || ''}
                                                    onChange={(e) => setData('account_info', {
                                                        ...data.account_info,
                                                        bank_name: e.target.value
                                                    })}
                                                    className="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                                >
                                                    <option className='bg-gray-700' value="">Pilih Bank</option>
                                                    <option className='bg-gray-700' value="BCA">Bank Central Asia (BCA)</option>
                                                    <option className='bg-gray-700' value="BRI">Bank Rakyat Indonesia (BRI)</option>
                                                    <option className='bg-gray-700' value="BNI">Bank Negara Indonesia (BNI)</option>
                                                    <option className='bg-gray-700' value="Mandiri">Bank Mandiri</option>
                                                    <option className='bg-gray-700' value="CIMB">CIMB Niaga</option>
                                                    <option className='bg-gray-700' value="Danamon">Bank Danamon</option>
                                                    <option className='bg-gray-700' value="Permata">Bank Permata</option>
                                                    <option className='bg-gray-700' value="BSI">Bank Syariah Indonesia (BSI)</option>
                                                </select>
                                                {errors['account_info.bank_name'] && (
                                                    <p className="mt-1 text-sm text-red-600">{errors['account_info.bank_name']}</p>
                                                )}
                                            </div>
                                            <div>
                                                <label htmlFor="account_number" className="block text-sm font-medium text-gray-100 mb-2">
                                                    Nomor Rekening *
                                                </label>
                                                <input
                                                    type="text"
                                                    id="account_number"
                                                    value={data.account_info.account_number || ''}
                                                    onChange={(e) => setData('account_info', {
                                                        ...data.account_info,
                                                        account_number: e.target.value
                                                    })}
                                                    className="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                                    placeholder="Masukkan nomor rekening"
                                                />
                                                {errors['account_info.account_number'] && (
                                                    <p className="mt-1 text-sm text-red-600">{errors['account_info.account_number']}</p>
                                                )}
                                            </div>
                                        </>
                                    )}

                                    {/* E-Wallet Fields */}
                                    {data.method === 'e_wallet' && (
                                        <>
                                            <div>
                                                <label htmlFor="wallet_type" className="block text-sm font-medium text-gray-100 mb-2">
                                                    Jenis E-Wallet *
                                                </label>
                                                <select
                                                    id="wallet_type"
                                                    value={data.account_info.wallet_type || ''}
                                                    onChange={(e) => setData('account_info', {
                                                        ...data.account_info,
                                                        wallet_type: e.target.value
                                                    })}
                                                    className="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                                >
                                                    <option className='bg-gray-700' value="gopay">GoPay</option>
                                                    <option className='bg-gray-700' value="ovo">OVO</option>
                                                    <option className='bg-gray-700' value="dana">DANA</option>
                                                    <option className='bg-gray-700' value="linkaja">LinkAja</option>
                                                </select>
                                                {errors['account_info.wallet_type'] && (
                                                    <p className="mt-1 text-sm text-red-600">{errors['account_info.wallet_type']}</p>
                                                )}
                                            </div>
                                            <div>
                                                <label htmlFor="phone_number" className="block text-sm font-medium text-gray-100 mb-2">
                                                    Nomor Telepon *
                                                </label>
                                                <input
                                                    type="tel"
                                                    id="phone_number"
                                                    value={data.account_info.phone_number || ''}
                                                    onChange={(e) => setData('account_info', {
                                                        ...data.account_info,
                                                        phone_number: e.target.value
                                                    })}
                                                    className="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                                    placeholder="08xxxxxxxxxx"
                                                />
                                                {errors['account_info.phone_number'] && (
                                                    <p className="mt-1 text-sm text-red-600">{errors['account_info.phone_number']}</p>
                                                )}
                                            </div>
                                        </>
                                    )}
                                </div>

                                {/* Important Notes */}
                                <div className="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                    <h4 className="text-sm font-medium text-amber-800 mb-2">⚠️ Penting untuk Diperhatikan:</h4>
                                    <ul className="text-sm text-amber-700 space-y-1">
                                        <li>• Pastikan nama pemilik akun sesuai dengan identitas Anda</li>
                                        <li>• Penarikan akan diproses dalam 1-3 hari kerja setelah disetujui</li>
                                        <li>• Biaya admin 2.5% + Rp 2.500 akan dipotong dari jumlah penarikan</li>
                                        <li>• Anda akan menerima notifikasi email untuk setiap update status</li>
                                        <li>• Penarikan yang sudah disetujui tidak dapat dibatalkan</li>
                                    </ul>
                                </div>

                                {/* Submit Button */}
                                <div className="flex justify-end space-x-4">
                                    <Link
                                        href="/withdrawals"
                                        className="px-6 py-2 border border-gray-300 text-gray-100 hover:text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        Batal
                                    </Link>
                                    <button
                                        type="submit"
                                        disabled={processing || !data.amount || parseFloat(data.amount) < 50000}
                                        className="px-6 py-2 cursor-pointer bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        {processing ? 'Memproses...' : 'Ajukan Penarikan'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
