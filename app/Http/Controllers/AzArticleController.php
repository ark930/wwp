<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Models\Article;
use App\Models\ArticleVersion;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AzArticleController extends Controller
{
    const MODE_AUTHOR_EDIT = 'author-edit';
    const MODE_AUTHOR_READ = 'author-read';
    CONST MODE_AUDIENCE_READ = 'audience-read';

    public function index(Request $request)
    {
        $user = Auth::user();
        if(empty($user)) {
            $user = User::create([
                'tel' => str_random(10),
            ]);

            Auth::guard()->login($user);
            $request->session()->regenerate();
        }

        return view('tp', [
            'title' => 'A-Z.press',
            'author' => '',
            'html_content' => '',
            'text_content' => '',
            'description' => '开箱即写，发布到任何地方',
            'created_at' => '',
            'mode' => self::MODE_AUTHOR_EDIT,
            'read_time' => 0,
        ]);
    }

    public function publish(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'html_content' => 'required',
            'text_content' => 'required',
            'author' => 'nullable',
            'description' => 'nullable',
        ]);

        $title = $request->input('title');
        $htmlContent = $request->input('html_content');
        $textContent = $request->input('text_content');
        $author = $request->input('author');
        $description = $request->input('description');

        if(empty($author)) {
            $author = "";
        }

        if(empty($description)) {
            $description = "";
        }

        $user = Auth::user();
        if(empty($user)) {
            throw new AuthenticationException();
        }

        $article = new Article();
        $article['tag'] = $this->generateTag();
        $article['status'] = Article::STATUS_PUBLISHED;
        $article['author'] = $author;
        $user->articles()->save($article);
        $articleVersion = new ArticleVersion();
        $articleVersion['title'] = $title;
        $articleVersion['html_content'] = $htmlContent;
        $articleVersion['text_content'] = $textContent;
        $articleVersion['description'] = $description;
        $articleVersion->save();
        $article->publishedVersion()->associate($articleVersion);
        $article->save();

        $data = $this->filterArticleData($article);
        $data['show_url'] = $this->makeShowUrl($data['tag']);

        return response()->json($data);
    }

    public function read($tag)
    {
        $article = $this->findArticleByTag($tag);
        $data = $this->filterArticleData($article);

        $data['mode'] = self::MODE_AUDIENCE_READ;
        $user = Auth::user();
        if(!empty($user)) {
            $articleUser = $article->writer;
            if($user['id'] === $articleUser['id']) {
                $data['mode'] = self::MODE_AUTHOR_READ;
            }
        }

        return view('tp', $data);
    }

    public function editByTag(Request $request, $tag)
    {
        $this->validate($request, [
            'title' => 'required',
            'html_content' => 'required',
            'text_content' => 'required',
            'author' => 'nullable',
            'description' => 'nullable',
        ]);

        $user = Auth::user();
        if(empty($user)) {
            throw new AuthenticationException();
        }

        $article = $this->findArticleByTag($tag);
        $articleUser = $article->writer;
        if($user['id'] != $articleUser['id']) {
            throw new AuthenticationException();
        }

        $article = $this->updateArticle($request, $article);
        $article->save();

        $data = $this->filterArticleData($article);
        $data['mode'] = self::MODE_AUTHOR_READ;
        $data['show_url'] = $this->makeShowUrl($data['tag']);

        return response()->json($data);
    }

    private function makeShowUrl($tag)
    {
        return '/a/' . $tag;
    }

    private function updateArticle(Request $request, Article $article) : Article
    {
        $title = $request->input('title');
        $htmlContent = $request->input('html_content');
        $textContent = $request->input('text_content');
        $author = $request->input('author');
        $description = $request->input('description');

        if(empty($author)) {
            $author = "";
        }

        if(empty($description)) {
            $description = "";
        }

        $article['author'] = $author;
        $articleVersion = new ArticleVersion();
        $articleVersion['title'] = $title;
        $articleVersion['html_content'] = $htmlContent;
        $articleVersion['text_content'] = $textContent;
        $articleVersion['description'] = $description;
        $articleVersion->save();
        $article->publishedVersion()->associate($articleVersion);
        $article->save();

        return $article;
    }

    private function findArticleByTag($articleTag) : Article
    {
        $article = Article::where('tag', $articleTag)
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
            'tag' => $article['tag'],
            'author' => $article['author'],
//            'cover_url' => $version['cover_url'],
            'title' => $version['title'],
            'html_content' => $version['html_content'],
            'text_content' => $version['text_content'],
            'description' => $version['description'],
            'read_time' => $this->readTime(urldecode($version['text_content'])),
//            'status' => $article['status'],
            'created_at' => strval($article['created_at']),
        ];

        return $data;
    }

    private function readTime($content)
    {
        if(empty($content)) {
            return 0;
        }

        $count = mb_strlen($content, 'UTF-8');

        return ceil($count / 500);
    }

    private function generateTag()
    {
        for($i = 0; $i < 5; $i++) {
            $tag = strtolower(str_random(8));
            $article = Article::where('tag', $tag)->first();
            if(empty($article)) {
                return $tag;
            }
        }

        throw new BadRequestException('TAG 生成失败');
    }
}