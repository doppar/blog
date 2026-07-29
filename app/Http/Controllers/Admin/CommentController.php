<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReplyToCommentRequest;
use App\Models\Comment;
use Phaseolies\Http\Request;
use Phaseolies\Http\Response;
use Phaseolies\Support\Facades\Auth;
use Phaseolies\Utilities\Attributes\Mapper;
use Phaseolies\Utilities\Attributes\Middleware;
use Phaseolies\Utilities\Attributes\Model as RouteModel;
use Phaseolies\Utilities\Attributes\Route;

#[Middleware(['auth', 'admin'])]
#[Mapper(prefix: 'admin/comments')]
class CommentController extends Controller
{
    #[Route(uri: '/', name: 'admin.comments.index')]
    public function index(Request $request, Comment $comment): Response
    {
        $search = (string) trim($request->q);

        $comments = $comment->embed(['user:name,image', 'post:title,slug', 'parent:body'])
            ->embedCount('likes')
            ->if($search, fn($q) => $q->search(['body', 'user.name', 'post.title'], $search))
            ->if($request->status, fn($q) => $q->where('status', $request->status === 'approved'))
            ->orderBy('id', 'DESC')
            ->paginate(15);

        $totalComments = Comment::count();
        $approvedComments = Comment::where('status', true)->count();
        $disapprovedComments = Comment::where('status', false)->count();

        return view('admin.comments.index', compact(
            'comments',
            'search',
            'totalComments',
            'approvedComments',
            'disapprovedComments'
        ));
    }

    #[Route(uri: '/{comment}/approve', methods: ['PUT'], name: 'admin.comments.approve')]
    public function approve(#[RouteModel(exception: true)] Comment $comment): Response
    {
        $comment->update(['status' => true]);

        return back()->withSuccess('Comment approved.');
    }

    #[Route(uri: '/{comment}/disapprove', methods: ['PUT'], name: 'admin.comments.disapprove')]
    public function disapprove(#[RouteModel(exception: true)] Comment $comment): Response
    {
        $comment->update(['status' => false]);

        return back()->withSuccess('Comment disapproved.');
    }

    #[Route(uri: '/{comment}/reply', methods: ['POST'], name: 'admin.comments.reply')]
    public function reply(ReplyToCommentRequest $request, #[RouteModel(exception: true)] Comment $comment): Response
    {
        Comment::create([
            'post_id' => $comment->post_id,
            'user_id' => Auth::id(),
            'parent_id' => $comment->id,
            'body' => $request->passed()['body'],
            'status' => true,
        ]);

        return back()->withSuccess('Reply posted successfully.');
    }

    #[Route(uri: '/{comment}', methods: ['DELETE'], name: 'admin.comments.destroy')]
    public function destroy(#[RouteModel(exception: true)] Comment $comment): Response
    {
        $comment->delete();

        return back()->withSuccess('Comment deleted successfully.');
    }
}
