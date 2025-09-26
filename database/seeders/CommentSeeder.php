<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing campaigns and users
        $campaigns = Campaign::all();
        $users = User::all();

        if ($campaigns->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No campaigns or users found. Please run CampaignSeeder and UserSeeder first.');
            return;
        }

        $comments = [
            [
                'content' => 'Semoga kampanye ini berhasil membantu banyak orang. Saya sangat mendukung inisiatif ini!',
                'is_public' => true,
            ],
            [
                'content' => 'Terima kasih sudah membuat kampanye yang sangat bermanfaat. Semoga target tercapai.',
                'is_public' => true,
            ],
            [
                'content' => 'Saya sudah berdonasi dan berharap bisa membantu. Terus semangat!',
                'is_public' => true,
            ],
            [
                'content' => 'Kampanye yang sangat mulia. Semoga Allah membalas kebaikan kalian.',
                'is_public' => true,
            ],
            [
                'content' => 'Bagaimana progress kampanye ini? Apakah ada update terbaru?',
                'is_public' => true,
            ],
            [
                'content' => 'Saya ingin ikut membantu menyebarkan kampanye ini ke teman-teman.',
                'is_public' => true,
            ],
            [
                'content' => 'Apakah ada cara lain untuk membantu selain donasi uang?',
                'is_public' => true,
            ],
            [
                'content' => 'Terima kasih sudah transparan dalam mengelola donasi. Sangat terpercaya!',
                'is_public' => true,
            ],
            [
                'content' => 'Semoga kampanye ini bisa menginspirasi lebih banyak orang untuk peduli.',
                'is_public' => true,
            ],
            [
                'content' => 'Saya akan terus mengikuti perkembangan kampanye ini. Sukses selalu!',
                'is_public' => true,
            ],
            [
                'content' => 'Kampanye yang sangat menyentuh hati. Semoga banyak yang tergerak untuk membantu.',
                'is_public' => true,
            ],
            [
                'content' => 'Apakah ada rencana untuk membuat kampanye serupa di masa depan?',
                'is_public' => true,
            ],
            [
                'content' => 'Saya sudah share ke media sosial. Semoga makin banyak yang tahu.',
                'is_public' => true,
            ],
            [
                'content' => 'Terima kasih sudah memberikan kesempatan untuk berkontribusi.',
                'is_public' => true,
            ],
            [
                'content' => 'Semoga dana yang terkumpul bisa digunakan dengan maksimal.',
                'is_public' => true,
            ],
            [
                'content' => 'Kampanye ini sangat penting dan urgent. Semoga cepat tercapai targetnya.',
                'is_public' => true,
            ],
            [
                'content' => 'Saya akan ajak keluarga untuk ikut berdonasi juga.',
                'is_public' => true,
            ],
            [
                'content' => 'Dokumentasi kampanye ini sangat baik dan informatif.',
                'is_public' => true,
            ],
            [
                'content' => 'Semoga Allah mudahkan segala urusan dan lancarkan kampanye ini.',
                'is_public' => true,
            ],
            [
                'content' => 'Terima kasih sudah menjadi jembatan kebaikan untuk banyak orang.',
                'is_public' => true,
            ],
            [
                'content' => 'Saya berharap ada update rutin tentang penggunaan dana donasi.',
                'is_public' => true,
            ],
            [
                'content' => 'Kampanye ini mengingatkan kita untuk selalu peduli sesama.',
                'is_public' => true,
            ],
            [
                'content' => 'Semoga bisa menjadi inspirasi untuk kampanye-kampanye kebaikan lainnya.',
                'is_public' => true,
            ],
            [
                'content' => 'Saya sangat mengapresiasi transparansi dalam pengelolaan donasi.',
                'is_public' => true,
            ],
            [
                'content' => 'Terima kasih sudah memfasilitasi kami untuk berbuat kebaikan.',
                'is_public' => true,
            ],
            [
                'content' => 'Semoga kampanye ini bisa membantu meringankan beban mereka yang membutuhkan.',
                'is_public' => true,
            ],
            [
                'content' => 'Saya akan terus mendukung dan mengikuti perkembangan kampanye ini.',
                'is_public' => true,
            ],
            [
                'content' => 'Bagaimana cara kami bisa membantu menyebarkan informasi kampanye ini?',
                'is_public' => true,
            ],
            [
                'content' => 'Terima kasih sudah memberikan platform yang aman untuk berdonasi.',
                'is_public' => true,
            ],
            [
                'content' => 'Semoga Allah memberikan keberkahan untuk semua yang terlibat.',
                'is_public' => true,
            ],
        ];

        // Additional private comments for testing
        $privateComments = [
            [
                'content' => 'Saya ingin memberikan saran untuk perbaikan kampanye ini secara pribadi.',
                'is_public' => false,
            ],
            [
                'content' => 'Ada beberapa hal yang perlu diperbaiki dalam pengelolaan kampanye.',
                'is_public' => false,
            ],
            [
                'content' => 'Saya memiliki pertanyaan khusus tentang penggunaan dana.',
                'is_public' => false,
            ],
        ];

        $allComments = array_merge($comments, $privateComments);

        // Create comments for each campaign
        foreach ($campaigns as $campaign) {
            // Randomly select 5-15 comments per campaign
            $numComments = rand(5, 15);
            $selectedComments = collect($allComments)->random($numComments);

            foreach ($selectedComments as $commentData) {
                Comment::create([
                    'campaign_id' => $campaign->id,
                    'user_id' => $users->random()->id,
                    'content' => $commentData['content'],
                    'is_public' => $commentData['is_public'],
                    'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                    'updated_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                ]);
            }
        }

        $this->command->info('Comments seeded successfully!');
    }
}
