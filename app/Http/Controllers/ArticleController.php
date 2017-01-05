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

        $data = [];
        foreach ($articles as $article) {
            try {
                $data[] = $this->filterArticleData($article);
            } catch (BadRequestException $e) {

            }
        }

        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return abort(404);
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
        $author = $request->input('author');

        $user = Auth::user();
        $article = new Article();
        $article['status'] = Article::STATUS_DRAFT;
        $article['author'] = $author;
        $user->articles()->save($article);
        $articleVersion = new ArticleVersion();
        $articleVersion['title'] = $title;
        $articleVersion['content'] = $content;
        $articleVersion->save();
        $article->draftVersion()->associate($articleVersion);
        $article->save();

        $data = $this->filterArticleData($article);
//        $data['show_url'] = '/p/' . $data['title'] . '-' . date('Y-m-d', strtotime($data['created_at']));
        $data['show_url'] = '/a/' . $data['id'];

        return response()->json($data);
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
        $data = $this->filterArticleData($article);

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return abort(404);
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
        $data = $this->filterArticleData($article);

        return response()->json($data);
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
//        $article = $this->updateArticle($request, $id);
        $article = Article::findOrFail($id);

        if($article['status'] === Article::STATUS_DRAFT
            || $article['status'] === Article::STATUS_PUBLISHED_WITH_DRAFT) {

            if($article['status'] === Article::STATUS_DRAFT) {
                $article['publish_version_id'] = $article['draft_version_id'];
                $article['draft_version_id'] = null;
            }
            $article['status'] = Article::STATUS_PUBLISHED;
            $article->save();

            $data = $this->filterArticleData($article);

            return response()->json($data);
        }

        throw new BadRequestException('操作失败');
    }

    public function unpublish(Request $request, $id)
    {
        $article = $this->updateArticle($request, $id);
        if($article['status'] == Article::STATUS_PUBLISHED) {
            $article['status'] = Article::STATUS_DRAFT;
            $article->save();
            $data = $this->filterArticleData($article);

            return response()->json($data);
        }

        throw new BadRequestException('操作失败');
    }

    public function trash(Request $request, $id)
    {
        $article = $this->updateArticle($request, $id);
        if($article['status'] == Article::STATUS_DRAFT) {
            $article['status'] = Article::STATUS_TRASHED;
            $article->save();
            $data = $this->filterArticleData($article);

            return response()->json($data);
        }

        throw new BadRequestException('操作失败');
    }

    public function untrash(Request $request, $id)
    {
        $article = $this->updateArticle($request, $id);
        if($article['status'] == Article::STATUS_TRASHED) {
            $article['status'] = Article::STATUS_DRAFT;
            $article->save();
            $data = $this->filterArticleData($article);

            return response()->json($data);
        }

        throw new BadRequestException('操作失败');

    }

    /**
     * 上传文章封面图片
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws BadRequestException
     */
    public function uploadCover(Request $request, $id)
    {
        $this->validate($request, [
            'cover' => 'required|max:5000',
        ]);

        $article = $this->findArticle($id);

        $coverImage = $request->file('cover');
        $filePath = $coverImage->store('img/cover');

        $articleVersion = new ArticleVersion();
        $articleVersion['cover_url'] = $request->getSchemeAndHttpHost() . '/' . $filePath;
        $articleVersion->save();
        $article->draftVersion()->associate($articleVersion);
        $article->save();

        $data = $this->filterArticleData($article);

        return response()->json($data);
    }

    /**
     * 获取文章所有评论
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function comments($id)
    {
        $article = Article::findOrFail($id);

        return response()->json($article->comments, 200);
    }

    public function read($id)
    {
        $article = $this->findArticle($id);
        $data = $this->filterArticleData($article);

        $data['show_edit_button'] = 'false';
        $data['is_read_only'] = 'true';
        return view('tp', $data);
    }

    private function updateArticle(Request $request, $id) : Article
    {
        $article = $this->findArticle($id);

        $title = $request->input('title');
        $content = $request->input('content');

        $articleVersion = new ArticleVersion();
        $articleVersion['title'] = $title;
        $articleVersion['content'] = $content;

        $status = $article['status'];
        if($status === Article::STATUS_PUBLISHED) {
            $article['status'] = Article::STATUS_PUBLISHED_WITH_DRAFT;
        }
        $article->draftVersion()->associate($articleVersion);
        $article->save();

        return $article;
    }

    private function findArticle($articleId) : Article
    {
        $user = Auth::user();
        $article = Article::where('user_id', $user['id'])
            ->where('id', $articleId)
            ->first();

        if(empty($article)) {
            throw new ModelNotFoundException();
        }

        return $article;
    }

    private function filterArticleData(Article $article)
    {
        $status = $article['status'];

        if($status === Article::STATUS_DRAFT) {
            $version = $article->draftVersion;
        } else if($status === Article::STATUS_PUBLISHED) {
            $version = $article->publishedVersion;
        }

        if(empty($version)) {
            throw new BadRequestException('文章版本不存在');
        }

        $data = [
            'id' => $article['id'],
            'author' => $article['author'],
            'cover_url' => $version['cover_url'],
            'title' => $version['title'],
            'content' => $version['content'],
            'read_time' => $this->readTime($version['content']),
            'status' => $article['status'],
            'created_at' => strval($article['created_at']),
            'updated_at' => strval($article['updated_at']),
        ];

        return $data;
    }

    private function readTime($content)
    {
        $count = mb_strlen($content, 'UTF-8');

        return ceil($count / 500);
    }
}
