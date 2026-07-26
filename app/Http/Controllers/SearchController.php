<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Phaseolies\Http\Request;
use Phaseolies\Http\Response;
use Phaseolies\Utilities\Attributes\Route;

class SearchController extends Controller
{
    #[Route(uri: '/api/search/posts', name: 'search.posts')]
    public function posts(Request $request): Response
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $posts = Post::query()
            ->published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('body', 'like', "%{$query}%");
            })
            ->embed(['category:name', 'user:name'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($post) {
                $createdAt = $post->created_at;
                if (is_string($createdAt)) {
                    $formattedDate = date('M j, Y', strtotime($createdAt));
                } else {
                    $formattedDate = $createdAt?->format('M j, Y');
                }

                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => strip_tags(substr($post->body, 0, 150)) . '...',
                    'url' => "/posts/{$post->slug}",
                    'category' => $post->category?->name,
                    'author' => $post->user?->name,
                    'created_at' => $formattedDate,
                ];
            });

        return response()->json($posts);
    }
}
