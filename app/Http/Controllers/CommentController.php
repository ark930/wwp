<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index($id)
    {
        $comment = Comment::findOrFail($id);

        return response($comment, 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $articleId = $request->input('article_id');
        $content = $request->input('comment');

        $article = Article::findOrFail($articleId);

        $comment = new Comment();
        $comment['user_id'] = $user['id'];
        $comment['comment'] = $content;

        $article->comments()->save($comment);

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

    public function reply(Request $request, $id)
    {
        $user = Auth::user();

        $content = $request->input('reply');

        $comment = Comment::findOrFail($id);
        $article = $comment->article;
        $writer = $article->writer;

        if($user['id'] != $writer['id']) {
            throw new AuthenticationException();
        }

        $comment['reply'] = $content;
        $comment->save();

        $comment = Comment::findOrFail($id);

        return response($comment, 200);
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response('', 204);
    }
}
