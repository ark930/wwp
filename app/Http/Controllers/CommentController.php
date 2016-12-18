<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function show($id)
    {
        $comment = Comment::findOrFail($id);

        return response($comment, 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $articleId = $request->input('article_id');
        $content = $request->input('comment');

        $comment = new Comment();
        $comment['article_id'] = $articleId;
        $comment['comment'] = $content;
        $comment->associate($user);
        $comment->save();

        return response($comment, 200);

    }

    public function update(Request $request, $id)
    {
        $content = $request->input('comment');

        $comment = Comment::findOrFail($id);
        $comment['comment'] = $content;
        $comment->save();

        return response($comment, 200);

    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response('', 200);
    }
}
