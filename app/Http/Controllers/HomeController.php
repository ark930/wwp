<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    public function tp()
    {
        return view('index');
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
}
