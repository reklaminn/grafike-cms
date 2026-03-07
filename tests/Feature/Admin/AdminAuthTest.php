<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $admin = Admin::factory()->create([
            'username' => 'admin',
            'email' => 'admin@grafike.com',
            'password' => 'password123',
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'username' => 'admin',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_admin_can_login_with_email_as_username(): void
    {
        $admin = Admin::factory()->create([
            'username' => 'admin',
            'email' => 'admin@grafike.com',
            'password' => 'password123',
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'username' => 'admin@grafike.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_admin_cannot_login_with_invalid_credentials(): void
    {
        Admin::factory()->create(['username' => 'admin']);

        $response = $this->post(route('admin.login.submit'), [
            'username' => 'admin',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest('admin');
    }

    public function test_admin_can_login_with_legacy_md5_password(): void
    {
        $admin = Admin::factory()->create([
            'username' => 'legacy',
            'email' => 'legacy@grafike.com',
            'password' => 'not-used',
            'legacy_password' => md5('legacy123'),
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'username' => 'legacy',
            'password' => 'legacy123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        // Verify legacy password was cleared and bcrypt set
        $admin->refresh();
        $this->assertNull($admin->legacy_password);
    }

    public function test_unauthenticated_admin_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_logout(): void
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->post(route('admin.logout'));

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest('admin');
    }
}
