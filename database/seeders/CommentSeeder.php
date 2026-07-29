<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Phaseolies\Database\Migration\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        if (Comment::count() > 0) {
            return;
        }

        $posts = Post::query()->where('status', 'published')->orderBy('id')->get();
        $users = User::query()->orderBy('id')->get();

        if ($posts->count() === 0 || $users->count() === 0) {
            return;
        }

        foreach ($posts as $post) {
            // Not every post gets discussion.
            if (mt_rand(1, 100) > 70) {
                continue;
            }

            $topLevelCount = mt_rand(1, 4);

            for ($i = 0; $i < $topLevelCount; $i++) {
                $author = $users[mt_rand(0, $users->count() - 1)];

                $comment = Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $author->id,
                    'parent_id' => null,
                    'body' => fake()->paragraph(mt_rand(1, 3)),
                    'status' => mt_rand(1, 100) <= 90,
                ]);

                $replyCount = mt_rand(0, 2);

                for ($r = 0; $r < $replyCount; $r++) {
                    $replier = $users[mt_rand(0, $users->count() - 1)];

                    Comment::create([
                        'post_id' => $post->id,
                        'user_id' => $replier->id,
                        'parent_id' => $comment->id,
                        'body' => fake()->sentence(mt_rand(6, 18)),
                        'status' => true,
                    ]);
                }
            }
        }
    }
}
