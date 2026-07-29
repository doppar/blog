<?php

namespace App\Http\Controllers;

use Phaseolies\Utilities\Attributes\Route;
use Phaseolies\Utilities\Attributes\Model as RouteModel;
use App\Models\Comment;
use App\Models\CommentLike;
use Phaseolies\Support\Facades\Auth;
use Phaseolies\Http\Response;

class CommentLikeController extends Controller
{
    #[Route(uri: 'comments/{comment}/like', name: 'comments.like.toggle', methods: ['POST'], middleware: ['auth'])]
    public function toggle(#[RouteModel(exception: true)] Comment $comment): Response
    {
        $existingLike = CommentLike::where('comment_id', $comment->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            CommentLike::create([
                'comment_id' => $comment->id,
                'user_id' => Auth::id(),
            ]);
            $liked = true;
        }

        $likeCount = CommentLike::where('comment_id', $comment->id)->count();

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'like_count' => $likeCount,
        ]);
    }
}
