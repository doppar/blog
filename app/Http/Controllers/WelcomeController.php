<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsPostFeed;
use App\Models\Post;
use Phaseolies\Http\Request;
use Phaseolies\Utilities\Attributes\Route;
use Phaseolies\Utilities\Attributes\Model as RouteModel;

class WelcomeController extends Controller
{
    use BuildsPostFeed;

    #[Route(uri: '/', name: 'home')]
    public function welcome(Request $request)
    {
        $perPage = 5;
        $cursorColumn = 'id';
        $direction = 'desc';

        $query = Post::query()
            ->where('status', 'published')
            ->embed('category:name')
            ->if($request->tab === 'featured', fn($query) => $query->where('is_featured', true));

        $posts = $query->cursorPaginate(
            perPage: $perPage,
            cursorColumn: $cursorColumn,
            direction: $direction,
            cursor: $request->query('cursor')
        );

        $posts['data'] = $this->decoratePostsWithAccent($posts['data'] ?? []);

        $tab = $request->input('tab', 'for-you');

        $loadMore = $this->resolveLoadMoreState(
            query: $query,
            paginated: $posts,
            perPage: $perPage,
            cursorColumn: $cursorColumn,
            direction: $direction,
            buildUrl: fn($cursor) => '/?tab=' . urlencode($tab) . '&cursor=' . urlencode($cursor)
        );

        if ($request->isAjax()) {
            return response()->json([
                'html' => view('partials.post-list-items', ['posts' => $posts['data']])->render(),
                'next_cursor' => $loadMore['next_cursor'],
                'next_url' => $loadMore['next_url'],
                'has_more' => $loadMore['has_more'],
            ]);
        }

        $showEmptyState = empty($posts['data']) && !$loadMore['has_more'];

        return view('welcome', compact('posts', 'tab', 'loadMore', 'showEmptyState'));
    }

    #[Route(uri: '/posts/{post}', name: 'post.show')]
    public function show(#[RouteModel(exception: true)] Post $post)
    {
        $viewedPosts = session('viewed_posts', []);

        if (!in_array($post->id, $viewedPosts, true)) {
            $post->increment('view_count');
            $viewedPosts[] = $post->id;
            session()->put('viewed_posts', $viewedPosts);
        }

        return view('post', compact('post'));
    }
}
