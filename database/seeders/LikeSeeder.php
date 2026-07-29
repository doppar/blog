<?php

namespace Database\Seeders;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Phaseolies\Database\Migration\Seeder;

class LikeSeeder extends Seeder
{
    public function run(): void
    {
        if (Like::count() > 0) {
            return;
        }

        $posts = Post::query()->where('status', 'published')->orderBy('id')->get();
        $userIds = array_map(fn($user) => $user->id, User::query()->orderBy('id')->get()->all());

        if ($posts->count() === 0 || count($userIds) === 0) {
            return;
        }

        foreach ($posts as $post) {
            $likerIds = $userIds;
            shuffle($likerIds);

            // Between zero and "everyone" likes a given post, skewed toward
            // partial engagement rather than all-or-nothing.
            $likerCount = mt_rand(0, count($likerIds));
            $likerIds = array_slice($likerIds, 0, $likerCount);

            foreach ($likerIds as $userId) {
                Like::create([
                    'post_id' => $post->id,
                    'user_id' => $userId,
                ]);
            }
        }
    }
}
