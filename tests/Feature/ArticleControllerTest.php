<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Event;
use App\Models\Launch;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_get_articles_endpoint_returns_articles() {
        $response = $this->json('GET', '/articles', ['page' => 0]);
        $expected_article = Article::orderBy("id")->first()->toArray();

        $response->assertStatus(200)
            ->assertJson([$expected_article])
            ->assertJsonCount(10);
    }

    public function test_get_articles_endpoint_changes_results_after_switch_pages() {
        $page_2_results = $this->json('GET', '/articles', ['page' => 2])->json();
        $page_3_results = $this->json('GET', '/articles', ['page' => 3])->json();

        $this->assertNotEquals($page_2_results, $page_3_results);
    }

    public function test_get_articles_endpoint_returns_400_when_invalid_page_is_send() {
        foreach ($this->provideInvalidPages() as $invalidPage){
            $response = $this->json('GET', '/articles', $invalidPage);

            $response->assertStatus(400);
        }
    }

    private function provideInvalidPages(){
        return [
            ['page' => -7],
            ['page' => 3.4],
            ['page' => 'a'],
            [],
        ];
    }

    public function test_post_articles_endpoint_creates_article(){
        $article_data = $this->buildArticleData();

        $new_article = $this->post('/articles', $article_data)->json();

        $this->assertDatabaseHas('articles', [
            'id' => $new_article["id"]
        ]);
    }

    public function test_post_articles_endpoint_creates_lauches(){
        $random_id = md5(rand());

        $article_data = $this->buildArticleData();
        $article_data["launches"] = [
            [
                'id' => $random_id,
                'provider' => 'provider teste'
            ]
        ];

        $new_article = $this->post('/articles', $article_data);

        $this->assertDatabaseHas('launches', [
            'id' => $random_id,
            'provider' => 'provider teste'
        ]);

        $this->assertDatabaseHas('articles_launches', [
            'launch_id' => $random_id,
            'article_id' => $new_article["id"]
        ]);
    }

    public function test_post_articles_endpoint_creates_events(){
        $random_id = md5(rand());

        $article_data = $this->buildArticleData();
        $article_data["events"] = [
            [
                'id' => $random_id,
                'provider' => 'provider teste'
            ]
        ];

        $new_article = $this->post('/articles', $article_data);

        $this->assertDatabaseHas('events', [
            'id' => $random_id,
            'provider' => 'provider teste'
        ]);

        $this->assertDatabaseHas('articles_events', [
            'event_id' => $random_id,
            'article_id' => $new_article["id"]
        ]);
    }

    public function test_post_articles_endpoint_associate_launches_that_already_exists() {
        $launch = Launch::first()->toArray();

        $article_data = $this->buildArticleData();
        $article_data["launches"] = [$launch];

        $new_article = $this->post('/articles', $article_data);

        $this->assertDatabaseHas('articles_launches', [
            'launch_id' => $launch["id"],
            'article_id' => $new_article["id"]
        ]);
    }

    public function test_post_articles_endpoint_associate_events_that_already_exists() {
        $event = Event::first()->toArray();

        $article_data = $this->buildArticleData();
        $article_data["events"] = [$event];

        $new_article = $this->post('/articles', $article_data);

        $this->assertDatabaseHas('articles_events', [
            'event_id' => $event["id"],
            'article_id' => $new_article["id"]
        ]);
    }

    private function buildArticleData($title = 'titulo teste'){
        return [
            'title' => $title,
            'url' => 'url teste',
            'imageUrl' => 'imagem teste',
            'newsSite' => 'site teste',
            'summary' => 'teste',
            'publishedAt' => '2018-10-15T22:00:00.000Z',
            'updatedAt' => '2021-05-18T13:43:21.253Z',
            'featured' => false,
            'launches' => [],
            'events' => []
        ];
    }
}
