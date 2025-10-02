import { Campaign, PaymentChannel } from '@/types';
import { useState } from 'react';
import PublicLayout from '@/layouts/PublicLayout';
import toast from 'react-hot-toast';
import { useForm } from '@inertiajs/react';
interface PaymentProvider {
    id: number;
    name: string;
    code: string;
    channels: PaymentChannel[];
    has_channels: boolean;
}

interface Props {
    campaign: Campaign;
    paymentProviders: PaymentProvider[];
}

export default function DonationCreate({ campaign, paymentProviders }: Props) {
    const [selectedAmount, setSelectedAmount] = useState<number | null>(null);
    const [customAmount, setCustomAmount] = useState('');
    const [selectedProvider, setSelectedProvider] = useState<PaymentProvider | null>(null);
    const [loading, setLoading] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        amount: 0,
        donor_name: '',
        donor_email: '',
        donor_phone: '',
        message: '',
        is_anonymous: false,
        payment_provider: '',
        payment_channel: '',
    });

    const progressPercentage = campaign.target_amount > 0
        ? (campaign.collected_amount / campaign.target_amount) * 100
        : 0;

    // Calculate remaining amount needed to reach target
    const remainingAmount = Math.max(0, campaign.target_amount - campaign.collected_amount);
    
    const predefinedAmounts = [50000, 100000, 200000, 500000, 1000000];

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(amount);
    };

    const handleAmountSelect = (amount: number) => {
        // Check if campaign is already completed
        if (remainingAmount <= 0) {
            toast.error('Kampanye ini sudah mencapai target donasi! 🎉', {
                duration: 4000,
                icon: '✅',
            });
            return;
        }
        
        // Check if amount exceeds remaining target
        if (amount > remainingAmount) {
            toast.error(`Nominal donasi melebihi sisa target! Sisa yang dibutuhkan: ${formatCurrency(remainingAmount)} 💰`, {
                duration: 5000,
                icon: '⚠️',
            });
            return;
        }
        
        setSelectedAmount(amount);
        setCustomAmount('');
        setData('amount', amount);
        
        // Success feedback
        toast.success(`Nominal ${formatCurrency(amount)} dipilih! 💝`, {
            duration: 2000,
            icon: '✨',
        });
    };

    const handleCustomAmountChange = (value: string) => {
        setCustomAmount(value);
        setSelectedAmount(null);
        const numValue = parseInt(value.replace(/\D/g, ''));
        
        // Validate custom amount
        if (numValue > 0) {
            if (remainingAmount <= 0) {
                toast.error('Kampanye ini sudah mencapai target donasi! 🎉', {
                    duration: 4000,
                    icon: '✅',
                });
                setCustomAmount('');
                setData('amount', 0);
                return;
            }
            
            if (numValue > remainingAmount) {
                toast.error(`Nominal donasi melebihi sisa target! Sisa yang dibutuhkan: ${formatCurrency(remainingAmount)} 💰`, {
                    duration: 5000,
                    icon: '⚠️',
                });
                setCustomAmount('');
                setData('amount', 0);
                return;
            }
        }
        
        setData('amount', numValue || 0);
    };

    const handleProviderSelect = (provider: PaymentProvider) => {
        setSelectedProvider(provider);
        setData('payment_provider', provider.id.toString());
        setData('payment_channel', ''); // Reset channel selection
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        
        // Final validation before submit
        if (remainingAmount <= 0) {
            toast.error('Kampanye ini sudah mencapai target donasi! 🎉', {
                duration: 4000,
                icon: '✅',
            });
            return;
        }
        
        if (data.amount > remainingAmount) {
            toast.error(`Nominal donasi melebihi sisa target! Sisa yang dibutuhkan: ${formatCurrency(remainingAmount)} 💰`, {
                duration: 5000,
                icon: '⚠️',
            });
            return;
        }
        
        if (data.amount <= 0) {
            toast.error('Silakan masukkan nominal donasi yang valid! 💸', {
                duration: 4000,
                icon: '❌',
            });
            return;
        }
        
        // Show processing toast
        toast.loading('Memproses donasi Anda... 🔄', {
            duration: 2000,
        });
        
        setLoading(true);
        
        // For Midtrans, we need to handle the response differently
        if (selectedProvider?.code === 'midtrans') {
            try {
                const response = await fetch(`/campaigns/${campaign.slug}/donate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data),
                });

                const result = await response.json();

                if (result.success && result.snap_token) {
                    toast.success('Donasi berhasil dibuat! Mengarahkan ke halaman pembayaran... 🎉', {
                        duration: 3000,
                        icon: '💝',
                    });
                    window.open(result.redirect_url, '_blank');
                    window.location.href = `/donations/${result.ref_id}/show`;
                    setLoading(false);
                } else {
                    console.error('Invalid response from server:', result);
                    toast.error('Terjadi kesalahan saat memproses donasi. Silakan coba lagi! 😔', {
                        duration: 4000,
                        icon: '❌',
                    });
                    setLoading(false);
                }
            } catch (error) {
                console.error('Request failed:', error);
                toast.error('Koneksi bermasalah. Silakan periksa internet Anda dan coba lagi! 🌐', {
                    duration: 4000,
                    icon: '⚠️',
                });
                setLoading(false);
            }
        } else {
            // For other providers (Tokopay), use normal form submission
            post(`/campaigns/${campaign.slug}/donate`);
        }
    };


    const showLoading = () => {
        return (
            <div className="fixed inset-0 bg-gradient-to-br from-blue-900/20 to-purple-900/20 backdrop-blur-sm flex items-center justify-center z-50">
                <div className="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-8 max-w-sm mx-4 text-center border border-white/20">
                    {/* Animated Heart Icon */}
                    <div className="relative mb-6">
                        <div className="absolute inset-0 animate-ping">
                            <svg className="w-16 h-16 mx-auto text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clipRule="evenodd" />
                            </svg>
                        </div>
                        <svg className="w-16 h-16 mx-auto text-red-500 relative z-10 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                            <path fillRule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clipRule="evenodd" />
                        </svg>
                    </div>

                    {/* Loading Text with Animation */}
                    <div className="space-y-3">
                        <h3 className="text-xl font-bold text-gray-800 animate-bounce">
                            Memproses Donasi Anda
                        </h3>
                        <p className="text-gray-600 text-sm">
                            Sedang menyiapkan kebaikan untuk disalurkan...
                        </p>
                        
                        {/* Animated Progress Dots */}
                        <div className="flex justify-center space-x-1 mt-4">
                            <div className="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style={{animationDelay: '0ms'}}></div>
                            <div className="w-2 h-2 bg-green-500 rounded-full animate-bounce" style={{animationDelay: '150ms'}}></div>
                            <div className="w-2 h-2 bg-yellow-500 rounded-full animate-bounce" style={{animationDelay: '300ms'}}></div>
                            <div className="w-2 h-2 bg-red-500 rounded-full animate-bounce" style={{animationDelay: '450ms'}}></div>
                            <div className="w-2 h-2 bg-purple-500 rounded-full animate-bounce" style={{animationDelay: '600ms'}}></div>
                        </div>

                        {/* Cute Message */}
                        <div className="mt-4 p-3 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-100">
                            <p className="text-xs text-gray-600 italic">
                                "Setiap donasi adalah harapan baru untuk sesama" 💝
                            </p>
                        </div>

                        {/* Animated Progress Bar */}
                        <div className="mt-4">
                            <div className="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div className="h-full bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-full animate-pulse" 
                                     style={{
                                         width: '70%',
                                         animation: 'loading-bar 2s ease-in-out infinite alternate'
                                     }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Floating Hearts Animation */}
                <div className="absolute inset-0 pointer-events-none overflow-hidden">
                    {[...Array(6)].map((_, i) => (
                        <div
                            key={i}
                            className="absolute animate-float"
                            style={{
                                left: `${Math.random() * 100}%`,
                                animationDelay: `${i * 0.5}s`,
                                animationDuration: `${3 + Math.random() * 2}s`
                            }}
                        >
                            <svg className="w-4 h-4 text-red-300 opacity-60" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clipRule="evenodd" />
                            </svg>
                        </div>
                    ))}
                </div>

                <style>{`
                    @keyframes loading-bar {
                        0% { width: 30%; }
                        50% { width: 70%; }
                        100% { width: 90%; }
                    }
                    
                    @keyframes float {
                        0% {
                            transform: translateY(100vh) rotate(0deg);
                            opacity: 0;
                        }
                        10% {
                            opacity: 1;
                        }
                        90% {
                            opacity: 1;
                        }
                        100% {
                            transform: translateY(-100px) rotate(360deg);
                            opacity: 0;
                        }
                    }
                    
                    .animate-float {
                        animation: float linear infinite;
                    }
                `}</style>
            </div>
        )
    }


    return (
        <PublicLayout title={`Donasi untuk ${campaign.title}`} currentPage="campaigns">
            {loading && showLoading()}

            <div className="py-8">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        {/* Campaign Info */}
                        <div className="bg-gray-800 rounded-lg shadow-md p-6 lg:sticky lg:top-8 lg:h-fit">
                            <h2 className="text-xl font-semibold mb-4">Informasi Kampanye</h2>
                            
                            {campaign.featured_image && (
                                <img
                                    src={`/storage/campaigns/${campaign.featured_image}`}
                                    alt={campaign.title}
                                    className="w-full h-48 object-cover rounded-lg mb-4"
                                />
                            )}

                            <h3 className="font-semibold text-lg mb-2">{campaign.title}</h3>
                            <p className="text-gray-600 mb-4">{campaign.short_desc}</p>

                            <div className="mb-4">
                                <div className="flex justify-between text-sm mb-1">
                                    <span>Terkumpul</span>
                                    <span>{progressPercentage.toFixed(1)}%</span>
                                </div>
                                <div className="w-full bg-gray-200 rounded-full h-2">
                                    <div
                                        className="bg-green-600 h-2 rounded-full"
                                        style={{ width: `${Math.min(progressPercentage, 100)}%` }}
                                    ></div>
                                </div>
                                <div className="flex justify-between mt-2">
                                    <span className="font-semibold text-green-600">
                                        {formatCurrency(campaign.collected_amount)}
                                    </span>
                                    <span className="text-gray-500">
                                        dari {formatCurrency(campaign.target_amount)}
                                    </span>
                                </div>
                                {remainingAmount > 0 && (
                                    <div className="mt-2 p-2 bg-blue-50 rounded-lg">
                                        <p className="text-sm text-blue-700">
                                            <span className="font-medium">Sisa target:</span> {formatCurrency(remainingAmount)}
                                        </p>
                                    </div>
                                )}
                                {remainingAmount <= 0 && (
                                    <div className="mt-2 p-2 bg-green-50 rounded-lg">
                                        <p className="text-sm text-green-700 font-medium">
                                            🎉 Target kampanye sudah tercapai!
                                        </p>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Donation Form */}
                        <div className="bg-gray-800 rounded-lg shadow-md p-6">
                            <h2 className="text-xl font-semibold mb-6">Form Donasi</h2>

                            <form onSubmit={handleSubmit} className="space-y-6">
                                {/* Amount Selection */}
                                <div>
                                    <label className="block text-sm font-medium text-white mb-3">
                                        Pilih Nominal Donasi
                                    </label>
                                    <div className="grid grid-cols-2 gap-3 mb-4">
                                        {remainingAmount > 0 ? (
                                            <>
                                                {predefinedAmounts
                                                    .filter(amount => amount <= remainingAmount)
                                                    .map((amount) => (
                                                    <button
                                                        key={amount}
                                                        type="button"
                                                        onClick={() => handleAmountSelect(amount)}
                                                        className={`p-3 rounded-lg border text-center ${
                                                            selectedAmount === amount
                                                                ? 'border-blue-500 bg-blue-50 text-blue-700'
                                                                : 'border-gray-300 hover:border-gray-400'
                                                        }`}
                                                    >
                                                        {formatCurrency(amount)}
                                                    </button>
                                                ))}
                                                {remainingAmount < Math.min(...predefinedAmounts) && (
                                                    <button
                                                        type="button"
                                                        onClick={() => handleAmountSelect(remainingAmount)}
                                                        className={`p-3 rounded-lg border text-center ${
                                                            selectedAmount === remainingAmount
                                                                ? 'border-blue-500 bg-blue-50 text-blue-700'
                                                                : 'border-green-300 bg-green-50 hover:border-green-400'
                                                        }`}
                                                    >
                                                        {formatCurrency(remainingAmount)}
                                                        <div className="text-xs text-green-600 mt-1">Sisa Target</div>
                                                    </button>
                                                )}
                                                {predefinedAmounts.filter(amount => amount <= remainingAmount).length === 0 && remainingAmount >= Math.min(...predefinedAmounts) && (
                                                    <div className="col-span-2 p-3 text-center text-gray-500 text-sm">
                                                        Gunakan input "Nominal lainnya" untuk memasukkan jumlah donasi
                                                    </div>
                                                )}
                                            </>
                                        ) : (
                                            <div className="col-span-2 p-3 text-center text-gray-500 text-sm">
                                                Kampanye ini sudah mencapai target donasi
                                            </div>
                                        )}
                                    </div>
                                    
                                    <input
                                        type="text"
                                        placeholder={remainingAmount > 0 ? `Nominal lainnya (Maks: ${formatCurrency(remainingAmount)})` : "Kampanye sudah mencapai target"}
                                        value={customAmount}
                                        onChange={(e) => handleCustomAmountChange(e.target.value)}
                                        disabled={remainingAmount <= 0}
                                        className={`w-full p-3 border rounded-lg focus:ring-blue-500 focus:border-blue-500 ${
                                            remainingAmount <= 0 
                                                ? 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed' 
                                                : 'border-gray-300'
                                        }`}
                                    />
                                    {errors.amount && (
                                        <p className="mt-1 text-sm text-red-600">{errors.amount}</p>
                                    )}
                                </div>

                                {/* Donor Information */}
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-sm font-medium text-white mb-1">
                                            Nama Lengkap *
                                        </label>
                                        <input
                                            type="text"
                                            value={data.donor_name}
                                            onChange={(e) => setData('donor_name', e.target.value)}
                                            className="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                            required
                                        />
                                        {errors.donor_name && (
                                            <p className="mt-1 text-sm text-red-600">{errors.donor_name}</p>
                                        )}
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-white mb-1">
                                            Email *
                                        </label>
                                        <input
                                            type="email"
                                            value={data.donor_email}
                                            onChange={(e) => setData('donor_email', e.target.value)}
                                            className="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                            required
                                        />
                                        {errors.donor_email && (
                                            <p className="mt-1 text-sm text-red-600">{errors.donor_email}</p>
                                        )}
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-white mb-1">
                                        Nomor Telepon
                                    </label>
                                    <input
                                        type="tel"
                                        value={data.donor_phone}
                                        onChange={(e) => setData('donor_phone', e.target.value)}
                                        className="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-white mb-1">
                                        Pesan (Opsional)
                                    </label>
                                    <textarea
                                        value={data.message}
                                        onChange={(e) => setData('message', e.target.value)}
                                        rows={3}
                                        className="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Tulis pesan dukungan Anda..."
                                    />
                                </div>

                                <div className="flex items-center">
                                    <input
                                        type="checkbox"
                                        id="is_anonymous"
                                        checked={data.is_anonymous}
                                        onChange={(e) => setData('is_anonymous', e.target.checked)}
                                        className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    />
                                    <label htmlFor="is_anonymous" className="ml-2 text-sm text-white">
                                        Donasi sebagai anonim
                                    </label>
                                </div>

                                {/* Payment Provider Selection */}
                                <div>
                                    <label className="block text-sm font-medium text-white mb-3">
                                        Pilih Provider Pembayaran
                                    </label>
                                    <div className="grid grid-cols-1 gap-3 mb-4">
                                        {paymentProviders.map((provider) => (
                                            <label
                                                key={provider.id}
                                                className={`flex items-center p-4 border rounded-lg cursor-pointer transition-colors duration-200 ${
                                                    data.payment_provider === provider.id.toString()
                                                        ? 'border-blue-500 bg-blue-50 text-blue-700'
                                                        : 'border-gray-300 hover:bg-gray-50 hover:text-gray-700'
                                                }`}
                                            >
                                                <input
                                                    type="radio"
                                                    name="payment_provider"
                                                    value={provider.id}
                                                    checked={data.payment_provider === provider.id.toString()}
                                                    onChange={() => handleProviderSelect(provider)}
                                                    className="sr-only"
                                                />
                                                <div className="flex-1">
                                                    <div className="font-medium">{provider.name}</div>
                                                    <div className="text-sm">
                                                        {provider.code === 'midtrans' 
                                                            ? 'Semua metode pembayaran tersedia (Credit Card, VA, E-Wallet, dll)'
                                                            : `${provider.channels.length} metode pembayaran tersedia`
                                                        }
                                                    </div>
                                                </div>
                                            </label>
                                        ))}
                                    </div>
                                    {errors.payment_provider && (
                                        <p className="mt-1 text-sm text-red-600">{errors.payment_provider}</p>
                                    )}
                                </div>

                                {/* Payment Channel Selection (Only for providers with channels) */}
                                {selectedProvider && selectedProvider.has_channels && (
                                    <div>
                                        <label className="block text-sm font-medium text-white mb-3">
                                            Pilih Metode Pembayaran
                                        </label>
                                        <div className="grid grid-cols-2 gap-3">
                                            {selectedProvider.channels.map((channel) => (
                                                <label
                                                    key={channel.id}
                                                    className={`flex items-center p-3 border rounded-lg cursor-pointer transition-colors duration-200 ${
                                                        data.payment_channel === channel.id.toString()
                                                            ? 'border-blue-500 bg-gray-500 text-white'
                                                            : 'border-gray-300 hover:bg-gray-600 hover:text-white'
                                                    }`}
                                                >
                                                    <input
                                                        type="radio"
                                                        name="payment_channel"
                                                        value={channel.id}
                                                        checked={data.payment_channel === channel.id.toString()}
                                                        onChange={(e) => setData('payment_channel', e.target.value)}
                                                        className="sr-only"
                                                    />
                                                    <div className="flex-1">
                                                        <span className="text-sm font-medium">{channel.name}</span>
                                                        {(channel.fee_fixed > 0 || channel.fee_percentage > 0) && (
                                                            <div className="text-xs text-gray-400 mt-1">
                                                                Fee: {channel.fee_fixed > 0 && `Rp ${channel.fee_fixed.toLocaleString()}`}
                                                                {channel.fee_fixed > 0 && channel.fee_percentage > 0 && ' + '}
                                                                {channel.fee_percentage > 0 && `${channel.fee_percentage}%`}
                                                            </div>
                                                        )}
                                                    </div>
                                                </label>
                                            ))}
                                        </div>
                                        {errors.payment_channel && (
                                            <p className="mt-1 text-sm text-red-600">{errors.payment_channel}</p>
                                        )}
                                    </div>
                                )}

                                <button
                                    type="submit"
                                    disabled={processing || data.amount < 10000}
                                    className="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-semibold py-3 px-6 rounded-lg"
                                >
                                    {processing ? 'Memproses...' : `Donasi ${formatCurrency(data.amount)}`}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
