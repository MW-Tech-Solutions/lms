<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_logout_via_get_and_post_without_csrf_succeeds(): void
    {
        $responseGet = $this->get('/logout');
        $responseGet->assertRedirect('/');

        $responsePost = $this->post('/logout');
        $responsePost->assertRedirect('/');
    }
}
