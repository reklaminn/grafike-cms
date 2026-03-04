<?php

namespace Tests\Unit\Models;

use App\Models\Article;
use App\Models\Language;
use App\Models\Page;
use App\Models\SeoEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_page(): void
    {
        $language = Language::factory()->create(['code' => 'tr']);
        $page = Page::factory()->create([
            'title' => 'Test Sayfası',
            'language_id' => $language->id,
        ]);

        $this->assertDatabaseHas('pages', [
            'title' => 'Test Sayfası',
            'language_id' => $language->id,
        ]);
    }

    public function test_page_has_parent_child_relationship(): void
    {
        $parent = Page::factory()->create(['title' => 'Ana Sayfa']);
        $child = Page::factory()->withParent($parent)->create(['title' => 'Alt Sayfa']);

        $this->assertEquals($parent->id, $child->parent_id);
        $this->assertTrue($parent->children->contains($child));
        $this->assertEquals('Ana Sayfa', $child->parent->title);
    }

    public function test_page_has_articles(): void
    {
        $page = Page::factory()->create();
        $article = Article::factory()->create(['page_id' => $page->id]);

        $this->assertTrue($page->articles->contains($article));
    }

    public function test_page_has_seo_relationship(): void
    {
        $page = Page::factory()->create();
        $seo = SeoEntry::factory()->forPage($page)->create();

        $page->refresh();
        $this->assertNotNull($page->seo);
        $this->assertEquals($seo->id, $page->seo->id);
    }

    public function test_published_scope(): void
    {
        Page::factory()->create(['status' => 'published']);
        Page::factory()->create(['status' => 'draft']);
        Page::factory()->create(['status' => 'archived']);

        $published = Page::published()->get();

        $this->assertCount(1, $published);
    }

    public function test_by_language_scope(): void
    {
        $tr = Language::factory()->create(['code' => 'tr']);
        $en = Language::factory()->create(['code' => 'en']);

        Page::factory()->create(['language_id' => $tr->id]);
        Page::factory()->create(['language_id' => $tr->id]);
        Page::factory()->create(['language_id' => $en->id]);

        $this->assertCount(2, Page::byLanguage($tr->id)->get());
        $this->assertCount(1, Page::byLanguage($en->id)->get());
    }

    public function test_layout_json_is_cast_to_array(): void
    {
        $page = Page::factory()->create([
            'layout_json' => ['header' => [], 'body' => [], 'footer' => []],
        ]);

        $page->refresh();
        $this->assertIsArray($page->layout_json);
        $this->assertArrayHasKey('header', $page->layout_json);
    }

    public function test_boolean_casts(): void
    {
        $page = Page::factory()->create([
            'is_password_protected' => 1,
            'show_in_menu' => 0,
            'show_social_share' => 1,
        ]);

        $page->refresh();
        $this->assertTrue($page->is_password_protected);
        $this->assertFalse($page->show_in_menu);
        $this->assertTrue($page->show_social_share);
    }

    public function test_page_soft_deletes(): void
    {
        $page = Page::factory()->create();
        $page->delete();

        $this->assertSoftDeleted('pages', ['id' => $page->id]);
        $this->assertCount(0, Page::all());
        $this->assertCount(1, Page::withTrashed()->get());
    }

    public function test_page_belongs_to_language(): void
    {
        $language = Language::factory()->create(['name' => 'Türkçe']);
        $page = Page::factory()->create(['language_id' => $language->id]);

        $this->assertEquals('Türkçe', $page->language->name);
    }
}
