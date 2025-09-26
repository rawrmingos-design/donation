import { Link } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';

export default function TermsOfService() {
    return (
        <PublicLayout title="Syarat dan Ketentuan" currentPage="terms-of-service">

            <div className="py-12">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="text-center mb-12">
                        <h1 className="text-4xl font-bold text-white mb-4">
                            Syarat dan Ketentuan
                        </h1>
                        <p className="text-xl text-gray-300">
                            Terakhir diperbarui: {new Date().toLocaleDateString('id-ID', { 
                                year: 'numeric', 
                                month: 'long', 
                                day: 'numeric' 
                            })}
                        </p>
                    </div>

                    {/* Quick Navigation */}
                    <div className="bg-gray-800 rounded-lg p-6 mb-8">
                        <h2 className="text-xl font-semibold text-white mb-4">Navigasi Cepat</h2>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <a href="#acceptance" className="text-blue-400 hover:text-blue-300 transition-colors">1. Penerimaan Syarat</a>
                            <a href="#definitions" className="text-blue-400 hover:text-blue-300 transition-colors">2. Definisi</a>
                            <a href="#user-accounts" className="text-blue-400 hover:text-blue-300 transition-colors">3. Akun Pengguna</a>
                            <a href="#donations" className="text-blue-400 hover:text-blue-300 transition-colors">4. Donasi</a>
                            <a href="#campaigns" className="text-blue-400 hover:text-blue-300 transition-colors">5. Kampanye</a>
                            <a href="#prohibited-uses" className="text-blue-400 hover:text-blue-300 transition-colors">6. Penggunaan Terlarang</a>
                            <a href="#liability" className="text-blue-400 hover:text-blue-300 transition-colors">7. Tanggung Jawab</a>
                            <a href="#changes" className="text-blue-400 hover:text-blue-300 transition-colors">8. Perubahan Syarat</a>
                        </div>
                    </div>

                    {/* Content */}
                    <div className="bg-gray-800 rounded-lg p-8 space-y-8">
                        <section id="acceptance">
                            <h2 className="text-2xl font-bold text-white mb-4">1. Penerimaan Syarat</h2>
                            <div className="text-gray-300 space-y-4">
                                <p>
                                    Dengan mengakses dan menggunakan platform DonationPlatform ("Platform"), Anda menyetujui untuk terikat 
                                    oleh syarat dan ketentuan ini ("Syarat"). Jika Anda tidak menyetujui semua syarat ini, 
                                    Anda tidak diperkenankan menggunakan Platform.
                                </p>
                                <p>
                                    Platform ini dioperasikan oleh Fahmi Aksan Nugroho ("Kami", "Operator"). Syarat ini berlaku 
                                    untuk semua pengguna Platform, termasuk namun tidak terbatas pada donatur, penggalang dana, 
                                    dan pengunjung.
                                </p>
                            </div>
                        </section>

                        <section id="definitions">
                            <h2 className="text-2xl font-bold text-white mb-4">2. Definisi</h2>
                            <div className="text-gray-300 space-y-4">
                                <ul className="space-y-2">
                                    <li><strong className="text-white">"Platform"</strong> - Website dan layanan DonationPlatform</li>
                                    <li><strong className="text-white">"Pengguna"</strong> - Setiap individu yang mengakses atau menggunakan Platform</li>
                                    <li><strong className="text-white">"Donatur"</strong> - Pengguna yang memberikan donasi melalui Platform</li>
                                    <li><strong className="text-white">"Penggalang Dana"</strong> - Pengguna yang membuat dan mengelola kampanye donasi</li>
                                    <li><strong className="text-white">"Kampanye"</strong> - Inisiatif penggalangan dana yang dibuat di Platform</li>
                                    <li><strong className="text-white">"Donasi"</strong> - Kontribusi finansial yang diberikan melalui Platform</li>
                                </ul>
                            </div>
                        </section>

                        <section id="user-accounts">
                            <h2 className="text-2xl font-bold text-white mb-4">3. Akun Pengguna</h2>
                            <div className="text-gray-300 space-y-4">
                                <h3 className="text-lg font-semibold text-white">3.1 Registrasi</h3>
                                <p>
                                    Untuk menggunakan fitur tertentu, Anda harus membuat akun dengan memberikan informasi yang 
                                    akurat, lengkap, dan terkini. Anda bertanggung jawab untuk menjaga kerahasiaan kredensial akun Anda.
                                </p>
                                
                                <h3 className="text-lg font-semibold text-white">3.2 Keamanan Akun</h3>
                                <p>
                                    Anda bertanggung jawab penuh atas semua aktivitas yang terjadi di bawah akun Anda. 
                                    Segera laporkan kepada kami jika Anda mengetahui adanya penggunaan tidak sah atas akun Anda.
                                </p>

                                <h3 className="text-lg font-semibold text-white">3.3 Pembatasan Usia</h3>
                                <p>
                                    Platform ini hanya dapat digunakan oleh individu yang berusia minimal 17 tahun atau 
                                    telah mendapat persetujuan dari orang tua/wali.
                                </p>
                            </div>
                        </section>

                        <section id="donations">
                            <h2 className="text-2xl font-bold text-white mb-4">4. Donasi</h2>
                            <div className="text-gray-300 space-y-4">
                                <h3 className="text-lg font-semibold text-white">4.1 Proses Donasi</h3>
                                <p>
                                    Semua donasi bersifat sukarela dan tidak dapat dibatalkan setelah diproses. 
                                    Kami menggunakan penyedia pembayaran pihak ketiga yang aman untuk memproses transaksi.
                                </p>

                                <h3 className="text-lg font-semibold text-white">4.2 Biaya Platform</h3>
                                <p>
                                    Platform dapat mengenakan biaya administrasi untuk menutupi biaya operasional. 
                                    Informasi biaya akan ditampilkan dengan jelas sebelum Anda menyelesaikan donasi.
                                </p>

                                <h3 className="text-lg font-semibold text-white">4.3 Penyaluran Dana</h3>
                                <p>
                                    Kami akan berusaha menyalurkan dana sesuai dengan tujuan kampanye. Namun, 
                                    kami tidak bertanggung jawab atas penggunaan dana oleh penggalang dana setelah disalurkan.
                                </p>
                            </div>
                        </section>

                        <section id="campaigns">
                            <h2 className="text-2xl font-bold text-white mb-4">5. Kampanye</h2>
                            <div className="text-gray-300 space-y-4">
                                <h3 className="text-lg font-semibold text-white">5.1 Pembuatan Kampanye</h3>
                                <p>
                                    Penggalang dana harus memberikan informasi yang akurat dan lengkap tentang kampanye mereka. 
                                    Semua kampanye harus untuk tujuan yang sah dan tidak melanggar hukum.
                                </p>

                                <h3 className="text-lg font-semibold text-white">5.2 Verifikasi</h3>
                                <p>
                                    Kami berhak untuk memverifikasi dan meninjau kampanye sebelum dipublikasikan. 
                                    Kampanye yang tidak memenuhi standar kami dapat ditolak atau dihapus.
                                </p>

                                <h3 className="text-lg font-semibold text-white">5.3 Tanggung Jawab Penggalang Dana</h3>
                                <p>
                                    Penggalang dana bertanggung jawab untuk memberikan update berkala kepada donatur 
                                    dan menggunakan dana sesuai dengan tujuan yang dinyatakan.
                                </p>
                            </div>
                        </section>

                        <section id="prohibited-uses">
                            <h2 className="text-2xl font-bold text-white mb-4">6. Penggunaan Terlarang</h2>
                            <div className="text-gray-300 space-y-4">
                                <p>Anda dilarang menggunakan Platform untuk:</p>
                                <ul className="list-disc list-inside space-y-2 ml-4">
                                    <li>Aktivitas ilegal atau penipuan</li>
                                    <li>Kampanye yang menyesatkan atau palsu</li>
                                    <li>Konten yang mengandung ujaran kebencian atau diskriminasi</li>
                                    <li>Pelanggaran hak kekayaan intelektual</li>
                                    <li>Spam atau aktivitas yang mengganggu</li>
                                    <li>Pencucian uang atau aktivitas keuangan ilegal lainnya</li>
                                </ul>
                            </div>
                        </section>

                        <section id="liability">
                            <h2 className="text-2xl font-bold text-white mb-4">7. Tanggung Jawab dan Batasan</h2>
                            <div className="text-gray-300 space-y-4">
                                <h3 className="text-lg font-semibold text-white">7.1 Peran Platform</h3>
                                <p>
                                    Platform berperan sebagai perantara antara donatur dan penggalang dana. 
                                    Kami tidak bertanggung jawab atas keakuratan informasi kampanye atau penggunaan dana.
                                </p>

                                <h3 className="text-lg font-semibold text-white">7.2 Batasan Tanggung Jawab</h3>
                                <p>
                                    Dalam batas maksimal yang diizinkan hukum, kami tidak bertanggung jawab atas kerugian 
                                    langsung, tidak langsung, insidental, atau konsekuensial yang timbul dari penggunaan Platform.
                                </p>

                                <h3 className="text-lg font-semibold text-white">7.3 Ketersediaan Layanan</h3>
                                <p>
                                    Kami berusaha menjaga Platform tetap tersedia, namun tidak menjamin ketersediaan 
                                    layanan 100% tanpa gangguan.
                                </p>
                            </div>
                        </section>

                        <section id="changes">
                            <h2 className="text-2xl font-bold text-white mb-4">8. Perubahan Syarat</h2>
                            <div className="text-gray-300 space-y-4">
                                <p>
                                    Kami berhak mengubah Syarat ini kapan saja. Perubahan akan diberitahukan melalui Platform 
                                    dan mulai berlaku setelah dipublikasikan. Penggunaan Platform setelah perubahan 
                                    dianggap sebagai persetujuan terhadap Syarat yang baru.
                                </p>
                            </div>
                        </section>

                        <section>
                            <h2 className="text-2xl font-bold text-white mb-4">9. Kontak</h2>
                            <div className="text-gray-300 space-y-4">
                                <p>
                                    Jika Anda memiliki pertanyaan tentang Syarat dan Ketentuan ini, silakan hubungi kami:
                                </p>
                                <div className="bg-gray-700 rounded-lg p-4">
                                    <p><strong className="text-white">Email:</strong> fahmiaksannugroho@gmail.com</p>
                                    <p><strong className="text-white">Instagram:</strong> @fhxmiii</p>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>

        </PublicLayout>
    );
}
