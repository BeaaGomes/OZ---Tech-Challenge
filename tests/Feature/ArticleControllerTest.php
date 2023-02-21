<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
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

}
