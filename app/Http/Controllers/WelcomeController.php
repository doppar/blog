<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Phaseolies\Http\Request;
use Phaseolies\Utilities\Attributes\Route;
use Phaseolies\Http\Response;
use Phaseolies\Utilities\Attributes\Model as RouteModel;

class WelcomeController extends Controller
{
    /**
     * Palette accents cycled per post for the list view.
     * Each entry is [solid CSS colour, soft tinted background].
     */
    private const ACCENT_PALETTE = [
        ['var(--c-primary)', 'rgba(109,108,243,.12)'],
        ['var(--c-cyan)',    'rgba(28,176,238,.12)'],
        ['var(--c-green)',   'rgba(54,202,132,.14)'],
        ['var(--c-amber)',   'rgba(243,177,26,.16)'],
        ['var(--c-pink)',    'rgba(214,104,135,.14)'],
        ['var(--c-gold)',    'rgba(148,107,1,.14)'],
    ];

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

        $loadMore = $this->resolveLoadMoreState(
            query: $query,
            posts: $posts,
            tab: $tab = $request->input('tab', 'for-you'),
            perPage: $perPage,
            cursorColumn: $cursorColumn,
            direction: $direction
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

    /**
     * Attach view-friendly presentation attributes to each post:
     *  - accent_solid : CSS colour for emphasis
     *  - accent_soft  : tinted background colour
     *  - author_initial : single uppercase letter for the avatar
     *
     * The accent is derived from the post id so it stays stable
     * across pagination and AJAX loads.
     */
    private function decoratePostsWithAccent(array $posts): array
    {
        $palette = self::ACCENT_PALETTE;
        $size = count($palette);

        foreach ($posts as $post) {
            $bucket = ((int) ($post->id ?? 0)) % $size;
            [$solid, $soft] = $palette[$bucket];

            $post->accent_solid = $solid;
            $post->accent_soft  = $soft;
            $post->author_initial = strtoupper(substr((string) ($post->author_name ?? 'A'), 0, 1));
        }

        return $posts;
    }

    #[Route(uri: '/posts/{post}', name: 'post.show')]
    public function show(#[RouteModel(exception: true)] Post $post)
    {
        return view('post', compact('post'));
    }

    private function resolveLoadMoreState(
        object $query,
        array $posts,
        string $tab,
        int $perPage,
        string $cursorColumn,
        string $direction
    ): array {
        $items = $posts['data'] ?? [];
        $nextCursor = $posts['next_cursor'] ?? null;
        $hasMore = (bool) ($posts['has_more'] ?? false);

        if (!$hasMore && count($items) === $perPage && !empty($items)) {
            $lastPost = end($items);
            $lastValue = $lastPost->{$cursorColumn} ?? null;

            if ($lastValue !== null) {
                $lookAhead = clone $query;
                $operator = strtolower($direction) === 'desc' ? '<' : '>';

                $hasMore = $lookAhead
                    ->where($cursorColumn, $operator, $lastValue)
                    ->limit(1)
                    ->exists();

                if ($hasMore && empty($nextCursor)) {
                    $nextCursor = base64_encode(json_encode(['v' => $lastValue]));
                }
            }
        }

        $nextUrl = null;

        if ($hasMore && !empty($nextCursor)) {
            $nextUrl = '/?tab=' . urlencode($tab) . '&cursor=' . urlencode($nextCursor);
        }

        return [
            'has_more' => $hasMore,
            'next_cursor' => $nextCursor,
            'next_url' => $nextUrl,
        ];
    }
}
