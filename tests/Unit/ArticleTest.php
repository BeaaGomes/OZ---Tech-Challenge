<?php

namespace Tests\Unit;

use App\Models\Article;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_build_article_changes_id_to_external_id() {
        Article::buildArticle([
            'id' => 1234567,
            'title' => 'titulo teste',
            'url' => 'url teste',
            'imageUrl' => 'imagem teste',
            'newsSite' => 'site teste',
            'summary' => 'teste',
            'publishedAt' => '2018-10-15T22:00:00.000Z',
            'updatedAt' => '2021-05-18T13:43:21.253Z',
            'featured' => false,
            'launches' => [],
            'events' => []
        ]);

        $this->assertDatabaseHas('articles', [
            'externalId' => 1234567
        ]);
    }

    public function test_associate_launches_and_events_accepts_null_as_parameter(){
        $article = Article::first();

        $article->associateLaunchesAndEvents(null, [
            [
                'id' => 1234567,
                'provider' => 'provider teste'
            ]
        ]);

        $this->assertDatabaseHas('articles_events', [
            'article_id' => $article->id,
            'event_id' => 1234567
        ]);
    }
}
