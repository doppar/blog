<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Phaseolies\Http\Request;
use Phaseolies\Utilities\Attributes\Route;
use Phaseolies\Http\Response;

class WelcomeController extends Controller
{
    #[Route(uri: '/', name: 'home')]
    public function welcome(Request $request): Response
    {
        $tab = $request->input('tab', 'for-you');

        $query = Post::query()
            ->where('status', 'published')
            ->embed(['category'])
            ->newest('published_at');

        if ($tab === 'featured') {
            $query->where('is_featured', true);
        }

        $posts = $query->paginate(10);

        return view('welcome', compact('posts', 'tab'));
    }

    #[Route(uri: '/posts/{slug}', name: 'post.show')]
    public function show(string $slug): Response
    {
        $post = Post::query()
            ->where('status', 'published')
            ->where('slug', $slug)
            ->embed(['category'])
            ->first();

        if (!$post) {
            abort(404);
        }

        return view('post', compact('post'));
    }
}
