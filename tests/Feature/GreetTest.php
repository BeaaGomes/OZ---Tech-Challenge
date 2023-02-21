<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GreetTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_greet_returns_message()
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertJson(["message" => "Back-end Challenge 2021 🏅 - Space Flight News"]);
    }
}
