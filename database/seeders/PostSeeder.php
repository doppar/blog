<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Support\CmsSlugger;
use Phaseolies\Database\Migration\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()->orderBy('id')->get();
        $tags = Tag::query()->orderBy('id')->get();

        if ($categories->count() === 0 || $tags->count() === 0) {
            return;
        }

        for ($index = 1; $index <= 18; $index++) {
            $category = $categories[($index - 1) % $categories->count()];
            $status = $index % 5 === 0 ? 'draft' : 'published';
            $title = fake()->unique()->sentence(mt_rand(4, 7));
            $publishedAt = $status === 'published'
                ? date('Y-m-d H:i:s', strtotime("-{$index} days"))
                : null;

            $post = Post::create([
                'category_id' => $category->id,
                'title' => $title,
                'slug' => CmsSlugger::unique(Post::class, $title),
                'excerpt' => fake()->paragraph(2),
                'body' => implode("\n\n", fake()->paragraphs(5)),
                'cover_image' => "https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80&seed={$index}",
                'author_name' => fake()->name(),
                'status' => $status,
                'is_featured' => $index % 4 === 0,
                'published_at' => $publishedAt,
                'view_count' => fake()->numberBetween(80, 3200),
                'seo_title' => substr($title . ' | Editorial Desk', 0, 255),
                'seo_description' => substr(fake()->sentence(12), 0, 255),
            ]);

            $postTagIds = [];

            for ($tagIndex = 0; $tagIndex < 3; $tagIndex++) {
                $tag = $tags[($index + $tagIndex) % $tags->count()];
                $postTagIds[] = $tag->id;
            }

            $post->tags()->relate(array_unique($postTagIds));
        }
    }
}
