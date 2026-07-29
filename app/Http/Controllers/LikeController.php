<?php

namespace App\Http\Controllers;

use Phaseolies\Utilities\Attributes\Route;
use Phaseolies\Utilities\Attributes\Model as RouteModel;
use App\Models\Like;
use App\Models\Post;
use Phaseolies\Support\Facades\Auth;
use Phaseolies\Http\Response;

class LikeController extends Controller
{
    #[Route(uri: 'posts/{post}/like', name: 'posts.like.toggle', methods: ['POST'], middleware: ['auth'])]
    public function toggle(#[RouteModel(exception: true)] Post $post): Response
    {
        $existingLike = Like::where('post_id', $post->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            Like::create([
                'post_id' => $post->id,
                'user_id' => Auth::id(),
            ]);
            $liked = true;
        }

        $likeCount = Like::where('post_id', $post->id)->count();

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'like_count' => $likeCount,
        ]);
    }
}
