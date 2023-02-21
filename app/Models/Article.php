<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $hidden = array('pivot');
    protected $fillable = [
        'id',
        'external_id',
        'title',
        'url',
        'image_url',
        'news_site',
        'summary',
        'published_at',
        'updated_at',
        'featured'
    ];

    public function launches() {
        return $this->belongsToMany(
            Launch::class,
            'articles_launches',
            'article_id',
            'launch_id'
        );
    }

    public function events() {
        return $this->belongsToMany(
            Event::class,
            'articles_events',
            'article_id',
            'event_id'
        );
    }

    public static function createFromExternalArticle($external_article) {
        $article = Article::create([
            'external_id' => $external_article['id'] ?? null,
            'title' => $external_article['title'],
            'url' => $external_article['url'],
            'image_url' => $external_article['imageUrl'] ?? null,
            'news_site' => $external_article['newsSite'],
            'summary' => $external_article['summary'] ?? null,
            'published_at' => Carbon::parse($external_article['publishedAt']),
            'updated_at' => Carbon::parse($external_article['updatedAt']),
            'featured' => $external_article['featured']
        ]);

        $article->associateLaunchesAndEvents($external_article['launches'], $external_article['events']);

        return $article;
    }

    public function associateLaunchesAndEvents($launches, $events){
        $launches = $launches ?? [];
        $events = $events ?? [];

        foreach($launches as $launch){
            Launch::firstOrCreate([
                'id' => $launch["id"],
                'provider' => $launch["provider"]
            ]);

            $this->launches()->attach($launch["id"]);
        }

        foreach($events as $event){
            Event::firstOrCreate([
                'id' => $event["id"],
                'provider' => $event["provider"]
            ]);

            $this->events()->attach($event["id"]);
        }
    }
}
