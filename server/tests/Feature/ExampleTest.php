<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestBase;

class ExampleTest extends TestBase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/api/x');

        $response->assertStatus(200);
        $response->assertSee('Webshop API');
    }
}
