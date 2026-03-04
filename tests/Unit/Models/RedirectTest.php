<?php

namespace Tests\Unit\Models;

use App\Models\Redirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_redirect(): void
    {
        $redirect = Redirect::factory()->create([
            'from_url' => '/eski-sayfa',
            'to_url' => '/yeni-sayfa',
            'status_code' => 301,
        ]);

        $this->assertDatabaseHas('redirects', [
            'from_url' => '/eski-sayfa',
            'to_url' => '/yeni-sayfa',
            'status_code' => 301,
        ]);
    }

    public function test_active_scope(): void
    {
        Redirect::factory()->create(['is_active' => true]);
        Redirect::factory()->create(['is_active' => true]);
        Redirect::factory()->create(['is_active' => false]);

        $this->assertCount(2, Redirect::active()->get());
    }

    public function test_is_active_cast(): void
    {
        $redirect = Redirect::factory()->create(['is_active' => 1]);

        $redirect->refresh();
        $this->assertTrue($redirect->is_active);
    }

    public function test_last_hit_at_cast(): void
    {
        $redirect = Redirect::factory()->create(['last_hit_at' => '2025-06-15 10:30:00']);

        $redirect->refresh();
        $this->assertInstanceOf(\Carbon\Carbon::class, $redirect->last_hit_at);
    }
}
