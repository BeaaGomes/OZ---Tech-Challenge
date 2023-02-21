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

    /**
    * Creates an article and associate it with its launches and events,
    * if necessary it also creates the launches and events
    *
    * the raw_article should be in the following format:
    * [
    *   'id' => 0,
    *   'featured' => false,
    *   'title' => 'string',
    *   'url' => 'string',
    *   'imageUrl' => 'string',
    *   'newsSite' => 'string',
    *   'summary' => 'string',
    *   'publishedAt' => 'string',
    *   'launches' => [
    *     [
    *       'id' => 'string',
    *       'provider' => 'string'
    *     ]
    *   ],
    *   'events' => [
    *     [
    *       'id' => 'string',
    *       'provider' => 'string'
    *     ]
    *   ]
    * ]
    */
    public static function buildArticle($raw_article) {
        if($raw_article['publishedAt']){
            $raw_article['publishedAt'] = Carbon::parse($raw_article['publishedAt']);
        }

        if($raw_article['updatedAt']){
            $raw_article['updatedAt'] = Carbon::parse($raw_article['updatedAt']);
        }

        $raw_article['externalId'] = $raw_article['id'] ?? null;

        $article = Article::create($raw_article);

        $article->associateLaunchesAndEvents($raw_article['launches'], $raw_article['events']);

        return $article;
    }

    public function associateLaunchesAndEvents($launches, $events){
        $launches = $launches ?? [];
        $events = $events ?? [];

        foreach($launches as $launch){
            Launch::firstOrCreate([
                'id' => $launch['id'],
                'provider' => $launch['provider']
            ]);

            $this->launches()->attach($launch['id']);
        }

        foreach($events as $event){
            Event::firstOrCreate([
                'id' => $event['id'],
                'provider' => $event['provider']
            ]);

            $this->events()->attach($event['id']);
        }
    }
}
