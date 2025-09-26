<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'creator')->get();
        $categories = Category::all();

        if ($users->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Please run UserSeeder and CategorySeeder first.');
            return;
        }

        $campaigns = [
            [
                'title' => 'Bantu Bangun Sumur Air Bersih di Pedesaan',
                'short_desc' => 'Menyediakan akses air bersih bagi keluarga di desa-desa terpencil',
                'description' => '<p>Banyak komunitas pedesaan tidak memiliki akses ke air minum yang bersih dan aman. Kampanye ini bertujuan untuk membangun sumur air yang berkelanjutan, yang akan melayani ratusan keluarga selama bertahun-tahun yang akan datang.</p><p>Donasi Anda akan membantu menutupi biaya pengeboran, peralatan, dan pelatihan pemeliharaan bagi masyarakat setempat.</p>',
                'target_amount' => 50000000, // 50 juta IDR
                'category' => 'Kesehatan',
                'status' => 'active',
                'featured_image' => 'campaigns/water-well.jpg',
                'deadline' => now()->addMonths(3),
                'donors_count' => 0,
            ],
            [
                'title' => 'Bantuan Darurat untuk Korban Banjir',
                'short_desc' => 'Bantuan segera bagi keluarga yang terkena dampak banjir baru-baru ini',
                'description' => '<p>Banjir baru-baru ini telah membuat ratusan keluarga kehilangan tempat tinggal, makanan, atau kebutuhan dasar lainnya.</p><p>Kampanye bantuan darurat ini akan menyediakan:</p><ul><li>Tempat tinggal sementara</li><li>Makanan dan air bersih</li><li>Perlengkapan medis</li><li>Pakaian dan selimut</li></ul>',
                'target_amount' => 25000000, // 25 juta IDR
                'category' => 'Bencana Alam',
                'status' => 'active',
                'featured_image' => 'campaigns/flood-relief.jpg',
                'deadline' => now()->addMonth(),
                'donors_count' => 0,
            ],
            [
                'title' => 'Beasiswa Pendidikan untuk Anak Kurang Mampu',
                'short_desc' => 'Mendukung anak-anak cerdas yang tidak mampu bersekolah',
                'description' => '<p>Pendidikan adalah kunci untuk memutus lingkaran kemiskinan. Kampanye ini menyediakan beasiswa untuk anak-anak berbakat dari keluarga berpenghasilan rendah.</p><p>Setiap beasiswa mencakup:</p><ul><li>Biaya sekolah dan seragam</li><li>Buku dan bahan ajar</li><li>Biaya transportasi</li><li>Makanan bergizi</li></ul>',
                'target_amount' => 75000000, // 75 juta IDR
                'category' => 'Pendidikan',
                'status' => 'active',
                'featured_image' => 'campaigns/education.jpg',
                'deadline' => now()->addMonths(6),
                'donors_count' => 0,
            ],
            [
                'title' => 'Pengobatan Medis untuk Anak Penderita Kanker',
                'short_desc' => 'Perawatan penyelamat hidup untuk pasien kanker anak',
                'description' => '<p>Setiap anak berhak mendapatkan kesempatan untuk melawan kanker dengan perawatan medis terbaik yang tersedia. Kampanye ini mendukung keluarga yang tidak mampu membayar pengobatan kanker yang mahal.</p><p>Dana akan digunakan untuk:</p><ul><li>Kemoterapi dan radiasi</li><li>Biaya operasi</li><li>Obat-obatan dan suplemen</li><li>Akomodasi rumah sakit untuk keluarga</li></ul>',
                'target_amount' => 100000000, // 100 juta IDR
                'category' => 'Kesehatan',
                'status' => 'active',
                'featured_image' => 'campaigns/cancer-treatment.jpg',
                'deadline' => now()->addMonths(4),
                'donors_count' => 0,
            ],
            [
                'title' => 'Bangun Kembali Pusat Komunitas Setelah Kebakaran',
                'short_desc' => 'Memulihkan jantung komunitas kita',
                'description' => '<p>Pusat komunitas tercinta kami hancur dalam kebakaran baru-baru ini. Bangunan ini telah menjadi tempat berkumpul untuk acara, kelas, dan pertemuan komunitas selama lebih dari 20 tahun.</p><p>Bantu kami membangun kembali lebih kuat dari sebelumnya dengan fasilitas modern, termasuk:</p><ul><li>Aula serbaguna</li><li>Lab komputer</li><li>Perpustakaan</li><li>Fasilitas dapur</li></ul>',
                'target_amount' => 150000000, // 150 juta IDR
                'category' => 'Kemanusiaan',
                'status' => 'active',
                'featured_image' => 'campaigns/community-center.jpg',
                'deadline' => now()->addMonths(8),
                'donors_count' => 0,
            ],
            [
                'title' => 'Dukung Penampungan Hewan Lokal',
                'short_desc' => 'Merawat hewan yang terlantar dan terluka',
                'description' => '<p>Penampungan hewan lokal kami menyediakan perawatan bagi ratusan hewan yang terlantar, terluka, dan tersesat setiap tahun. Kami membutuhkan dukungan Anda untuk melanjutkan pekerjaan vital ini.</p><p>Donasi Anda membantu menyediakan:</p><ul><li>Makanan dan perawatan medis</li><li>Perawatan penampungan</li><li>Program sterilisasi/kebiri</li><li>Layanan adopsi</li></ul>',
                'target_amount' => 30000000, // 30 juta IDR
                'category' => 'Hewan',
                'status' => 'active',
                'featured_image' => 'campaigns/animal-shelter.jpg',
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
                'collected_amount' => 0, // Random progress
                'status' => $campaignData['status'],
                'featured_image' => $campaignData['featured_image'],
                'deadline' => $campaignData['deadline'],
                'donors_count' => $campaignData['donors_count'],
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);
        }

        $this->command->info('Campaign seeder completed successfully.');
    }
}
