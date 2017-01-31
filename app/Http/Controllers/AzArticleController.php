<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Models\Article;
use App\Models\ArticleVersion;
use App\Models\Device;
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
            'reply_slug' => 'nullable',
        ]);

        $title = $request->input('title');
        $htmlContent = $request->input('html_content');
        $textContent = $request->input('text_content');
        $author = $request->input('author');
        $description = $request->input('description');
        $replySlug = $request->input('reply_slug');

        if(empty($author)) {
            $author = "";
        }
        if(empty($description)) {
            $description = "";
        }


        // 这篇文章是否是回应其他文章
        if(!empty($replySlug)) {
            $replayArticle = Article::where('slug', $replySlug)->first();
            if(empty($replayArticle)) {
                throw new BadRequestException('你要回应的文章不存在');
            }
        }

        // 如果设备已存在，获取设备ID
        if($request->session()->has('device_id')) {
            $device = Device::find($request->session()->get('device_id'));
        }

        // 如果不存在，新建设备，并存入session
        if(empty($device)) {
            $device = Device::createDevice();

            $deviceId = $device['id'];
            $request->session()->put('device_id', $deviceId);
        }

        // 如果用户已登录，检测设备是否已与用户绑定。
        if(Auth::check()) {
            $user = Auth::user();
            $userDevice = $user->devices->where('user_id', $user['id'])->first();
            if(empty($userDevice)) {
                $device['user_id'] = $user['id'];
                $device->save();
            }
        }

        $article = new Article();
        $article['slug'] = $this->generateSlug();
        $article['status'] = Article::STATUS_PUBLISHED;
        $article['author'] = $author;
        if(!empty($replayArticle)) {
            $article['reply_article_id'] =  $replayArticle['id'];
        }
        $device->articles()->save($article);
        $articleVersion = new ArticleVersion();
        $articleVersion['title'] = $title;
        $articleVersion['html_content'] = $htmlContent;
        $articleVersion['text_content'] = $textContent;
        $articleVersion['description'] = $description;
        $articleVersion->save();
        $article->publishedVersion()->associate($articleVersion);
        $article->save();

        $data = $this->filterArticleData($article);
        $data['show_url'] = $this->makeShowUrl($data['slug']);

        return response()->json($data);
    }

    public function read(Request $request, $tag)
    {
        $article = $this->findArticleBySlug($tag);
        $data = $this->filterArticleData($article);

        $data['mode'] = self::MODE_AUDIENCE_READ;

        if($request->session()->has('device_id')) {
            $device = Device::find($request->session()->get('device_id'));
            if(!empty($device)) {
                $articleDevice = $article->device;
                if($device['id'] === $articleDevice['id']) {
                    $data['mode'] = self::MODE_AUTHOR_READ;
                }
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

        $article = $this->findArticleBySlug($tag);

        if($request->session()->has('device_id')) {
            $device = Device::find($request->session()->get('device_id'));
            if (!empty($device)) {
                $articleDevice = $article->device;
                if($device['id'] !== $articleDevice['id']) {
                    throw new AuthenticationException();
                }
            } else {
                throw new AuthenticationException();
            }
        } else {
            throw new AuthenticationException();
        }

        $article = $this->updateArticle($request, $article);
        $article->save();

        $data = $this->filterArticleData($article);
        $data['mode'] = self::MODE_AUTHOR_READ;
        $data['show_url'] = $this->makeShowUrl($data['slug']);

        return response()->json($data);
    }

    public function myArticles(Request $request)
    {
        $articles = [];
        if(Auth::check()) {
            $user = Auth::user();
            $devices = $user->devices;
            $articles = collect();
            foreach ($devices as $device) {
                $a = $device->articles;
                $articles = $articles->merge($a);
            }
        } else if($request->session()->has('device_id')) {
            $device = Device::find($request->session()->get('device_id'));
            if(!empty($device)) {
                $articles = $device->articles;
            }
        }

        $data = [];
        foreach ($articles as $article) {
            $data[] = $this->filterArticleData($article);
        }

//        return $data;
        return view('my_articles', [
            'articles' => $data
        ]);
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
        $articleVersion = $article->publishedVersion;
        $articleVersion['title'] = $title;
        $articleVersion['html_content'] = $htmlContent;
        $articleVersion['text_content'] = $textContent;
        $articleVersion['description'] = $description;
        $articleVersion->save();
//        $article->publishedVersion()->associate($articleVersion);
//        $article->save();

        return $article;
    }

    private function findArticleBySlug($articleTag) : Article
    {
        $article = Article::where('slug', $articleTag)
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
            'slug' => $article['slug'],
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

    private function generateSlug()
    {
        for($i = 0; $i < 5; $i++) {
            $tag = strtolower(str_random(8));
            $article = Article::where('slug', $tag)->first();
            if(empty($article)) {
                return $tag;
            }
        }

        throw new BadRequestException('Slug 生成失败');
    }
}