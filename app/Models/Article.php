<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

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

    public function createFromExternalArticle($external_article) {
        $article = Article::create([
            'external_id' => $external_article->id,
            'title' => $external_article->title,
            'url' => $external_article->url,
            'imageUrl' => $external_article->imageUrl,
            'newsSite' => $external_article->newsSite,
            'summary' => $external_article->summary,
            'publishedAt' => $external_article->publishedAt,
            'updatedAt' => $external_article->updatedAt,
            'featured' => $external_article->featured
        ]);

        foreach($external_article->launches as $launch){
            $launch = Launch::firstOrCreate([
                'id' => $external_article->id,
                'provider' => $external_article->provider
            ]);

            $article->launches()->attach($launch->id);
        }

        foreach($external_article->events as $event){
            $event = Event::firstOrCreate([
                'id' => $external_article->id,
                'provider' => $external_article->provider
            ]);

            $article->events()->attach($event->id);
        }

    }
}
