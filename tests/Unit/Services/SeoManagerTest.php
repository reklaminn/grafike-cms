<?php

namespace Tests\Unit\Services;

use App\Models\Page;
use App\Models\Redirect;
use App\Models\SeoEntry;
use App\Services\SeoManager\SeoManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoManagerTest extends TestCase
{
    use RefreshDatabase;

    protected SeoManager $seoManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seoManager = new SeoManager();
    }

    public function test_resolve_returns_redirect_when_exists(): void
    {
        Redirect::factory()->create([
            'from_url' => 'eski-sayfa',
            'to_url' => '/yeni-sayfa',
            'status_code' => 301,
        ]);

        $result = $this->seoManager->resolve('eski-sayfa');

        $this->assertNotNull($result);
        $this->assertEquals('redirect', $result['type']);
        $this->assertEquals('/yeni-sayfa', $result['url']);
        $this->assertEquals(301, $result['status_code']);
    }

    public function test_resolve_increments_redirect_hit_count(): void
    {
        $redirect = Redirect::factory()->create([
            'from_url' => 'counted',
            'hit_count' => 0,
        ]);

        $this->seoManager->resolve('counted');

        $redirect->refresh();
        $this->assertEquals(1, $redirect->hit_count);
        $this->assertNotNull($redirect->last_hit_at);
    }

    public function test_resolve_returns_content_for_seo_entry(): void
    {
        $page = Page::factory()->create();
        SeoEntry::factory()->forPage($page)->create(['slug' => 'test-sayfa']);

        Cache::flush(); // Clear any cached results

        $result = $this->seoManager->resolve('test-sayfa');

        $this->assertNotNull($result);
        $this->assertEquals('content', $result['type']);
        $this->assertEquals(Page::class, $result['entity_type']);
        $this->assertEquals($page->id, $result['entity']->id);
    }

    public function test_resolve_returns_null_for_unknown_slug(): void
    {
        $result = $this->seoManager->resolve('nonexistent-page');

        $this->assertNull($result);
    }

    public function test_generate_slug_basic(): void
    {
        $slug = $this->seoManager->generateSlug('Hakkımızda Sayfası');

        $this->assertEquals('hakkimizda-sayfasi', $slug);
    }

    public function test_generate_slug_turkish_characters(): void
    {
        $slug = $this->seoManager->generateSlug('Türkçe Özel Karakterler: ç, ğ, ı, ö, ş, ü');

        $this->assertStringNotContainsString('ç', $slug);
        $this->assertStringNotContainsString('ğ', $slug);
        $this->assertStringNotContainsString('ı', $slug);
        $this->assertStringNotContainsString('ö', $slug);
        $this->assertStringNotContainsString('ş', $slug);
        $this->assertStringNotContainsString('ü', $slug);
        $this->assertStringContainsString('turkce', $slug);
    }

    public function test_generate_slug_removes_special_characters(): void
    {
        $slug = $this->seoManager->generateSlug('Test! @Sayfa# $İçeriği%');

        $this->assertDoesNotMatchRegularExpression('/[^a-z0-9-]/', $slug);
    }

    public function test_generate_slug_trims_hyphens(): void
    {
        $slug = $this->seoManager->generateSlug('  Test Sayfa  ');

        $this->assertFalse(str_starts_with($slug, '-'));
        $this->assertFalse(str_ends_with($slug, '-'));
    }

    public function test_clear_cache_for_specific_slug(): void
    {
        Cache::put('seo_resolve_test-slug', ['cached' => true], 600);

        $this->seoManager->clearCache('test-slug');

        $this->assertNull(Cache::get('seo_resolve_test-slug'));
    }

    public function test_resolve_caches_result(): void
    {
        $page = Page::factory()->create();
        SeoEntry::factory()->forPage($page)->create(['slug' => 'cached-page']);

        // First call
        $this->seoManager->resolve('cached-page');

        // Verify cache
        $this->assertTrue(Cache::has('seo_resolve_cached-page_'));
    }
}
