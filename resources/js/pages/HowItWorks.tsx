import { Link } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';

export default function HowItWorks() {
    return (
        <PublicLayout title="Cara Kerja Platform" currentPage="how-it-works">

            <div className="py-12">
                <div className="max-w-6xl mx-auto sm:px-6 lg:px-8">
                    {/* Hero Section */}
                    <div className="text-center mb-16">
                        <h1 className="text-4xl font-bold text-white mb-4">
                            Cara Kerja Platform Donasi
                        </h1>
                        <p className="text-xl text-gray-300 max-w-3xl mx-auto">
                            Pelajari bagaimana platform kami bekerja untuk menghubungkan kebaikan hati Anda 
                            dengan mereka yang membutuhkan bantuan.
                        </p>
                    </div>

                    {/* For Donors Section */}
                    <div className="mb-20">
                        <div className="text-center mb-12">
                            <h2 className="text-3xl font-bold text-white mb-4">Untuk Donatur</h2>
                            <p className="text-gray-300">Langkah mudah untuk berdonasi dan membantu sesama</p>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div className="text-center">
                                <div className="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <span className="text-2xl font-bold text-white">1</span>
                                </div>
                                <h3 className="text-xl font-semibold text-white mb-4">Jelajahi Kampanye</h3>
                                <p className="text-gray-300 mb-4">
                                    Temukan berbagai kampanye donasi yang tersedia. Filter berdasarkan kategori 
                                    atau cari kampanye yang sesuai dengan minat Anda.
                                </p>
                                <div className="bg-gray-800 rounded-lg p-4">
                                    <p className="text-sm text-gray-400">💡 Tips: Baca deskripsi kampanye dengan teliti</p>
                                </div>
                            </div>

                            <div className="text-center">
                                <div className="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <span className="text-2xl font-bold text-white">2</span>
                                </div>
                                <h3 className="text-xl font-semibold text-white mb-4">Pilih & Donasi</h3>
                                <p className="text-gray-300 mb-4">
                                    Tentukan jumlah donasi (minimal Rp 10.000), pilih metode pembayaran yang tersedia, 
                                    dan tambahkan pesan dukungan jika diinginkan.
                                </p>
                                <div className="bg-gray-800 rounded-lg p-4">
                                    <p className="text-sm text-gray-400">💡 Tips: Donasi bisa dilakukan secara anonim</p>
                                </div>
                            </div>

                            <div className="text-center">
                                <div className="w-20 h-20 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <span className="text-2xl font-bold text-white">3</span>
                                </div>
                                <h3 className="text-xl font-semibold text-white mb-4">Pantau Progress</h3>
                                <p className="text-gray-300 mb-4">
                                    Dapatkan update berkala tentang perkembangan kampanye dan bagaimana 
                                    donasi Anda membantu mencapai tujuan.
                                </p>
                                <div className="bg-gray-800 rounded-lg p-4">
                                    <p className="text-sm text-gray-400">💡 Tips: Bookmark kampanye favorit Anda</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* For Fundraisers Section */}
                    <div className="mb-20">
                        <div className="text-center mb-12">
                            <h2 className="text-3xl font-bold text-white mb-4">Untuk Penggalang Dana</h2>
                            <p className="text-gray-300">Cara membuat dan mengelola kampanye donasi</p>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div className="bg-gray-800 rounded-lg p-6 text-center">
                                <div className="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <h3 className="text-lg font-semibold text-white mb-2">1. Daftar Akun</h3>
                                <p className="text-gray-300 text-sm">
                                    Buat akun dengan informasi yang valid dan lengkap untuk memulai.
                                </p>
                            </div>

                            <div className="bg-gray-800 rounded-lg p-6 text-center">
                                <div className="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <h3 className="text-lg font-semibold text-white mb-2">2. Buat Kampanye</h3>
                                <p className="text-gray-300 text-sm">
                                    Isi detail kampanye, upload foto, dan tentukan target dana yang dibutuhkan.
                                </p>
                            </div>

                            <div className="bg-gray-800 rounded-lg p-6 text-center">
                                <div className="w-16 h-16 bg-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 className="text-lg font-semibold text-white mb-2">3. Verifikasi</h3>
                                <p className="text-gray-300 text-sm">
                                    Tim kami akan meninjau dan memverifikasi kampanye Anda sebelum dipublikasikan.
                                </p>
                            </div>

                            <div className="bg-gray-800 rounded-lg p-6 text-center">
                                <div className="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <h3 className="text-lg font-semibold text-white mb-2">4. Kelola & Update</h3>
                                <p className="text-gray-300 text-sm">
                                    Berikan update berkala kepada donatur dan kelola dana yang terkumpul.
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Payment Process */}
                    <div className="mb-20">
                        <div className="bg-gray-800 rounded-lg p-8">
                            <h2 className="text-2xl font-bold text-white mb-6 text-center">Proses Pembayaran</h2>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <h3 className="text-lg font-semibold text-white mb-4">Metode Pembayaran</h3>
                                    <ul className="space-y-2 text-gray-300">
                                        <li className="flex items-center">
                                            <span className="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                                            Transfer Bank (BCA, Mandiri, BRI, BNI)
                                        </li>
                                        <li className="flex items-center">
                                            <span className="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                            E-Wallet (GoPay, OVO, DANA, ShopeePay)
                                        </li>
                                        <li className="flex items-center">
                                            <span className="w-2 h-2 bg-purple-500 rounded-full mr-3"></span>
                                            Virtual Account
                                        </li>
                                    </ul>
                                </div>
                                <div>
                                    <h3 className="text-lg font-semibold text-white mb-4">Keamanan</h3>
                                    <ul className="space-y-2 text-gray-300">
                                        <li className="flex items-center">
                                            <span className="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                            Enkripsi SSL 256-bit
                                        </li>
                                        <li className="flex items-center">
                                            <span className="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                            Gateway pembayaran tersertifikasi
                                        </li>
                                        <li className="flex items-center">
                                            <span className="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                            Data pribadi terlindungi
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Trust & Safety */}
                    <div className="mb-20">
                        <h2 className="text-3xl font-bold text-white mb-8 text-center">Kepercayaan & Keamanan</h2>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div className="bg-gray-800 rounded-lg p-6">
                                <div className="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-4">
                                    <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <h3 className="text-xl font-semibold text-white mb-3">Verifikasi Kampanye</h3>
                                <p className="text-gray-300">
                                    Setiap kampanye diverifikasi oleh tim kami untuk memastikan keaslian dan transparansi.
                                </p>
                            </div>

                            <div className="bg-gray-800 rounded-lg p-6">
                                <div className="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
                                    <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <h3 className="text-xl font-semibold text-white mb-3">Transparansi Dana</h3>
                                <p className="text-gray-300">
                                    Laporan penggunaan dana dan progress kampanye dapat diakses secara real-time.
                                </p>
                            </div>

                            <div className="bg-gray-800 rounded-lg p-6">
                                <div className="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-4">
                                    <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <h3 className="text-xl font-semibold text-white mb-3">Dukungan 24/7</h3>
                                <p className="text-gray-300">
                                    Tim support siap membantu Anda kapan saja jika mengalami kendala atau pertanyaan.
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* CTA Section */}
                    <div className="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-8 text-center">
                        <h2 className="text-3xl font-bold text-white mb-4">
                            Siap Memulai Perjalanan Kebaikan?
                        </h2>
                        <p className="text-blue-100 mb-6 max-w-2xl mx-auto">
                            Bergabunglah dengan ribuan orang yang telah mempercayai platform kami untuk berbagi kebaikan.
                        </p>
                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            <Link
                                href="/campaigns"
                                className="bg-white text-blue-600 hover:bg-gray-100 font-semibold py-3 px-6 rounded-lg transition-colors"
                            >
                                Mulai Berdonasi
                            </Link>
                            <Link
                                href="/register"
                                className="bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-600 font-semibold py-3 px-6 rounded-lg transition-colors"
                            >
                                Buat Kampanye
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
