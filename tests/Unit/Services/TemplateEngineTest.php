<?php

namespace Tests\Unit\Services;

use App\Models\Admin;
use App\Models\Article;
use App\Models\Page;
use App\Services\TemplateEngine\TemplateEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateEngineTest extends TestCase
{
    use RefreshDatabase;

    protected TemplateEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new TemplateEngine();
    }

    public function test_replaces_page_title_token(): void
    {
        $page = Page::factory()->create(['title' => 'Hakkımızda']);

        $result = $this->engine->render('<h1>#baslikisim#</h1>', $page);

        $this->assertEquals('<h1>Hakkımızda</h1>', $result);
    }

    public function test_replaces_article_title_when_present(): void
    {
        $page = Page::factory()->create(['title' => 'Blog']);
        $article = Article::factory()->create([
            'title' => 'Makale Başlığı',
            'page_id' => $page->id,
        ]);

        $result = $this->engine->render('#baslikisim#', $page, $article);

        $this->assertEquals('Makale Başlığı', $result);
    }

    public function test_replaces_article_body(): void
    {
        $page = Page::factory()->create();
        $article = Article::factory()->create([
            'body' => '<p>İçerik burada</p>',
            'page_id' => $page->id,
        ]);

        $result = $this->engine->render('#yaziisim#', $page, $article);

        $this->assertEquals('<p>İçerik burada</p>', $result);
    }

    public function test_replaces_date_tokens(): void
    {
        $page = Page::factory()->create();
        $article = Article::factory()->create([
            'published_at' => '2025-06-15 10:30:00',
            'page_id' => $page->id,
        ]);

        $this->assertEquals('15.06.2025', $this->engine->render('#strtarih#', $page, $article));
        $this->assertEquals('15', $this->engine->render('#strtarihday#', $page, $article));
        $this->assertEquals('06', $this->engine->render('#strtarihmonth#', $page, $article));
        $this->assertEquals('2025', $this->engine->render('#strtarihyear#', $page, $article));
    }

    public function test_replaces_author_name(): void
    {
        $admin = Admin::factory()->create(['name' => 'Ali Yılmaz']);
        $page = Page::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $admin->id,
            'page_id' => $page->id,
        ]);

        $result = $this->engine->render('#stradmin#', $page, $article);

        $this->assertEquals('Ali Yılmaz', $result);
    }

    public function test_replaces_category_name(): void
    {
        $page = Page::factory()->create(['title' => 'Haberler']);

        $result = $this->engine->render('#strkategori#', $page);

        $this->assertEquals('Haberler', $result);
    }

    public function test_handles_calculation_addition(): void
    {
        $page = Page::factory()->create();

        $result = $this->engine->render('#hesapla(10,+,5)#', $page);

        $this->assertEquals('15', $result);
    }

    public function test_handles_calculation_multiplication(): void
    {
        $page = Page::factory()->create();

        $result = $this->engine->render('#hesapla(3,*,7)#', $page);

        $this->assertEquals('21', $result);
    }

    public function test_handles_calculation_division_by_zero(): void
    {
        $page = Page::factory()->create();

        $result = $this->engine->render('#hesapla(10,/,0)#', $page);

        $this->assertEquals('0', $result);
    }

    public function test_handles_percentage_calculation(): void
    {
        $page = Page::factory()->create();

        $result = $this->engine->render('#hesapla(50,%,200)#', $page);

        $this->assertEquals('25', $result);
    }

    public function test_multiple_tokens_in_template(): void
    {
        $page = Page::factory()->create(['title' => 'Ana Sayfa']);
        $article = Article::factory()->create([
            'title' => 'Blog Yazısı',
            'excerpt' => 'Kısa özet',
            'page_id' => $page->id,
        ]);

        $template = '<h1>#baslikisim#</h1><p>#strozet#</p><small>#strkategori#</small>';
        $result = $this->engine->render($template, $page, $article);

        $this->assertStringContainsString('Blog Yazısı', $result);
        $this->assertStringContainsString('Kısa özet', $result);
        $this->assertStringContainsString('Ana Sayfa', $result);
    }

    public function test_unknown_tokens_remain_empty(): void
    {
        $page = Page::factory()->create();

        // Tokens with no data resolve to empty
        $result = $this->engine->render('#yaziisim#', $page);

        $this->assertEquals('', $result);
    }
}
