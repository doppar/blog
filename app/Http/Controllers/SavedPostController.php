<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsPostFeed;
use Phaseolies\Utilities\Attributes\Route;
use Phaseolies\Utilities\Attributes\Model as RouteModel;
use App\Models\SavedPost;
use App\Models\Post;
use Phaseolies\Http\Request;
use Phaseolies\Support\Facades\Auth;
use Phaseolies\Http\Response;

class SavedPostController extends Controller
{
    use BuildsPostFeed;

    #[Route(uri: '/saved', name: 'saved.index', methods: ['GET'], middleware: ['auth'])]
    public function index(Request $request)
    {
        $perPage = 6;
        $cursorColumn = 'id';
        $direction = 'desc';

        $query = SavedPost::where('user_id', Auth::id());

        $saved = $query->cursorPaginate(
            perPage: $perPage,
            cursorColumn: $cursorColumn,
            direction: $direction,
            cursor: $request->query('cursor')
        );

        $posts = $this->decoratePostsWithAccent(
            array_values(array_filter(array_map(
                fn($savedPost) => $savedPost->post,
                $saved['data'] ?? []
            )))
        );

        $loadMore = $this->resolveLoadMoreState(
            query: $query,
            paginated: $saved,
            perPage: $perPage,
            cursorColumn: $cursorColumn,
            direction: $direction,
            buildUrl: fn($cursor) => '/saved?cursor=' . urlencode($cursor)
        );

        if ($request->isAjax()) {
            return response()->json([
                'html' => view('partials.post-list-items', ['posts' => $posts])->render(),
                'next_cursor' => $loadMore['next_cursor'],
                'next_url' => $loadMore['next_url'],
                'has_more' => $loadMore['has_more'],
            ]);
        }

        $showEmptyState = empty($posts) && !$loadMore['has_more'];

        return view('saved', compact('posts', 'loadMore', 'showEmptyState'));
    }

    #[Route(uri: 'posts/{post}/save', name: 'posts.save.toggle', methods: ['POST'], middleware: ['auth'])]
    public function toggle(#[RouteModel(exception: true)] Post $post): Response
    {
        $existingSave = SavedPost::where('post_id', $post->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingSave) {
            $existingSave->delete();
            $saved = false;
        } else {
            SavedPost::create([
                'post_id' => $post->id,
                'user_id' => Auth::id(),
            ]);
            $saved = true;
        }

        return response()->json([
            'success' => true,
            'saved' => $saved,
        ]);
    }
}
