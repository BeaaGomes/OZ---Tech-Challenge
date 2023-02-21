<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    public const PAGE_SIZE = 10;

    public function index(Request $request) {
        $validator = Validator::make($request->all(), [
            'page' => 'required|integer|min:0',
        ]);

        if($validator->fails()){
            return response($validator->errors(), 400);
        }

        $skip_entries = $request->page * self::PAGE_SIZE;

        return Article::orderBy('id')
            ->skip($skip_entries)
            ->take(self::PAGE_SIZE)
            ->with('launches')
            ->with('events')
            ->get();
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'string|required',
            'url' => 'string|required',
            'imageUrl' => 'string|present',
            'newsSite' => 'string|required',
            'summary' => 'string|present',
            'publishedAt' => 'date|required',
            'updatedAt' => 'date|required',
            'featured' => 'boolean|required',
            'launches' => 'array|present',
            'events' => 'array|present',
        ]);

        if($validator->fails()){
            return response($validator->errors(), 400);
        }

        $new_article = Article::buildArticle($validator->valid());

        return $this->getArticleWithLaunchesAndEvents($new_article);
    }

    public function show(Article $article) {
        return $this->getArticleWithLaunchesAndEvents($article);
    }

    public function update(Request $request, Article $article) {
        $validator = Validator::make($request->all(), [
            'title' => 'string',
            'url' => 'string',
            'imageUrl' => 'string',
            'newsSite' => 'string',
            'summary' => 'string',
            'publishedAt' => 'date',
            'updatedAt' => 'date',
            'featured' => 'boolean',
            'launches' => 'array',
            'events' => 'array'
        ]);

        if($validator->fails()){
            return response($validator->errors(), 400);
        }

        $fields_to_update = $validator->valid();

        if($fields_to_update['publishedAt']){
            $fields_to_update['publishedAt'] = Carbon::parse($fields_to_update['publishedAt']);
        }
        if($fields_to_update['updatedAt']){
            $fields_to_update['updatedAt'] = Carbon::parse($fields_to_update['updatedAt']);
        }

        $article->update($fields_to_update);

        $this->updateLaunchesAndEvents($article, $request->launches, $request->events);

        return $this->getArticleWithLaunchesAndEvents($article);
    }

    private function updateLaunchesAndEvents($article, $launches, $events){
        if(isset($launches) && count($launches) > 0){
            DB::table('articles_launches')->where('article_id', $article->id)->delete();
        }

        if(isset($events) && count($events) > 0){
            DB::table('articles_events')->where('article_id', $article->id)->delete();
        }

        $article->associateLaunchesAndEvents($launches, $events);
    }

    private function getArticleWithLaunchesAndEvents($article){
        return Article::where('id', $article->id)
            ->with('launches')
            ->with('events')
            ->first();
    }

    public function destroy(Article $article) {
        $article->delete();
    }
}
