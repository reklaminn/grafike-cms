<?php

namespace Tests\Feature\Frontend;

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_login_page_is_accessible(): void
    {
        $response = $this->get(route('member.login'));

        $response->assertStatus(200);
    }

    public function test_member_register_page_is_accessible(): void
    {
        $response = $this->get(route('member.register'));

        $response->assertStatus(200);
    }

    public function test_member_can_register(): void
    {
        $response = $this->post(route('member.register.submit'), [
            'name' => 'Test Üye',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('members', [
            'name' => 'Test Üye',
            'email' => 'test@example.com',
        ]);
    }

    public function test_member_can_login(): void
    {
        $member = Member::factory()->create([
            'email' => 'member@example.com',
            'password' => 'secret123',
            'is_active' => true,
        ]);

        $response = $this->post(route('member.login.submit'), [
            'email' => 'member@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($member, 'member');
    }

    public function test_inactive_member_cannot_login(): void
    {
        Member::factory()->create([
            'email' => 'inactive@example.com',
            'password' => 'secret123',
            'is_active' => false,
        ]);

        $response = $this->post(route('member.login.submit'), [
            'email' => 'inactive@example.com',
            'password' => 'secret123',
        ]);

        // Controller uses session flash 'error', not validation errors
        $response->assertSessionHas('error');
        $this->assertGuest('member');
    }

    public function test_member_can_logout(): void
    {
        $member = Member::factory()->create(['is_active' => true]);
        $this->actingAs($member, 'member');

        $response = $this->post(route('member.logout'));

        $response->assertRedirect();
        $this->assertGuest('member');
    }

    public function test_member_can_view_profile(): void
    {
        $member = Member::factory()->create(['is_active' => true]);

        $response = $this->actingAs($member, 'member')
            ->get(route('member.profile'));

        $response->assertStatus(200);
    }

    public function test_registration_requires_valid_email(): void
    {
        $response = $this->post(route('member.register.submit'), [
            'name' => 'Test',
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->post(route('member.register.submit'), [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
