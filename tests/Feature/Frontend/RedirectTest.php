<?php

namespace Tests\Feature\Frontend;

use App\Models\Redirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_301_redirect_works(): void
    {
        Redirect::factory()->create([
            'from_url' => 'eski-url',
            'to_url' => '/yeni-url',
            'status_code' => 301,
            'is_active' => true,
        ]);

        $response = $this->get('/eski-url');

        $response->assertRedirect('/yeni-url');
        $response->assertStatus(301);
    }

    public function test_302_redirect_works(): void
    {
        Redirect::factory()->create([
            'from_url' => 'gecici',
            'to_url' => '/hedef',
            'status_code' => 302,
            'is_active' => true,
        ]);

        $response = $this->get('/gecici');

        $response->assertRedirect('/hedef');
        $response->assertStatus(302);
    }

    public function test_redirect_increments_hit_count(): void
    {
        $redirect = Redirect::factory()->create([
            'from_url' => 'tracked',
            'to_url' => '/target',
            'hit_count' => 0,
        ]);

        $this->get('/tracked');

        $redirect->refresh();
        $this->assertEquals(1, $redirect->hit_count);
    }
}
