<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use App\Models\Language;
use App\Models\Page;
use App\Models\SeoEntry;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleApiTest extends TestCase
{
    use RefreshDatabase;

    private Language $language;
    private Page $page;

    protected function setUp(): void
    {
        parent::setUp();

        $this->language = Language::factory()->create(['code' => 'tr', 'locale' => 'tr_TR']);
        $this->page     = Page::factory()->create(['language_id' => $this->language->id]);
    }

    // ─── index ───────────────────────────────────────────────────────────

    public function test_index_returns_published_articles(): void
    {
        Article::factory()->count(3)->create([
            'page_id'     => $this->page->id,
            'language_id' => $this->language->id,
        ]);
        Article::factory()->draft()->create([
            'page_id'     => $this->page->id,
            'language_id' => $this->language->id,
        ]);

        $this->getJson('/api/v1/articles')
            ->assertOk()
            ->assertJsonPath('meta.total', 3);
    }

    public function test_index_filters_by_page_id(): void
    {
        $other = Page::factory()->create(['language_id' => $this->language->id]);

        Article::factory()->count(2)->create(['page_id' => $this->page->id,  'language_id' => $this->language->id]);
        Article::factory()->count(3)->create(['page_id' => $other->id, 'language_id' => $this->language->id]);

        $this->getJson("/api/v1/articles?page_id={$this->page->id}")
            ->assertOk()
            ->assertJsonPath('meta.total', 2);
    }

    public function test_index_filters_by_site_id(): void
    {
        // Site model'i doğrudan oluştur (factory yok)
        $site  = \App\Models\Site::create(['name' => 'Site A', 'domain' => 'site-a.test', 'slug' => 'site-a', 'status' => 'active']);
        $other = \App\Models\Site::create(['name' => 'Site B', 'domain' => 'site-b.test', 'slug' => 'site-b', 'status' => 'active']);

        Article::factory()->count(2)->create(['site_id' => $site->id,  'page_id' => $this->page->id, 'language_id' => $this->language->id]);
        Article::factory()->count(4)->create(['site_id' => $other->id, 'page_id' => $this->page->id, 'language_id' => $this->language->id]);

        $this->getJson("/api/v1/articles?site_id={$site->id}")
            ->assertOk()
            ->assertJsonPath('meta.total', 2);
    }

    public function test_index_filters_by_language(): void
    {
        $en = Language::factory()->create(['code' => 'en', 'locale' => 'en_US']);

        Article::factory()->count(2)->create(['page_id' => $this->page->id, 'language_id' => $this->language->id]);
        Article::factory()->count(3)->create(['page_id' => $this->page->id, 'language_id' => $en->id]);

        $this->getJson('/api/v1/articles?lang=tr')
            ->assertOk()
            ->assertJsonPath('meta.total', 2);
    }

    public function test_index_featured_only_filter(): void
    {
        Article::factory()->count(3)->create(['page_id' => $this->page->id, 'language_id' => $this->language->id]);
        Article::factory()->featured()->count(2)->create(['page_id' => $this->page->id, 'language_id' => $this->language->id]);

        $this->getJson('/api/v1/articles?featured_only=1')
            ->assertOk()
            ->assertJsonPath('meta.total', 2);
    }

    public function test_index_respects_limit_parameter(): void
    {
        Article::factory()->count(10)->create(['page_id' => $this->page->id, 'language_id' => $this->language->id]);

        $this->getJson('/api/v1/articles?limit=3')
            ->assertOk()
            ->assertJsonPath('meta.per_page', 3)
            ->assertJsonPath('meta.total', 10);
    }

    public function test_index_item_contains_required_fields(): void
    {
        Article::factory()->create([
            'page_id'      => $this->page->id,
            'language_id'  => $this->language->id,
            'is_featured'  => true,
            'published_at' => now(),
        ]);

        // Standard ResourceCollection format: data[] at root, meta at root
        $this->getJson('/api/v1/articles')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id', 'title', 'slug', 'excerpt',
                    'display_date', 'published_at', 'is_featured', 'cover',
                ]],
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ]);
    }

    // ─── show ────────────────────────────────────────────────────────────

    public function test_show_returns_article_detail(): void
    {
        $article = Article::factory()->create([
            'page_id'      => $this->page->id,
            'language_id'  => $this->language->id,
            'content_json' => [['type' => 'heading', 'level' => 1, 'text' => 'Test Başlık']],
        ]);

        // SeoManager resolves by SeoEntry slug
        SeoEntry::create([
            'seoable_id'   => $article->id,
            'seoable_type' => Article::class,
            'slug'         => $article->slug,
            'language_id'  => $this->language->id,
        ]);

        // ArticleResource wraps in data (standard JsonResource behaviour)
        $this->getJson("/api/v1/articles/{$article->slug}")
            ->assertOk()
            ->assertJsonPath('data.article.slug', $article->slug)
            ->assertJsonPath('data.article.content_json.0.type', 'heading')
            ->assertJsonStructure([
                'data' => [
                    'article' => [
                        'id', 'title', 'slug', 'excerpt', 'body',
                        'content_json', 'display_date', 'published_at',
                        'listing_variant', 'detail_variant', 'is_featured', 'cover', 'gallery',
                    ],
                    'author',
                    'language',
                    'page',
                    'seo' => ['title', 'description', 'canonical', 'noindex'],
                ],
            ]);
    }

    public function test_show_returns_404_for_draft(): void
    {
        $article = Article::factory()->draft()->create([
            'page_id'     => $this->page->id,
            'language_id' => $this->language->id,
        ]);

        SeoEntry::create([
            'seoable_id'   => $article->id,
            'seoable_type' => Article::class,
            'slug'         => $article->slug,
            'language_id'  => $this->language->id,
        ]);

        $this->getJson("/api/v1/articles/{$article->slug}")->assertNotFound();
    }

    public function test_show_returns_404_for_unknown_slug(): void
    {
        $this->getJson('/api/v1/articles/yoktur-boyle-bir-yazi')->assertNotFound();
    }
}
