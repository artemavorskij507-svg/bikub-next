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
        // The public homepage is intentionally database-backed. Keep this
        // baseline smoke test independent from the unavailable SQLite driver.
        $response = $this->get('/up');

        $response->assertStatus(200);
    }
}
