<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function articles(Request $request)
    {
        $this->isAdmin($request);

        $articles = DB::table(DB::raw('articles as a'))
            ->join(DB::raw('article_versions as v'), 'a.publish_version_id', '=', 'v.id')
            ->join(DB::raw('devices as d'), 'a.device_id', '=', 'd.id')
            ->select('v.title', 'a.tag', 'a.created_at, d.tel as device')
            ->orderBy('a.created_at', 'DESC')
            ->paginate(10);

        foreach ($articles as &$item) {
            $item->title = urldecode($item->title);
            $item->link = route('article_read', $item->tag);
            unset($item->tag);
        }

        return $articles;
    }

    public function devices(Request $request)
    {
        $this->isAdmin($request);

        $articles = DB::table(DB::raw('devices as d'))
            ->select(DB::raw('d.tel as device, created_at'))
            ->orderBy('a.created_at', 'DESC')
            ->paginate(10);

        foreach ($articles as &$item) {
            $item->title = urldecode($item->title);
            $item->link = route('article_read', $item->tag);
            unset($item->tag);
        }

        return $articles;
    }

    protected function isAdmin(Request $request)
    {
        $isAdmin = false;
        if($request->session()->has('device_id')) {
            $device = Device::find($request->session()->get('device_id'));
            if(!empty($device)) {
                if($device['is_admin'] === 1) {
                    $isAdmin = true;
                }
            }
        }

        if($isAdmin === false) {
            // 挡掉没权限的人
            if(!Auth::check()) {
                throw new AuthenticationException();
            }
            $user = Auth::user();
            if(!$user->isAdmin())
            {
                throw new AuthenticationException();
            }
        }
    }
}
