import { Link } from '@inertiajs/react';
import { useState } from 'react';
import PublicLayout from '@/layouts/PublicLayout';

export default function FAQ() {
    const [openItems, setOpenItems] = useState<number[]>([]);

    const toggleItem = (index: number) => {
        setOpenItems(prev => 
            prev.includes(index) 
                ? prev.filter(i => i !== index)
                : [...prev, index]
        );
    };

    const faqData = [
        {
            category: "Umum",
            questions: [
                {
                    question: "Apa itu DonationPlatform?",
                    answer: "DonationPlatform adalah platform donasi online yang menghubungkan donatur dengan penggalang dana untuk berbagai tujuan kebaikan seperti bantuan medis, pendidikan, bencana alam, dan lainnya."
                },
                {
                    question: "Apakah platform ini gratis digunakan?",
                    answer: "Ya, platform ini gratis untuk digunakan oleh donatur. Untuk penggalang dana, kami mengenakan biaya administrasi kecil untuk menutupi biaya operasional platform."
                },
                {
                    question: "Bagaimana cara kerja platform ini?",
                    answer: "Penggalang dana membuat kampanye dengan detail yang jelas, kami verifikasi kampanye tersebut, kemudian donatur dapat memberikan donasi. Dana yang terkumpul akan disalurkan kepada penggalang dana setelah dikurangi biaya administrasi."
                }
            ]
        },
        {
            category: "Donasi",
            questions: [
                {
                    question: "Berapa minimal donasi yang bisa diberikan?",
                    answer: "Minimal donasi adalah Rp 10.000. Tidak ada batas maksimal untuk donasi."
                },
                {
                    question: "Metode pembayaran apa saja yang tersedia?",
                    answer: "Kami menerima transfer bank (BCA, Mandiri, BRI, BNI), e-wallet (GoPay, OVO, DANA, ShopeePay), dan virtual account."
                },
                {
                    question: "Apakah bisa berdonasi secara anonim?",
                    answer: "Ya, Anda dapat memilih untuk berdonasi secara anonim. Nama Anda tidak akan ditampilkan di halaman kampanye."
                },
                {
                    question: "Bisakah donasi dibatalkan setelah pembayaran?",
                    answer: "Donasi yang sudah diproses tidak dapat dibatalkan. Pastikan Anda telah membaca detail kampanye dengan teliti sebelum berdonasi."
                }
            ]
        },
        {
            category: "Kampanye",
            questions: [
                {
                    question: "Siapa saja yang bisa membuat kampanye?",
                    answer: "Siapa saja yang berusia minimal 17 tahun dan memiliki tujuan yang sah dapat membuat kampanye donasi di platform kami."
                },
                {
                    question: "Berapa lama proses verifikasi kampanye?",
                    answer: "Proses verifikasi biasanya memakan waktu 1-3 hari kerja. Kami akan meninjau kelengkapan dokumen dan keaslian kampanye."
                },
                {
                    question: "Apa saja yang tidak boleh dijadikan kampanye?",
                    answer: "Kampanye yang melanggar hukum, menyesatkan, mengandung ujaran kebencian, atau untuk kepentingan pribadi yang tidak jelas tidak diperbolehkan."
                },
                {
                    question: "Bagaimana cara menarik dana yang terkumpul?",
                    answer: "Dana dapat ditarik setelah kampanye mencapai minimal 50% dari target atau setelah 30 hari berjalan. Proses pencairan membutuhkan waktu 3-5 hari kerja."
                }
            ]
        },
        {
            category: "Keamanan",
            questions: [
                {
                    question: "Apakah data pribadi saya aman?",
                    answer: "Ya, kami menggunakan enkripsi SSL 256-bit dan mematuhi standar keamanan internasional untuk melindungi data pribadi Anda."
                },
                {
                    question: "Bagaimana cara melaporkan kampanye yang mencurigakan?",
                    answer: "Anda dapat melaporkan kampanye yang mencurigakan melalui tombol 'Laporkan' di halaman kampanye atau menghubungi tim support kami."
                },
                {
                    question: "Apa yang terjadi jika kampanye ternyata palsu?",
                    answer: "Jika terbukti kampanye palsu, kami akan mengembalikan dana kepada donatur dan memblokir akun penggalang dana tersebut."
                }
            ]
        },
        {
            category: "Teknis",
            questions: [
                {
                    question: "Mengapa pembayaran saya gagal?",
                    answer: "Pembayaran bisa gagal karena saldo tidak mencukupi, masalah jaringan, atau masalah teknis dari penyedia pembayaran. Coba lagi atau gunakan metode pembayaran lain."
                },
                {
                    question: "Bagaimana cara mengubah informasi profil?",
                    answer: "Masuk ke akun Anda, klik menu 'Profil' dan edit informasi yang ingin diubah. Jangan lupa klik 'Simpan' setelah selesai."
                },
                {
                    question: "Apakah ada aplikasi mobile?",
                    answer: "Saat ini kami belum memiliki aplikasi mobile, namun website kami sudah responsive dan dapat diakses dengan baik melalui browser mobile."
                }
            ]
        }
    ];

    return (
        <PublicLayout title="Frequently Asked Questions" currentPage="faq">

            <div className="py-12">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    {/* Hero Section */}
                    <div className="text-center mb-12">
                        <h1 className="text-4xl font-bold text-white mb-4">
                            Frequently Asked Questions
                        </h1>
                        <p className="text-xl text-gray-300 max-w-2xl mx-auto">
                            Temukan jawaban atas pertanyaan yang sering diajukan tentang platform donasi kami.
                        </p>
                    </div>

                    {/* Quick Search */}
                    <div className="mb-12">
                        <div className="bg-gray-800 rounded-lg p-6">
                            <h2 className="text-xl font-semibold text-white mb-4">Cari Pertanyaan</h2>
                            <input
                                type="text"
                                placeholder="Ketik kata kunci untuk mencari jawaban..."
                                className="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                    </div>

                    {/* FAQ Categories */}
                    <div className="space-y-8">
                        {faqData.map((category, categoryIndex) => (
                            <div key={categoryIndex} className="bg-gray-800 rounded-lg p-6">
                                <h2 className="text-2xl font-bold text-white mb-6 flex items-center">
                                    <span className="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                        {categoryIndex + 1}
                                    </span>
                                    {category.category}
                                </h2>
                                
                                <div className="space-y-4">
                                    {category.questions.map((faq, faqIndex) => {
                                        const globalIndex = categoryIndex * 100 + faqIndex;
                                        const isOpen = openItems.includes(globalIndex);
                                        
                                        return (
                                            <div key={faqIndex} className="border border-gray-700 rounded-lg">
                                                <button
                                                    onClick={() => toggleItem(globalIndex)}
                                                    className="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-700 transition-colors rounded-lg"
                                                >
                                                    <span className="text-white font-medium pr-4">
                                                        {faq.question}
                                                    </span>
                                                    <svg
                                                        className={`w-5 h-5 text-gray-400 transition-transform ${
                                                            isOpen ? 'rotate-180' : ''
                                                        }`}
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            strokeLinecap="round"
                                                            strokeLinejoin="round"
                                                            strokeWidth={2}
                                                            d="M19 9l-7 7-7-7"
                                                        />
                                                    </svg>
                                                </button>
                                                
                                                {isOpen && (
                                                    <div className="px-6 pb-4">
                                                        <div className="border-t border-gray-700 pt-4">
                                                            <p className="text-gray-300 leading-relaxed">
                                                                {faq.answer}
                                                            </p>
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Contact Support */}
                    <div className="mt-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-8 text-center">
                        <h2 className="text-2xl font-bold text-white mb-4">
                            Tidak Menemukan Jawaban yang Anda Cari?
                        </h2>
                        <p className="text-blue-100 mb-6">
                            Tim support kami siap membantu Anda dengan pertanyaan atau masalah apapun.
                        </p>
                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            <Link
                                href="/contact"
                                className="bg-white text-blue-600 hover:bg-gray-100 font-semibold py-3 px-6 rounded-lg transition-colors"
                            >
                                Hubungi Support
                            </Link>
                            <a
                                href="mailto:fahmiaksannugroho@gmail.com"
                                className="bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-600 font-semibold py-3 px-6 rounded-lg transition-colors"
                            >
                                Kirim Email
                            </a>
                        </div>
                    </div>

                    {/* Popular Topics */}
                    <div className="mt-12">
                        <h2 className="text-2xl font-bold text-white mb-6 text-center">Topik Populer</h2>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div className="bg-gray-800 rounded-lg p-6 text-center">
                                <div className="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                </div>
                                <h3 className="text-lg font-semibold text-white mb-2">Cara Berdonasi</h3>
                                <p className="text-gray-300 text-sm">
                                    Panduan lengkap untuk memberikan donasi dengan aman dan mudah.
                                </p>
                            </div>

                            <div className="bg-gray-800 rounded-lg p-6 text-center">
                                <div className="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <h3 className="text-lg font-semibold text-white mb-2">Keamanan</h3>
                                <p className="text-gray-300 text-sm">
                                    Informasi tentang keamanan data dan transaksi di platform kami.
                                </p>
                            </div>

                            <div className="bg-gray-800 rounded-lg p-6 text-center">
                                <div className="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <h3 className="text-lg font-semibold text-white mb-2">Buat Kampanye</h3>
                                <p className="text-gray-300 text-sm">
                                    Langkah-langkah untuk membuat dan mengelola kampanye donasi.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
