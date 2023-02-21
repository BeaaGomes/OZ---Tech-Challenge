<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Event;
use App\Models\Launch;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_get_articles_endpoint_returns_articles() {
        $response = $this->json('GET', '/articles', ['page' => 0]);
        $expected_article = Article::orderBy('id')->first()->toArray();

        $response->assertStatus(200)
            ->assertJson([$expected_article])
            ->assertJsonCount(10);
    }

    public function test_get_articles_endpoint_changes_results_after_switch_pages() {
        $page_2_results = $this->json('GET', '/articles', ['page' => 2])->json();
        $page_3_results = $this->json('GET', '/articles', ['page' => 3])->json();

        $this->assertNotEquals($page_2_results, $page_3_results);
    }

    public function test_get_articles_endpoint_returns_400_when_invalid_params() {
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

    public function test_get_article_by_id_endpoint_returns_article() {
        $test_article_id = 4;
        $expected_article = Article::where('id', $test_article_id)->first()->toArray();

        $response = $this->get("/articles/$test_article_id");

        $response->assertStatus(200)
            ->assertJson($expected_article);
    }

    private function buildArticleData(){
        return [
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
        ];
    }

    public function test_post_articles_endpoint_creates_article(){
        $article_data = $this->buildArticleData();

        $new_article = $this->post('/articles', $article_data)->json();

        $this->assertDatabaseHas('articles', [
            'id' => $new_article['id']
        ]);
    }

    public function test_post_articles_endpoint_creates_launches(){
        $random_id = md5(rand());

        $article_data = $this->buildArticleData();
        $article_data['launches'] = [
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
            'article_id' => $new_article['id']
        ]);
    }

    public function test_post_articles_endpoint_creates_events(){
        $random_id = md5(rand());

        $article_data = $this->buildArticleData();
        $article_data['events'] = [
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
            'article_id' => $new_article['id']
        ]);
    }

    public function test_post_articles_endpoint_associate_launches_that_already_exists() {
        $launch = Launch::first()->toArray();

        $article_data = $this->buildArticleData();
        $article_data['launches'] = [$launch];

        $new_article = $this->post('/articles', $article_data);

        $this->assertDatabaseHas('articles_launches', [
            'launch_id' => $launch['id'],
            'article_id' => $new_article['id']
        ]);
    }

    public function test_post_articles_endpoint_associate_events_that_already_exists() {
        $event = Event::first()->toArray();

        $article_data = $this->buildArticleData();
        $article_data['events'] = [$event];

        $new_article = $this->post('/articles', $article_data);

        $this->assertDatabaseHas('articles_events', [
            'event_id' => $event['id'],
            'article_id' => $new_article['id']
        ]);
    }

    public function test_post_articles_endpoint_returns_400_when_invalid_params() {
        $invalid_article_data = $this->buildArticleData();
        $invalid_article_data['title'] = null;

        $response = $this->post('/articles', $invalid_article_data);

        $response->assertStatus(400);
    }

    public function test_update_articles_endpoint_updates_article() {
        $article_data = $this->buildArticleData();
        $new_article = $this->put('/articles/8', $article_data);

        $article_data['updatedAt'] = Carbon::parse($article_data['updatedAt'])->toDateTimeString();
        $article_data['publishedAt'] = Carbon::parse($article_data['publishedAt'])->toDateTimeString();

        $new_article->assertJson($article_data);
    }

    public function test_update_articles_endpoint_updates_launch() {
        $random_id = md5(rand());

        $article_data = $this->buildArticleData();
        $article_data['launches'] = [
            [
                'id' => $random_id,
                'provider' => 'provider teste'
            ]
        ];

        $new_article = $this->put('/articles/8', $article_data);

        $this->assertDatabaseHas('articles_launches', [
            'launch_id' => $random_id,
            'article_id' => $new_article['id']
        ]);

        $this->assertDatabaseHas('launches', [
            'id' => $random_id
        ]);
    }

    public function test_update_articles_endpoint_updates_event() {
        $random_id = md5(rand());

        $article_data = $this->buildArticleData();
        $article_data['events'] = [
            [
                'id' => $random_id,
                'provider' => 'provider teste'
            ]
        ];

        $new_article = $this->put('/articles/8', $article_data);

        $this->assertDatabaseHas('articles_events', [
            'event_id' => $random_id,
            'article_id' => $new_article['id']
        ]);

        $this->assertDatabaseHas('events', [
            'id' => $random_id
        ]);
    }

    public function test_update_articles_endpoint_does_not_delete_relationship_with_launch() {
        $article_launch = DB::table('articles_launches')->first();

        $article_launch = [
            'article_id' => $article_launch->article_id,
            'launch_id' => $article_launch->launch_id
        ];

        $article_data = $this->buildArticleData();

        $this->put('/articles/' . $article_launch['article_id'], $article_data);

        $this->assertDatabaseHas('articles_launches', $article_launch);
    }

    public function test_update_articles_endpoint_does_not_delete_relationship_with_event() {
        $article_event = DB::table('articles_events')->first();

        $article_event = [
            'article_id' => $article_event->article_id,
            'event_id' => $article_event->event_id
        ];

        $article_data = $this->buildArticleData();

        $this->put('/articles/' . $article_event['article_id'], $article_data);

        $this->assertDatabaseHas('articles_events', $article_event);
    }

    public function test_update_articles_endpoint_returns_400_when_invalid_params() {
        $article = Article::first();
        $invalid_article_data = $this->buildArticleData();
        $invalid_article_data['publishedAt'] = 'invalidDate';

        $response = $this->put('/articles/' . $article->id, $invalid_article_data);

        $response->assertStatus(400);
    }

    public function test_delete_articles_endpoint_deletes_article() {
        $article = Article::first();
        $this->delete('/articles/' . $article->id);

        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }
}
