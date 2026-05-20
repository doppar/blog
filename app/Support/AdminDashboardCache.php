<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;

final class AdminDashboardCache
{
    public const COUNTS_KEY = 'admin.dashboard.counts';

    public static function counts(): array
    {
        return cache()->stash(self::COUNTS_KEY, 600, function (): array {
            return [
                'totalPosts' => Post::query()->count(),
                'totalCategories' => Category::query()->count(),
                'totalTags' => Tag::query()->count(),
            ];
        });
    }

    public static function forgetCounts(): void
    {
        cache()->delete(self::COUNTS_KEY);
    }
}
