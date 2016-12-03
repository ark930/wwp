<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $coverImage = $request->input('cover_image');
        $title = $request->input('title');
        $content = $request->input('content');

        $article = new Article();
        $article['cover_image'] = $coverImage;
        $article['title'] = $title;
        $article['content'] = $content;

//        $user = User::find(1);
        $user = Auth::user();
        $user->articles()->save($article);

        return response()->json($article);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws BadRequestException
     */
    public function show($id)
    {
        $article = Article::find($id);
        if(empty($article)) {
            throw new BadRequestException('该文章不存在');
        }

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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws BadRequestException
     */
    public function update(Request $request, $id)
    {
        $article = Article::find($id);
        if(empty($article)) {
            throw new BadRequestException('该文章不存在');
        }

        $coverImage = $request->input('cover_image');
        $title = $request->input('title');
        $content = $request->input('content');

        $article['cover_image'] = $coverImage;
        $article['title'] = $title;
        $article['content'] = $content;
        $article['status'] = Article::STATUS_DRAFT;
        $article->save();

        return response()->json($article);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $article = Article::find($id);
        if(!empty($article)) {
            $article->delete();
        }

        return response('', 204);
    }
}
