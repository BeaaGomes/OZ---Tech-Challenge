<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function index(Request $request) {
        $skip_entries = $request->page * 10;
        return Article::orderBy("id")
            ->skip($skip_entries)
            ->take(10)
            ->get();
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

    public function store(Request $request) {
        $external_article = $request->only([
            'title',
            'url',
            'imageUrl',
            'newsSite',
            'summary',
            'publishedAt',
            'updatedAt',
            'featured',
            'launches',
            'events'
        ]);

        Article::createFromExternalArticle($external_article);

        return "Article created!";
    }

    public function show(Article $article) {
        return $article;
    }

    public function update(Request $request, Article $article) {
        $article->update($request->only([
            'title',
            'url',
            'imageUrl',
            'newsSite',
            'summary',
            'publishedAt',
            'updatedAt',
            'featured'
        ]));

        if(isset($request->launches)){
            DB::table('articles_launches')->where('article_id', $article->id)->delete();
        }

        if(isset($request->events)){
            DB::table('articles_events')->where('article_id', $article->id)->delete();
        }

        $article->associateLaunchesAndEvents($request->launches, $request->events);

        return "Article updated!";
    }

    public function destroy(Article $article) {
        $article->delete();

        return "Article deleted!";
    }
}
