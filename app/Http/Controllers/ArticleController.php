<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Models\Article;
use App\Models\ArticleVersion;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    /**
     * Article list
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::all();

        return response()->json($articles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created article
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $title = $request->input('title');
        $content = $request->input('content');

        $user = Auth::user();
        $article = new Article();
        $article['status'] = Article::STATUS_DRAFT;
        $user->articles()->save($article);
        $articleVersion = new ArticleVersion();
        $articleVersion['title'] = $title;
        $articleVersion['content'] = $content;
        $article->versions()->save($articleVersion);
//        $articleVersion->article()->associate($article);
//        $article->currentVersion()->save($articleVersion);

        return response()->json($articleVersion->article);
    }

    /**
     * Display the specified article.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws BadRequestException
     */
    public function show($id)
    {
        $article = $this->findArticle($id);

        return response()->json($article);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort(404);
    }

    /**
     * Update the specified article.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws BadRequestException
     */
    public function update(Request $request, $id)
    {
        $article = $this->updateArticle($request, $id);
        $article->save();

        return response()->json($article);
    }

    /**
     * Remove the specified article.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $article = $this->findArticle($id);
        $article->delete();

        return response('', 200);
    }

    /**
     * Publish the specified article
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws BadRequestException
     */
    public function publish(Request $request, $id)
    {
        $article = $this->updateArticle($request, $id);
        if($article['status'] == Article::STATUS_DRAFT) {
            $article['status'] = Article::STATUS_PUBLISHED;
            $article->save();
            return response()->json($article);
        }

        throw new BadRequestException('操作失败');
    }

    public function unpublish(Request $request, $id)
    {
        $article = $this->updateArticle($request, $id);
        if($article['status'] == Article::STATUS_PUBLISHED) {
            $article['status'] = Article::STATUS_DRAFT;
            $article->save();
            return response()->json($article);
        }

        throw new BadRequestException('操作失败');
    }

    public function trash(Request $request, $id)
    {
        $article = $this->updateArticle($request, $id);
        if($article['status'] == Article::STATUS_DRAFT) {
            $article['status'] = Article::STATUS_TRASHED;
            $article->save();
            return response()->json($article);
        }

        throw new BadRequestException('操作失败');
    }

    public function untrash(Request $request, $id)
    {
        $article = $this->updateArticle($request, $id);
        if($article['status'] == Article::STATUS_TRASHED) {
            $article['status'] = Article::STATUS_DRAFT;
            $article->save();
            return response()->json($article);
        }

        throw new BadRequestException('操作失败');

    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws BadRequestException
     */
    public function uploadCover(Request $request, $id)
    {
        $this->validate($request, [
            'cover_url' => 'required|max:5000',
        ]);

        $article = $this->findArticle($id);

        $coverImage = $request->file('cover_url');
        $filePath = $coverImage->store('cover');

        $articleVersion = new ArticleVersion();
        $articleVersion['cover_url'] = $filePath;
        $article->versions()->save($articleVersion);

        return response()->json($article);
    }

    private function updateArticle(Request $request, $id) : Article
    {
        $article = $this->findArticle($id);

        $title = $request->input('title');
        $content = $request->input('content');

        $articleVersion = new ArticleVersion();
        $articleVersion['title'] = $title;
        $articleVersion['content'] = $content;
        $article->versions()->save($articleVersion);

        return $article;
    }

    private function findArticle($articleId) : Article
    {
        $user = Auth::user();
        $article = Article::where('user_id', $user['id'])
            ->where('id', $articleId)
            ->first();

        if(empty($article)) {
            throw new ModelNotFoundException("该文章不存在");
        }

        return $article;
    }
}
