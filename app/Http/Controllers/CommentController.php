<?php

namespace App\Http\Controllers;

use Phaseolies\Utilities\Attributes\Route;
use Phaseolies\Utilities\Attributes\Model as RouteModel;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Phaseolies\Support\Facades\Auth;
use Phaseolies\Http\Request;
use Phaseolies\Http\Response;

class CommentController extends Controller
{
    #[Route(uri: 'posts/{post}/comments', name: 'comments.index', methods: ['GET'])]
    public function index(Request $request, #[RouteModel(exception: true)] Post $post): Response
    {
        $query = $post->comments()
            ->where('status', true)
            ->whereNull('parent_id')
            ->embed(['user', 'replies.user']);

        $query = $request->input('sort') === 'oldest' ? $query->oldest() : $query->newest();

        $paginated = $query->paginate(Comment::PER_PAGE);

        $html = '';

        foreach ($paginated['data'] as $comment) {
            $html .= view('partials.comment', ['comment' => $comment, 'postAuthorId' => $post->user_id])->render();
        }

        return response()->json([
            'success' => true,
            'html' => $html,
            'has_more' => $paginated['next_page_url'] !== null,
            'next_page' => $paginated['current_page'] + 1,
        ]);
    }

    #[Route(uri: 'posts/{post}/comments', name: 'comments.store', methods: ['POST'], middleware: ['auth'])]
    public function store(StoreCommentRequest $request, #[RouteModel(exception: true)] Post $post): Response
    {
        $data = $request->passed();
        $parentId = $data['parent_id'] ?? null;

        if ($parentId) {
            $parent = Comment::find($parentId);

            if (!$parent || (int) $parent->post_id !== (int) $post->id) {
                return response()->json(['success' => false, 'message' => 'Parent comment not found for this post'], 422);
            }
        }

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'parent_id' => $parentId,
            'body' => $data['body'],
            'status' => true,
        ]);

        // create() doesn't populate created_at/updated_at back onto the
        // in-memory instance (they're only written to the SQL insert), so
        // re-fetch before reading them.
        $comment = Comment::find($comment->id);

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'body' => $comment->body,
                'created_at' => $comment->created_at->diffForHumans(),
                'user' => [
                    'name' => $comment->user->name,
                    'avatar' => $comment->user->image ?? null,
                ],
                'parent_id' => $comment->parent_id,
                'is_post_author' => (int) $comment->user_id === (int) $post->user_id,
            ],
        ]);
    }

    #[Route(uri: 'comments/{comment}', name: 'comments.update', methods: ['PUT'], middleware: ['auth'])]
    public function update(UpdateCommentRequest $request, #[RouteModel(exception: true)] Comment $comment): Response
    {
        if ((int) $comment->user_id !== (int) Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $comment->update([
            'body' => $request->passed()['body'],
        ]);

        // update() doesn't sync updated_at back onto the in-memory instance
        // either, so re-fetch to get the fresh timestamp for the response.
        $comment = Comment::find($comment->id);

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'body' => $comment->body,
                'updated_at' => $comment->updated_at->diffForHumans(),
            ],
        ]);
    }

    #[Route(uri: 'comments/{comment}', name: 'comments.destroy', methods: ['DELETE'], middleware: ['auth'])]
    public function destroy(#[RouteModel(exception: true)] Comment $comment): Response
    {
        if ((int) $comment->user_id !== (int) Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['success' => true]);
    }
}
