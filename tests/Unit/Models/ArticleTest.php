<?php

namespace Tests\Unit\Models;

use App\Models\Admin;
use App\Models\Article;
use App\Models\Page;
use App\Models\SeoEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_article(): void
    {
        $article = Article::factory()->create(['title' => 'Test Yazısı']);

        $this->assertDatabaseHas('articles', ['title' => 'Test Yazısı']);
    }

    public function test_article_belongs_to_page(): void
    {
        $page = Page::factory()->create(['title' => 'Blog']);
        $article = Article::factory()->create(['page_id' => $page->id]);

        $this->assertEquals('Blog', $article->page->title);
    }

    public function test_article_has_author(): void
    {
        $admin = Admin::factory()->create(['name' => 'Editör']);
        $article = Article::factory()->create(['author_id' => $admin->id]);

        $this->assertEquals('Editör', $article->author->name);
    }

    public function test_published_scope(): void
    {
        Article::factory()->create(['status' => 'published']);
        Article::factory()->create(['status' => 'draft']);

        $this->assertCount(1, Article::published()->get());
    }

    public function test_featured_scope(): void
    {
        Article::factory()->create(['is_featured' => true]);
        Article::factory()->create(['is_featured' => false]);
        Article::factory()->create(['is_featured' => true]);

        $this->assertCount(2, Article::featured()->get());
    }

    public function test_article_has_seo_relationship(): void
    {
        $article = Article::factory()->create();
        SeoEntry::factory()->create([
            'seoable_id' => $article->id,
            'seoable_type' => Article::class,
        ]);

        $article->refresh();
        $this->assertNotNull($article->seo);
    }

    public function test_article_translations(): void
    {
        $parent = Article::factory()->create(['title' => 'Orijinal']);
        $translation = Article::factory()->create([
            'parent_article_id' => $parent->id,
            'title' => 'Translation',
        ]);

        $this->assertTrue($parent->translations->contains($translation));
        $this->assertEquals('Orijinal', $translation->parentArticle->title);
    }

    public function test_published_at_cast(): void
    {
        $article = Article::factory()->create(['published_at' => '2025-06-15 10:30:00']);

        $article->refresh();
        $this->assertInstanceOf(\Carbon\Carbon::class, $article->published_at);
        $this->assertEquals('15.06.2025', $article->published_at->format('d.m.Y'));
    }

    public function test_article_soft_deletes(): void
    {
        $article = Article::factory()->create();
        $article->delete();

        $this->assertSoftDeleted('articles', ['id' => $article->id]);
    }
}
