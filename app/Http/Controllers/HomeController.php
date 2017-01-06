<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function tp(Request $request)
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
            'title' => '',
            'author' => '',
            'content' => '',
            'updated_at' => '',
            'show_edit_button' => 'true',
            'is_read_only' => false,
        ]);
    }

    public function check()
    {
        return [
            "short_name" => "",
            "author_name" => "",
            "author_url" => "",
            "can_edit" => false
        ];
    }

    public function save(Request $request)
    {
        $data = $request->file('Data');
        $author = $request->input('author');
        $authorUrl = $request->input('author_url');
        $pageId = $request->input('page_id');
        $title = $request->input('title');
        $data = file_get_contents($data);

        $data = json_decode($data, true);

        dd($data, $author, $authorUrl, $pageId, $title);
    }

    public function notify(Request $request)
    {
        Log::info($request->method() . ' ' . $request->fullUrl());
        Log::info(\GuzzleHttp\json_encode($request->all()));

        return response($request->all(), 401);
    }
}
