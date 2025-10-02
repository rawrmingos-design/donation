import PublicLayout from '@/layouts/PublicLayout';

export default function PrivacyPolicy() {
    return (
        <PublicLayout title="Kebijakan Privasi" currentPage="privacy-policy">

            <div className="py-12">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="text-center mb-12">
                        <h1 className="text-4xl font-bold text-white mb-4">
                            Kebijakan Privasi
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
                            <a href="#introduction" className="text-blue-400 hover:text-blue-300 transition-colors">1. Pendahuluan</a>
                            <a href="#information-collected" className="text-blue-400 hover:text-blue-300 transition-colors">2. Informasi yang Dikumpulkan</a>
                            <a href="#how-we-use" className="text-blue-400 hover:text-blue-300 transition-colors">3. Cara Penggunaan Data</a>
                            <a href="#information-sharing" className="text-blue-400 hover:text-blue-300 transition-colors">4. Pembagian Informasi</a>
                            <a href="#data-security" className="text-blue-400 hover:text-blue-300 transition-colors">5. Keamanan Data</a>
                            <a href="#your-rights" className="text-blue-400 hover:text-blue-300 transition-colors">6. Hak Anda</a>
                            <a href="#cookies" className="text-blue-400 hover:text-blue-300 transition-colors">7. Cookies</a>
                            <a href="#changes" className="text-blue-400 hover:text-blue-300 transition-colors">8. Perubahan Kebijakan</a>
                        </div>
                    </div>

                    {/* Content */}
                    <div className="bg-gray-800 rounded-lg p-8 space-y-8">
                        <section id="introduction">
                            <h2 className="text-2xl font-bold text-white mb-4">1. Pendahuluan</h2>
                            <div className="text-gray-300 space-y-4">
                                <p>
                                    DonationPlatform ("kami", "platform") berkomitmen untuk melindungi privasi dan keamanan 
                                    informasi pribadi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, 
                                    menggunakan, dan melindungi informasi Anda saat menggunakan platform donasi kami.
                                </p>
                                <p>
                                    Dengan menggunakan platform ini, Anda menyetujui praktik yang dijelaskan dalam 
                                    Kebijakan Privasi ini. Jika Anda tidak setuju dengan kebijakan ini, 
                                    mohon untuk tidak menggunakan platform kami.
                                </p>
                            </div>
                        </section>

                        <section id="information-collected">
                            <h2 className="text-2xl font-bold text-white mb-4">2. Informasi yang Dikumpulkan</h2>
                            <div className="text-gray-300 space-y-4">
                                <h3 className="text-lg font-semibold text-white">2.1 Informasi yang Anda Berikan</h3>
                                <ul className="list-disc list-inside space-y-2 ml-4">
                                    <li>Informasi akun: nama, email, nomor telepon</li>
                                    <li>Informasi donasi: jumlah donasi, pesan dukungan</li>
                                    <li>Informasi kampanye: deskripsi, gambar, target dana</li>
                                    <li>Informasi pembayaran: data yang diperlukan untuk memproses transaksi</li>
                                    <li>Komunikasi: pesan yang Anda kirim kepada kami</li>
                                </ul>

                                <h3 className="text-lg font-semibold text-white">2.2 Informasi yang Dikumpulkan Otomatis</h3>
                                <ul className="list-disc list-inside space-y-2 ml-4">
                                    <li>Data teknis: alamat IP, jenis browser, sistem operasi</li>
                                    <li>Data penggunaan: halaman yang dikunjungi, waktu akses</li>
                                    <li>Data perangkat: informasi tentang perangkat yang digunakan</li>
                                    <li>Cookies dan teknologi pelacakan serupa</li>
                                </ul>

                                <h3 className="text-lg font-semibold text-white">2.3 Informasi dari Pihak Ketiga</h3>
                                <ul className="list-disc list-inside space-y-2 ml-4">
                                    <li>Informasi dari penyedia pembayaran</li>
                                    <li>Data dari platform media sosial (jika Anda login melalui media sosial)</li>
                                    <li>Informasi verifikasi identitas</li>
                                </ul>
                            </div>
                        </section>

                        <section id="how-we-use">
                            <h2 className="text-2xl font-bold text-white mb-4">3. Cara Penggunaan Data</h2>
                            <div className="text-gray-300 space-y-4">
                                <p>Kami menggunakan informasi Anda untuk:</p>
                                <ul className="list-disc list-inside space-y-2 ml-4">
                                    <li>Menyediakan dan memelihara layanan platform</li>
                                    <li>Memproses donasi dan transaksi keuangan</li>
                                    <li>Memverifikasi identitas dan mencegah penipuan</li>
                                    <li>Berkomunikasi dengan Anda tentang akun dan layanan</li>
                                    <li>Mengirim update kampanye dan notifikasi penting</li>
                                    <li>Meningkatkan keamanan dan fungsionalitas platform</li>
                                    <li>Menganalisis penggunaan untuk perbaikan layanan</li>
                                    <li>Mematuhi kewajiban hukum dan regulasi</li>
                                </ul>
                            </div>
                        </section>

                        <section id="information-sharing">
                            <h2 className="text-2xl font-bold text-white mb-4">4. Pembagian Informasi</h2>
                            <div className="text-gray-300 space-y-4">
                                <h3 className="text-lg font-semibold text-white">4.1 Kami Tidak Menjual Data Anda</h3>
                                <p>
                                    Kami tidak menjual, menyewakan, atau memperdagangkan informasi pribadi Anda 
                                    kepada pihak ketiga untuk tujuan komersial.
                                </p>

                                <h3 className="text-lg font-semibold text-white">4.2 Pembagian yang Diperlukan</h3>
                                <p>Kami dapat membagikan informasi Anda dalam situasi berikut:</p>
                                <ul className="list-disc list-inside space-y-2 ml-4">
                                    <li>Dengan penyedia layanan pembayaran untuk memproses transaksi</li>
                                    <li>Dengan penggalang dana terkait donasi yang Anda berikan</li>
                                    <li>Untuk mematuhi kewajiban hukum atau perintah pengadilan</li>
                                    <li>Untuk melindungi hak, properti, atau keamanan platform dan pengguna</li>
                                    <li>Dengan persetujuan eksplisit Anda</li>
                                </ul>

                                <h3 className="text-lg font-semibold text-white">4.3 Informasi Publik</h3>
                                <p>
                                    Informasi tertentu seperti nama donatur (kecuali donasi anonim), 
                                    jumlah donasi, dan pesan dukungan dapat ditampilkan secara publik di platform.
                                </p>
                            </div>
                        </section>

                        <section id="data-security">
                            <h2 className="text-2xl font-bold text-white mb-4">5. Keamanan Data</h2>
                            <div className="text-gray-300 space-y-4">
                                <h3 className="text-lg font-semibold text-white">5.1 Langkah Keamanan</h3>
                                <p>Kami menerapkan berbagai langkah keamanan untuk melindungi data Anda:</p>
                                <ul className="list-disc list-inside space-y-2 ml-4">
                                    <li>Enkripsi data saat transmisi dan penyimpanan</li>
                                    <li>Akses terbatas pada informasi pribadi</li>
                                    <li>Pemantauan keamanan sistem secara berkala</li>
                                    <li>Penggunaan penyedia pembayaran yang tersertifikasi</li>
                                </ul>

                                <h3 className="text-lg font-semibold text-white">5.2 Penyimpanan Data</h3>
                                <p>
                                    Data Anda disimpan selama diperlukan untuk menyediakan layanan atau 
                                    sesuai dengan kewajiban hukum. Data yang tidak lagi diperlukan akan dihapus secara aman.
                                </p>

                                <h3 className="text-lg font-semibold text-white">5.3 Pelanggaran Data</h3>
                                <p>
                                    Jika terjadi pelanggaran data yang dapat mempengaruhi informasi pribadi Anda, 
                                    kami akan memberitahu Anda dan otoritas yang berwenang sesuai dengan hukum yang berlaku.
                                </p>
                            </div>
                        </section>

                        <section id="your-rights">
                            <h2 className="text-2xl font-bold text-white mb-4">6. Hak Anda</h2>
                            <div className="text-gray-300 space-y-4">
                                <p>Anda memiliki hak untuk:</p>
                                <ul className="list-disc list-inside space-y-2 ml-4">
                                    <li><strong className="text-white">Akses:</strong> Meminta salinan data pribadi yang kami miliki tentang Anda</li>
                                    <li><strong className="text-white">Koreksi:</strong> Meminta perbaikan data yang tidak akurat atau tidak lengkap</li>
                                    <li><strong className="text-white">Penghapusan:</strong> Meminta penghapusan data pribadi dalam kondisi tertentu</li>
                                    <li><strong className="text-white">Pembatasan:</strong> Meminta pembatasan pemrosesan data Anda</li>
                                    <li><strong className="text-white">Portabilitas:</strong> Meminta transfer data ke penyedia layanan lain</li>
                                    <li><strong className="text-white">Keberatan:</strong> Menolak pemrosesan data untuk tujuan tertentu</li>
                                </ul>
                                
                                <div className="bg-gray-700 rounded-lg p-4 mt-4">
                                    <p><strong className="text-white">Cara Menggunakan Hak Anda:</strong></p>
                                    <p>Hubungi kami di fahmiaksannugroho@gmail.com untuk menggunakan hak-hak di atas.</p>
                                </div>
                            </div>
                        </section>

                        <section id="cookies">
                            <h2 className="text-2xl font-bold text-white mb-4">7. Cookies dan Teknologi Pelacakan</h2>
                            <div className="text-gray-300 space-y-4">
                                <h3 className="text-lg font-semibold text-white">7.1 Apa itu Cookies</h3>
                                <p>
                                    Cookies adalah file kecil yang disimpan di perangkat Anda saat mengunjungi website. 
                                    Kami menggunakan cookies untuk meningkatkan pengalaman Anda di platform.
                                </p>

                                <h3 className="text-lg font-semibold text-white">7.2 Jenis Cookies yang Kami Gunakan</h3>
                                <ul className="list-disc list-inside space-y-2 ml-4">
                                    <li><strong className="text-white">Cookies Penting:</strong> Diperlukan untuk fungsi dasar platform</li>
                                    <li><strong className="text-white">Cookies Fungsional:</strong> Mengingat preferensi dan pengaturan Anda</li>
                                    <li><strong className="text-white">Cookies Analitik:</strong> Membantu kami memahami cara penggunaan platform</li>
                                </ul>

                                <h3 className="text-lg font-semibold text-white">7.3 Mengelola Cookies</h3>
                                <p>
                                    Anda dapat mengatur preferensi cookies melalui pengaturan browser Anda. 
                                    Namun, menonaktifkan cookies tertentu dapat mempengaruhi fungsionalitas platform.
                                </p>
                            </div>
                        </section>

                        <section id="changes">
                            <h2 className="text-2xl font-bold text-white mb-4">8. Perubahan Kebijakan</h2>
                            <div className="text-gray-300 space-y-4">
                                <p>
                                    Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu untuk mencerminkan 
                                    perubahan dalam praktik kami atau untuk alasan operasional, hukum, atau regulasi lainnya.
                                </p>
                                <p>
                                    Perubahan material akan diberitahukan kepada Anda melalui email atau pemberitahuan 
                                    di platform. Penggunaan platform setelah perubahan dianggap sebagai persetujuan 
                                    terhadap kebijakan yang diperbarui.
                                </p>
                            </div>
                        </section>

                        <section>
                            <h2 className="text-2xl font-bold text-white mb-4">9. Hubungi Kami</h2>
                            <div className="text-gray-300 space-y-4">
                                <p>
                                    Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini atau ingin menggunakan 
                                    hak privasi Anda, silakan hubungi kami:
                                </p>
                                <div className="bg-gray-700 rounded-lg p-4">
                                    <p><strong className="text-white">Email:</strong> fahmiaksannugroho@gmail.com</p>
                                    <p><strong className="text-white">Instagram:</strong> @fhxmiii</p>
                                    <p><strong className="text-white">Subjek Email:</strong> "Privacy Policy Inquiry - DonationPlatform"</p>
                                </div>
                                <p className="text-sm">
                                    Kami akan merespons permintaan Anda dalam waktu 30 hari kerja sesuai dengan 
                                    peraturan perlindungan data yang berlaku.
                                </p>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
