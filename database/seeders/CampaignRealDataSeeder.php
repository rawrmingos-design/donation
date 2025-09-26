<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CampaignRealDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'creator')->get();
        $categories = Category::all();

        if ($users->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Pastikan Anda sudah menjalankan UserSeeder dan CategorySeeder.');
            return;
        }

        $campaigns = [
            // --- KESEHATAN ---
            [
                'title' => 'Bantu Pembangunan Sumur Wakaf untuk Warga Kekurangan Air',
                'short_desc' => 'Menyediakan air bersih yang layak konsumsi bagi warga di daerah kering.',
                'description' => '<p>Di beberapa daerah, warga harus berjalan berkilo-kilometer untuk mendapatkan air bersih. Pembangunan sumur wakaf akan menjadi solusi jangka panjang bagi masalah ini. Dana akan digunakan untuk pengeboran sumur, instalasi pompa, dan fasilitas penampungan air.</p>',
                'target_amount' => 55000000,
                'category' => 'Kemanusiaan',
                'status' => 'active',
                'featured_image' => 'campaigns/kemanusiaan/sumur-wakaf.jpg',
                'deadline' => now()->addMonths(3),
                'donors_count' => 0,

            ],
            [
                'title' => 'Beasiswa Sekolah Vokasi untuk Remaja Yatim Piatu',
                'short_desc' => 'Membekali keterampilan praktis untuk masa depan yang lebih cerah.',
                'description' => '<p>Banyak remaja yatim piatu putus sekolah karena kendala biaya. Kampanye ini menyediakan beasiswa penuh untuk sekolah vokasi, melatih mereka dengan keterampilan yang relevan seperti menjahit, tata boga, atau teknisi. Tujuannya adalah agar mereka mandiri dan siap kerja.</p>',
                'target_amount' => 70000000,
                'category' => 'Pendidikan',
                'status' => 'active',
                'featured_image' => 'campaigns/pendidikan/beasiswa-vokasi.jpg',
                'deadline' => now()->addMonths(5),
                'donors_count' => 0,
            ],
            [
                'title' => 'Dukungan Makanan Bergizi untuk Balita Kurang Gizi di Pedalaman',
                'short_desc' => 'Melawan stunting dengan gizi seimbang sejak dini.',
                'description' => '<p>Stunting masih menjadi masalah serius di Indonesia, terutama di daerah terpencil. Kami akan mendistribusikan paket makanan bergizi rutin, susu, dan vitamin untuk balita. Donasi Anda akan membantu mencegah stunting dan memastikan tumbuh kembang mereka optimal.</p>',
                'target_amount' => 45000000,
                'category' => 'Kesehatan',
                'status' => 'active',
                'featured_image' => 'campaigns/kesehatan/makanan-balita.jpg',
                'deadline' => now()->addMonths(2),
                'donors_count' => 0,
            ],
            [
                'title' => 'Rehabilitasi Hutan Mangrove Pesisir di Jawa Tengah',
                'short_desc' => 'Menanam kembali mangrove untuk mencegah abrasi dan melestarikan ekosistem.',
                'description' => '<p>Kerusakan hutan mangrove akibat abrasi dan alih fungsi lahan mengancam ekosistem pesisir. Kampanye ini mengajak Anda untuk berpartisipasi dalam penanaman ribuan bibit mangrove. Setiap donasi akan mendukung upaya konservasi dan melindungi habitat biota laut.</p>',
                'target_amount' => 80000000,
                'category' => 'Lingkungan',
                'status' => 'active',
                'featured_image' => 'campaigns/lingkungan/rehabilitasi-mangrove.jpg',
                'deadline' => now()->addMonths(6),
                'donors_count' => 0,
            ],
            [
                'title' => 'Bantu Pembangunan Sekolah Darurat Pasca Bencana Banjir',
                'short_desc' => 'Memastikan anak-anak bisa kembali bersekolah setelah banjir menghancurkan gedung sekolah mereka.',
                'description' => '<p>Sebuah sekolah dasar di desa terendam banjir dan rusak parah, menghentikan kegiatan belajar mengajar. Kampanye ini bertujuan membangun sekolah darurat dari bahan ringan agar anak-anak bisa segera melanjutkan pendidikan mereka.</p>',
                'target_amount' => 110000000,
                'category' => 'Bencana Alam',
                'status' => 'active',
                'featured_image' => 'campaigns/bencana-alam/sekolah-banjir.jpg',
                'deadline' => now()->addMonths(4),
                'donors_count' => 0,
                ],
            [
                'title' => 'Pemberian Kaki Palsu untuk Korban Kecelakaan dan Difabel',
                'short_desc' => 'Mengembalikan mobilitas dan kemandirian bagi mereka yang membutuhkan.',
                'description' => '<p>Banyak penyandang disabilitas di Indonesia tidak memiliki akses ke alat bantu gerak yang layak. Kampanye ini akan mendanai pembuatan dan pemasangan kaki palsu yang disesuaikan, serta memberikan pelatihan agar penerima dapat kembali beraktivitas normal.</p>',
                'target_amount' => 95000000,
                'category' => 'Kemanusiaan',
                'status' => 'active',
                'featured_image' => 'campaigns/kemanusiaan/kaki-palsu.jpg',
                'deadline' => now()->addMonths(7),
                'donors_count' => 0,
            ],
            [
                'title' => 'Bantu Operasi Bedah Jantung untuk Anak Kurang Mampu',
                'short_desc' => 'Menyediakan harapan hidup baru bagi anak-anak dengan kelainan jantung bawaan.',
                'description' => '<p>Setiap tahun, banyak anak lahir dengan penyakit jantung bawaan, tetapi tidak semua keluarga mampu menanggung biaya operasi yang besar. Donasi Anda akan membantu menutupi biaya pra-operasi, bedah, dan perawatan pasca-operasi untuk anak-anak ini, memberi mereka kesempatan untuk hidup sehat.</p>',
                'target_amount' => 250000000,
                'category' => 'Kesehatan',
                'status' => 'active',
                'featured_image' => 'campaigns/kesehatan/operasi-jantung.jpg',
                'deadline' => now()->addMonths(8),
                'donors_count' => 0,
                ],
            [
                'title' => 'Program Makanan Sehat untuk Anak Jalanan',
                'short_desc' => 'Memberikan makanan layak dan gizi yang cukup untuk anak-anak yang hidup di jalanan.',
                'description' => '<p>Anak-anak jalanan sering kali tidak mendapatkan asupan gizi yang memadai, berisiko tinggi terkena penyakit. Kami akan menjalankan program pemberian makanan sehat setiap hari, serta menyediakan layanan medis dasar dan bimbingan belajar.</p>',
                'target_amount' => 40000000,
                'category' => 'Kemanusiaan',
                'status' => 'active',
                'featured_image' => 'campaigns/kemanusiaan/makanan-anak-jalanan.jpg',
                'deadline' => now()->addMonths(1),
                'donors_count' => 0,
            ],
            [
                'title' => 'Bantu Rehabilitasi dan Pelepasan Burung Endemik',
                'short_desc' => 'Menyelamatkan burung langka dari perdagangan ilegal dan merawatnya hingga siap dilepasliarkan.',
                'description' => '<p>Perdagangan burung ilegal mengancam kelestarian burung endemik di Indonesia. Kami mengumpulkan dana untuk operasional pusat rehabilitasi, pakan, dan tim medis yang merawat burung-burung sitaan. Tujuan akhir kami adalah mengembalikan mereka ke habitat aslinya.</p>',
                'target_amount' => 35000000,
                'category' => 'Hewan',
                'status' => 'active',
                'featured_image' => 'campaigns/hewan/rehabilitasi-burung.jpg',
                'deadline' => now()->addMonths(4),
                'donors_count' => 0,
            ],
            [
                'title' => 'Pembangunan Kembali Jembatan Desa Pasca Longsor',
                'short_desc' => 'Menghubungkan kembali dua desa yang terisolasi akibat jembatan putus.',
                'description' => '<p>Jembatan utama yang menghubungkan dua desa di pegunungan hancur akibat longsor, memutus akses warga ke fasilitas publik seperti sekolah dan puskesmas. Dana akan digunakan untuk membeli material dan membayar pekerja lokal untuk membangun jembatan darurat yang aman.</p>',
                'target_amount' => 180000000,
                'category' => 'Bencana Alam',
                'status' => 'active',
                'featured_image' => 'campaigns/bencana-alam/jembatan-longsor.jpg',
                'deadline' => now()->addMonths(5),
                'donors_count' => 0,
            ],
        ];

        foreach ($campaigns as $campaignData) {
            $category = $categories->where('name', $campaignData['category'])->first();
            $user = $users->random();

            Campaign::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'title' => $campaignData['title'],
                'slug' => Str::slug($campaignData['title']),
                'short_desc' => $campaignData['short_desc'],
                'description' => $campaignData['description'],
                'target_amount' => $campaignData['target_amount'],
                'collected_amount' => rand(1000000, $campaignData['target_amount'] * 0.5), // Progres yang lebih bervariasi
                'status' => $campaignData['status'],
                'featured_image' => $campaignData['featured_image'],
                'deadline' => $campaignData['deadline'],
                'donors_count' => $campaignData['donors_count'],
                'created_at' => now()->subDays(rand(1, 90)),
                'updated_at' => now()->subDays(rand(0, 15)),
            ]);
        }

        $this->command->info('Campaign Real Data Seeder completed successfully.');
    }
}