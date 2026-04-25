<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Article;
use App\Models\Language;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected Language $language;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin    = Admin::factory()->create();
        $this->language = Language::factory()->create();
    }

    public function test_admin_can_view_articles_index(): void
    {
        Article::factory()->count(3)->create(['language_id' => $this->language->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.articles.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_articles_for_page(): void
    {
        $page = Page::factory()->create(['language_id' => $this->language->id]);
        Article::factory()->count(3)->create(['page_id' => $page->id, 'language_id' => $this->language->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.articles.index', ['page_id' => $page->id]));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_create_form(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.articles.create'));

        $response->assertStatus(200);
        $response->assertSee('Yeni Yazı');
    }

    public function test_admin_can_view_edit_form(): void
    {
        $article = Article::factory()->create(['language_id' => $this->language->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.articles.edit', $article));

        $response->assertStatus(200);
        $response->assertSee($article->title);
    }

    public function test_admin_can_create_article(): void
    {
        $page = Page::factory()->create(['language_id' => $this->language->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.articles.store'), [
                'title'       => 'Test Yazısı',
                'body'        => '<p>İçerik</p>',
                'page_id'     => $page->id,
                'language_id' => $this->language->id,
                'status'      => 'published',
                'slug'        => 'test-yazisi',
                'sort_order'  => 1,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('articles', ['title' => 'Test Yazısı']);
    }

    public function test_admin_can_update_article(): void
    {
        $article = Article::factory()->create(['language_id' => $this->language->id, 'title' => 'Eski']);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.articles.update', $article), [
                'title'       => 'Güncel Başlık',
                'body'        => $article->body,
                'page_id'     => $article->page_id,
                'language_id' => $this->language->id,
                'status'      => 'published',
                'slug'        => $article->slug,
                'sort_order'  => 1,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('articles', [
            'id'    => $article->id,
            'title' => 'Güncel Başlık',
        ]);
    }

    public function test_admin_can_update_listing_and_detail_variant(): void
    {
        $article = Article::factory()->create(['language_id' => $this->language->id]);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.articles.update', $article), [
                'title'           => $article->title,
                'language_id'     => $this->language->id,
                'status'          => $article->status,
                'listing_variant' => 'card-horizontal',
                'detail_variant'  => 'full-width',
            ]);

        $article->refresh();
        $this->assertEquals('card-horizontal', $article->listing_variant);
        $this->assertEquals('full-width', $article->detail_variant);
    }

    public function test_admin_can_delete_article(): void
    {
        $article = Article::factory()->create(['language_id' => $this->language->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.articles.destroy', $article));

        $response->assertRedirect();
        $this->assertSoftDeleted('articles', ['id' => $article->id]);
    }

    public function test_featured_filter_returns_only_featured(): void
    {
        Article::factory()->create(['language_id' => $this->language->id, 'is_featured' => true,  'title' => 'Öne Çıkan Yazı']);
        Article::factory()->create(['language_id' => $this->language->id, 'is_featured' => false, 'title' => 'Standart Yazı']);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.articles.index', ['is_featured' => '1']));

        $response->assertStatus(200);
        $response->assertSee('Öne Çıkan Yazı');
        $response->assertDontSee('Standart Yazı');
    }

    public function test_archived_status_filter(): void
    {
        Article::factory()->create(['language_id' => $this->language->id, 'status' => 'archived',  'title' => 'Arşiv Yazı']);
        Article::factory()->create(['language_id' => $this->language->id, 'status' => 'published', 'title' => 'Yayında Yazı']);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.articles.index', ['status' => 'archived']));

        $response->assertStatus(200);
        $response->assertSee('Arşiv Yazı');
        $response->assertDontSee('Yayında Yazı');
    }

    public function test_article_requires_title(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.articles.store'), [
                'title'       => '',
                'language_id' => $this->language->id,
                'status'      => 'draft',
            ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_unauthenticated_user_cannot_manage_articles(): void
    {
        $response = $this->get(route('admin.articles.index'));
        $response->assertRedirect(route('admin.login'));
    }
}
