<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Article;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
    }

    public function test_admin_can_view_articles_for_page(): void
    {
        $page = Page::factory()->create();
        Article::factory()->count(3)->create(['page_id' => $page->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.articles.index', ['page_id' => $page->id]));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_article(): void
    {
        $page = Page::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.articles.store'), [
                'title' => 'Test Yazısı',
                'body' => '<p>İçerik</p>',
                'page_id' => $page->id,
                'language_id' => $page->language_id,
                'status' => 'published',
                'slug' => 'test-yazisi',
                'sort_order' => 1,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('articles', ['title' => 'Test Yazısı']);
    }

    public function test_admin_can_update_article(): void
    {
        $article = Article::factory()->create(['title' => 'Eski']);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.articles.update', $article), [
                'title' => 'Güncel Başlık',
                'body' => $article->body,
                'page_id' => $article->page_id,
                'language_id' => $article->language_id,
                'status' => 'published',
                'slug' => $article->slug,
                'sort_order' => 1,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Güncel Başlık',
        ]);
    }

    public function test_admin_can_delete_article(): void
    {
        $article = Article::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.articles.destroy', $article));

        $response->assertRedirect();
        $this->assertSoftDeleted('articles', ['id' => $article->id]);
    }
}
