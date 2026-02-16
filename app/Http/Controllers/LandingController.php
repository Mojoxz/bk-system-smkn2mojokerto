<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $news = News::published()
            ->latest('published_at')
            ->take(6)
            ->get();

        return view('landing.index', compact('news'));
    }

    public function showNews($slug)
    {
        $news = News::where('slug', $slug)->published()->firstOrFail();
        $news->increment('views');

        $relatedNews = News::published()
            ->where('id', '!=', $news->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('landing.news-show', compact('news', 'relatedNews'));
    }
}
