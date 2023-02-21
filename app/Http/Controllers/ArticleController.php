<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public const PAGE_SIZE = 10;

    public function index(Request $request) {
        $skip_entries = $request->page * self::PAGE_SIZE;
        return Article::orderBy('id')
            ->skip($skip_entries)
            ->take(self::PAGE_SIZE)
            ->with('launches')
            ->with('events')
            ->get();
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

        $new_article = Article::createFromExternalArticle($external_article);

        return $this->getArticleWithLaunchesAndEvents($new_article);
    }

    public function show(Article $article) {
        return $this->getArticleWithLaunchesAndEvents($article);
    }

    public function update(Request $request, Article $article) {
        $fields_to_update = $request->only([
            'title',
            'url',
            'imageUrl',
            'newsSite',
            'summary',
            'publishedAt',
            'updatedAt',
            'featured'
        ]);

        if($fields_to_update['publishedAt']){
            $fields_to_update['publishedAt'] = Carbon::parse($fields_to_update['publishedAt']);
        }

        if($fields_to_update['updatedAt']){
            $fields_to_update['updatedAt'] = Carbon::parse($fields_to_update['updatedAt']);
        }

        $article->update($fields_to_update);

        if(isset($request->launches) && count($request->launches) > 0){
            DB::table('articles_launches')->where('article_id', $article->id)->delete();
        }

        if(isset($request->events) && count($request->events) > 0){
            DB::table('articles_events')->where('article_id', $article->id)->delete();
        }

        $article->associateLaunchesAndEvents($request->launches, $request->events);

        return $this->getArticleWithLaunchesAndEvents($article);
    }

    private function getArticleWithLaunchesAndEvents($article){
        return Article::where('id', $article->id)
            ->with('launches')
            ->with('events')
            ->first();
    }

    public function destroy(Article $article) {
        $article->delete();

        return "Article deleted!";
    }
}
