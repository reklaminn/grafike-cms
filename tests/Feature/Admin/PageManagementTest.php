<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Language;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
    }

    public function test_admin_can_view_pages_list(): void
    {
        Page::factory()->count(3)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.pages.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_page_create_form(): void
    {
        Language::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.pages.create'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_page(): void
    {
        $language = Language::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.pages.store'), [
                'title' => 'Yeni Sayfa',
                'slug' => 'yeni-sayfa',
                'language_id' => $language->id,
                'status' => 'published',
                'module_type' => 0,
                'sort_order' => 1,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pages', [
            'title' => 'Yeni Sayfa',
            'slug' => 'yeni-sayfa',
        ]);
    }

    public function test_admin_can_edit_page(): void
    {
        $page = Page::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.pages.edit', $page));

        $response->assertStatus(200);
    }

    public function test_admin_can_update_page(): void
    {
        $page = Page::factory()->create(['title' => 'Eski Başlık']);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.pages.update', $page), [
                'title' => 'Yeni Başlık',
                'slug' => $page->slug,
                'language_id' => $page->language_id,
                'status' => 'published',
                'module_type' => 0,
                'sort_order' => 1,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'Yeni Başlık',
        ]);
    }

    public function test_admin_can_delete_page(): void
    {
        $page = Page::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.pages.destroy', $page));

        $response->assertRedirect();
        $this->assertSoftDeleted('pages', ['id' => $page->id]);
    }

    public function test_page_creation_requires_title(): void
    {
        $language = Language::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.pages.store'), [
                'title' => '', // empty
                'slug' => 'test',
                'language_id' => $language->id,
                'status' => 'published',
            ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_unauthenticated_user_cannot_manage_pages(): void
    {
        $response = $this->get(route('admin.pages.index'));

        $response->assertRedirect(route('admin.login'));
    }
}
