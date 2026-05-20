<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Phaseolies\Http\Response;
use Phaseolies\Utilities\Attributes\Mapper;
use Phaseolies\Utilities\Attributes\Route;

#[Mapper(prefix: 'admin')]
class DashboardController extends Controller
{
    #[Route(uri: '/', name: 'admin.dashboard')]
    public function index(): Response
    {
        $recentPosts = Post::query()
            ->embed(['category'])
            ->orderBy('updated_at', 'DESC')
            ->limit(6)
            ->get();

        $categorySummary = Category::query()
            ->embedCount('posts')
            ->orderBy('name')
            ->get();

        $tagSummary = Tag::query()
            ->embedCount('posts')
            ->orderBy('name')
            ->limit(8)
            ->get();

        $totalPosts = Post::query()->count();
        $publishedPosts = Post::published()->count();

        return view('admin.dashboard', [
            'totalPosts' => $totalPosts,
            'publishedPosts' => $publishedPosts,
            'draftPosts' => Post::draft()->count(),
            'featuredPosts' => Post::featured()->count(),
            'totalCategories' => Category::query()->count(),
            'totalTags' => Tag::query()->count(),
            'newThisMonth' => Post::query()->whereThisMonth('created_at')->count(),
            'publishedRatio' => $totalPosts > 0 ? (int) round(($publishedPosts / $totalPosts) * 100) : 0,
            'recentPosts' => $recentPosts,
            'categorySummary' => $categorySummary,
            'tagSummary' => $tagSummary,
        ]);
    }
}
