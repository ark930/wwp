<?php

namespace App\Http\Controllers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // 挡掉没权限的人
        if(!Auth::check()) {
            throw new AuthenticationException();
        }
        $user = Auth::user();
        if(!$user->isAdmin())
        {
            throw new AuthenticationException();
        }

        $articles = DB::table(DB::raw('articles as a'))
            ->join(DB::raw('article_versions as v'), 'a.publish_version_id', '=', 'v.id')
            ->select('v.title', 'a.tag', 'a.created_at')
            ->orderBy('a.created_at', 'DESC')
            ->paginate(10);

        foreach ($articles as &$item) {
            $item->title = urldecode($item->title);
            $item->link = route('article_read', $item->tag);
            unset($item->tag);
        }

        return $articles;
    }
}
