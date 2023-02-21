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
        'externalId',
        'title',
        'url',
        'imageUrl',
        'newsSite',
        'summary',
        'publishedAt',
        'updatedAt',
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
        if($external_article['publishedAt']){
            $external_article['publishedAt'] = Carbon::parse($external_article['publishedAt']);
        }

        if($external_article['updatedAt']){
            $external_article['updatedAt'] = Carbon::parse($external_article['updatedAt']);
        }

        $external_article['externalId'] = $external_article['id'] ?? null;

        $article = Article::create($external_article);

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
