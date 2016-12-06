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
     * @apiDefine ParamArticleId
     * @apiParam {Number} id Articles unique ID.
     */
    /**
     * @apiDefine ParamTitle
     * @apiParam {String{最大255}} title 文章标题
     */

    /**
     * @apiDefine ParamContent
     * @apiParam {String} content 文章正文
     */

    /**
     * @apiDefine ArticleObject
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "id": 1,
     *      "user_id": 1,
     *      "version_id": null,
     *      "status": "draft",
     *      "created_at": "2016-12-06 07:49:16",
     *      "updated_at": "2016-12-06 07:49:16"
     *  }
     */

    /**
     * @apiDefine NotFound
     * @apiErrorExample {json} NotFound-Response:
     *  HTTP/1.1 404 Not Found
     *  {
     *      "msg": "资源不存在"
     *  }
     */

    /**
     * @apiDefine Unauthorized
     * @apiErrorExample {json} Unauthorized-Response:
     *  HTTP/1.1 401 Unauthorized
     *  {
     *      "msg": "未授权"
     *  }
     */



    /**
     * @api {get} /articles 文章列表
     * @apiGroup Articles
     * @apiUse ArticleObject
     * @apiUse Unauthorized
     * @apiUse NotFound
     */

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
        return abort(404);
    }

    /**
     * @api {post} /articles 新建文章
     * @apiGroup Articles
     * @apiUse ParamTitle
     * @apiUse ParamContent
     * @apiParamExample {json} Request-Example:
     * {
     *      "title": "标题",
     *      "content": "内容"
     * }
     * @apiUse ArticleObject
     * @apiUse Unauthorized
     * @apiUse NotFound
     */

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
     * @api {get} /articles/:id 获取指定文章
     * @apiGroup Articles
     * @apiUse ParamArticleId
     * @apiUse ArticleObject
     * @apiUse Unauthorized
     * @apiUse NotFound
     */

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
        return abort(404);
    }

    /**
     * @api {put} /articles/:id 更新指定文章
     * @apiGroup Articles
     * @apiUse ParamArticleId
     * @apiUse ParamTitle
     * @apiUse ParamContent
     * @apiParamExample {json} Request-Example:
     * {
     *      "title": "又一个标题标题",
     *      "content": "新的内容"
     * }
     * @apiUse ArticleObject
     * @apiUse Unauthorized
     * @apiUse NotFound
     */

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
     * @api {delete} /articles/:id 删除指定文章
     * @apiDescription 彻底删除对象
     * @apiGroup Articles
     * @apiUse ParamArticleId
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *
     * @apiUse Unauthorized
     * @apiUse NotFound
     */

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
     * @api {post} /articles/:id/publish 发布指定文章
     * @apiGroup Articles
     * @apiUse ParamArticleId
     * @apiUse ArticleObject
     * @apiUse Unauthorized
     * @apiUse NotFound
     */

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

    /**
     * @api {post} /articles/:id/unpublish 移除发布指定文章
     * @apiGroup Articles
     * @apiUse ParamArticleId
     * @apiUse ArticleObject
     * @apiUse Unauthorized
     * @apiUse NotFound
     */
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

    /**
     * @api {post} /articles/:id/trash 将指定文章放入回收站
     * @apiGroup Articles
     * @apiUse ParamArticleId
     * @apiUse ArticleObject
     * @apiUse Unauthorized
     * @apiUse NotFound
     */
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

    /**
     * @api {post} /articles/:id/untrash 从收站取回指定文章
     * @apiGroup Articles
     * @apiUse ParamArticleId
     * @apiUse ArticleObject
     * @apiUse Unauthorized
     * @apiUse NotFound
     */
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
     * @api {post} /articles/:id/cover 上传文章封面图片
     * @apiGroup Articles
     * @apiUse ParamArticleId
     * @apiParam {File{最大5M}} cover 封面图片
     * @apiUse ArticleObject
     * @apiUse Unauthorized
     * @apiUse NotFound
     */

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
            throw new ModelNotFoundException();
        }

        return $article;
    }
}
