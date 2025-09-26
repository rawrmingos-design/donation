import { Link } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';

export default function About() {
    return (
        <PublicLayout title="Tentang Kami" currentPage="about">
            <div className="py-12">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    {/* Hero Section */}
                    <div className="text-center mb-12">
                        <h1 className="text-4xl font-bold text-white mb-4">
                            Tentang Platform Donasi Kami
                        </h1>
                        <p className="text-xl text-gray-300 max-w-2xl mx-auto">
                            Platform donasi terpercaya yang menghubungkan kebaikan hati Anda dengan mereka yang membutuhkan
                        </p>
                    </div>

                    {/* Mission & Vision */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                        <div className="bg-gray-800 rounded-lg p-8">
                            <div className="flex items-center mb-4">
                                <div className="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mr-4">
                                    <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <h2 className="text-2xl font-bold text-white">Misi Kami</h2>
                            </div>
                            <p className="text-gray-300 leading-relaxed">
                                Memfasilitasi aksi kebaikan dengan menyediakan platform yang aman, transparan, dan mudah digunakan 
                                untuk menghubungkan donatur dengan kampanye-kampanye yang membutuhkan bantuan.
                            </p>
                        </div>

                        <div className="bg-gray-800 rounded-lg p-8">
                            <div className="flex items-center mb-4">
                                <div className="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mr-4">
                                    <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>
                                <h2 className="text-2xl font-bold text-white">Visi Kami</h2>
                            </div>
                            <p className="text-gray-300 leading-relaxed">
                                Menjadi platform donasi terdepan di Indonesia yang menciptakan dampak positif berkelanjutan 
                                melalui teknologi dan kepercayaan masyarakat.
                            </p>
                        </div>
                    </div>

                    {/* Features */}
                    <div className="mb-12">
                        <h2 className="text-3xl font-bold text-white text-center mb-8">
                            Mengapa Memilih Platform Kami?
                        </h2>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div className="text-center">
                                <div className="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <h3 className="text-xl font-semibold text-white mb-2">Aman & Terpercaya</h3>
                                <p className="text-gray-300">
                                    Sistem keamanan berlapis dan verifikasi ketat untuk melindungi setiap transaksi donasi Anda.
                                </p>
                            </div>

                            <div className="text-center">
                                <div className="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <h3 className="text-xl font-semibold text-white mb-2">Transparan</h3>
                                <p className="text-gray-300">
                                    Laporan penggunaan dana real-time dan update berkala dari setiap kampanye donasi.
                                </p>
                            </div>

                            <div className="text-center">
                                <div className="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3 className="text-xl font-semibold text-white mb-2">Mudah Digunakan</h3>
                                <p className="text-gray-300">
                                    Interface yang intuitif dan proses donasi yang sederhana, dapat diakses dari mana saja.
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Statistics */}
                    <div className="bg-gray-800 rounded-lg p-8 mb-12">
                        <h2 className="text-2xl font-bold text-white text-center mb-8">Dampak yang Telah Diciptakan</h2>
                        <div className="grid grid-cols-1 md:grid-cols-4 gap-6 text-center">
                            <div>
                                <div className="text-3xl font-bold text-blue-400 mb-2">1,000+</div>
                                <div className="text-gray-300">Kampanye Berhasil</div>
                            </div>
                            <div>
                                <div className="text-3xl font-bold text-green-400 mb-2">Rp 5M+</div>
                                <div className="text-gray-300">Dana Terkumpul</div>
                            </div>
                            <div>
                                <div className="text-3xl font-bold text-purple-400 mb-2">10,000+</div>
                                <div className="text-gray-300">Donatur Aktif</div>
                            </div>
                            <div>
                                <div className="text-3xl font-bold text-yellow-400 mb-2">50,000+</div>
                                <div className="text-gray-300">Orang Terbantu</div>
                            </div>
                        </div>
                    </div>

                    {/* How it Works */}
                    <div className="mb-12">
                        <h2 className="text-3xl font-bold text-white text-center mb-8">Cara Kerja Platform</h2>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div className="text-center">
                                <div className="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span className="text-2xl font-bold text-white">1</span>
                                </div>
                                <h3 className="text-xl font-semibold text-white mb-2">Pilih Kampanye</h3>
                                <p className="text-gray-300">
                                    Jelajahi berbagai kampanye donasi yang telah diverifikasi dan pilih yang sesuai dengan hati Anda.
                                </p>
                            </div>

                            <div className="text-center">
                                <div className="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span className="text-2xl font-bold text-white">2</span>
                                </div>
                                <h3 className="text-xl font-semibold text-white mb-2">Berdonasi</h3>
                                <p className="text-gray-300">
                                    Tentukan jumlah donasi dan pilih metode pembayaran yang tersedia dengan proses yang aman.
                                </p>
                            </div>

                            <div className="text-center">
                                <div className="w-20 h-20 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span className="text-2xl font-bold text-white">3</span>
                                </div>
                                <h3 className="text-xl font-semibold text-white mb-2">Pantau Dampak</h3>
                                <p className="text-gray-300">
                                    Dapatkan update berkala tentang perkembangan kampanye dan dampak dari donasi Anda.
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Call to Action */}
                    <div className="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-8 text-center">
                        <h2 className="text-3xl font-bold text-white mb-4">
                            Mari Bersama Menciptakan Perubahan
                        </h2>
                        <p className="text-blue-100 mb-6">
                            Setiap donasi, sekecil apapun, memiliki kekuatan untuk mengubah hidup seseorang. 
                            Bergabunglah dengan ribuan donatur lainnya dalam menciptakan dampak positif.
                        </p>
                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            <Link
                                href="/campaigns"
                                className="bg-white text-blue-600 hover:bg-gray-100 font-semibold py-3 px-6 rounded-lg transition-colors"
                            >
                                Mulai Berdonasi
                            </Link>
                            <a
                                href="mailto:fahmiaksannugroho@gmail.com"
                                className="bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-600 font-semibold py-3 px-6 rounded-lg transition-colors"
                            >
                                Hubungi Kami
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
