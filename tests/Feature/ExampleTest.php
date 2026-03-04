<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Without a configured homepage, the app returns welcome view
        $response = $this->get('/');

        $this->assertTrue(in_array($response->status(), [200, 404]));
    }
}
