<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Kesehatan',
                'description' => 'Kampanye untuk bantuan kesehatan, pengobatan, dan perawatan medis',
            ],
            [
                'name' => 'Pendidikan',
                'description' => 'Kampanye untuk bantuan pendidikan, beasiswa, dan fasilitas sekolah',
            ],
            [
                'name' => 'Bencana Alam',
                'description' => 'Kampanye untuk bantuan korban bencana alam dan pemulihan pasca bencana',
            ],
            [
                'name' => 'Kemanusiaan',
                'description' => 'Kampanye untuk bantuan kemanusiaan dan sosial',
            ],
            [
                'name' => 'Lingkungan',
                'description' => 'Kampanye untuk pelestarian lingkungan dan konservasi',
            ],
            [
                'name' => 'Hewan',
                'description' => 'Kampanye untuk perlindungan dan kesejahteraan hewan',
            ],
            [
                'name' => 'Teknologi',
                'description' => 'Kampanye untuk pengembangan teknologi dan inovasi',
            ],
            [
                'name' => 'Olahraga',
                'description' => 'Kampanye untuk dukungan atlet dan pengembangan olahraga',
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
            ]);
        }
    }
}
