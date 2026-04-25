<?php

namespace Tests\Unit\Support;

use App\Support\ArticleBlockRenderer;
use Tests\TestCase;

class ArticleBlockRendererTest extends TestCase
{
    public function test_renders_heading_block(): void
    {
        $html = ArticleBlockRenderer::toHtml([
            ['type' => 'heading', 'level' => 2, 'text' => 'Merhaba Dünya'],
        ]);

        $this->assertStringContainsString('<h2>Merhaba Dünya</h2>', $html);
    }

    public function test_heading_level_is_clamped_between_1_and_6(): void
    {
        $html = ArticleBlockRenderer::toHtml([
            ['type' => 'heading', 'level' => 9, 'text' => 'Taşan'],
        ]);
        $this->assertStringContainsString('<h6>', $html);

        $html2 = ArticleBlockRenderer::toHtml([
            ['type' => 'heading', 'level' => 0, 'text' => 'Sıfır'],
        ]);
        $this->assertStringContainsString('<h1>', $html2);
    }

    public function test_heading_text_is_escaped(): void
    {
        $html = ArticleBlockRenderer::toHtml([
            ['type' => 'heading', 'level' => 2, 'text' => '<script>alert(1)</script>'],
        ]);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_renders_paragraph_block(): void
    {
        $html = ArticleBlockRenderer::toHtml([
            ['type' => 'paragraph', 'content' => '<p>İçerik <strong>burada</strong>.</p>'],
        ]);

        $this->assertStringContainsString('<p>İçerik <strong>burada</strong>.</p>', $html);
    }

    public function test_renders_image_block_with_caption(): void
    {
        $html = ArticleBlockRenderer::toHtml([
            ['type' => 'image', 'url' => 'https://example.com/img.jpg', 'alt' => 'Test', 'caption' => 'Açıklama'],
        ]);

        $this->assertStringContainsString('<figure>', $html);
        $this->assertStringContainsString('alt="Test"', $html);
        $this->assertStringContainsString('<figcaption>Açıklama</figcaption>', $html);
    }

    public function test_image_block_without_url_returns_empty(): void
    {
        $html = ArticleBlockRenderer::toHtml([
            ['type' => 'image', 'url' => '', 'alt' => '', 'caption' => ''],
        ]);

        $this->assertEmpty(trim($html));
    }

    public function test_renders_youtube_video_block(): void
    {
        $html = ArticleBlockRenderer::toHtml([
            ['type' => 'video', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'embed_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ'],
        ]);

        $this->assertStringContainsString('<iframe', $html);
        $this->assertStringContainsString('youtube.com/embed/', $html);
    }

    public function test_renders_html_block(): void
    {
        $code = '<div class="custom"><p>İçerik</p></div>';

        $html = ArticleBlockRenderer::toHtml([
            ['type' => 'html', 'code' => $code],
        ]);

        $this->assertStringContainsString($code, $html);
    }

    public function test_empty_blocks_array_returns_empty_string(): void
    {
        $this->assertEmpty(ArticleBlockRenderer::toHtml([]));
    }

    public function test_multiple_blocks_are_concatenated(): void
    {
        $html = ArticleBlockRenderer::toHtml([
            ['type' => 'heading',   'level' => 1, 'text' => 'Başlık'],
            ['type' => 'paragraph', 'content' => '<p>Paragraf</p>'],
            ['type' => 'html',      'code' => '<hr>'],
        ]);

        $this->assertStringContainsString('<h1>Başlık</h1>', $html);
        $this->assertStringContainsString('<p>Paragraf</p>', $html);
        $this->assertStringContainsString('<hr>', $html);
    }

    // ─── Gallery (v2 image block) ─────────────────────────────────────────

    public function test_single_image_in_gallery_array_renders_figure_without_wrapper(): void
    {
        $html = ArticleBlockRenderer::toHtml([
            ['type' => 'image', 'images' => [
                ['url' => 'https://example.com/a.jpg', 'alt' => 'Tek', 'caption' => ''],
            ]],
        ]);

        $this->assertStringContainsString('<figure>', $html);
        $this->assertStringContainsString('src="https://example.com/a.jpg"', $html);
        $this->assertStringNotContainsString('article-gallery', $html);
    }

    public function test_multiple_images_render_gallery_wrapper(): void
    {
        $html = ArticleBlockRenderer::toHtml([
            ['type' => 'image', 'images' => [
                ['url' => 'https://example.com/a.jpg', 'alt' => 'Bir', 'caption' => 'İlk'],
                ['url' => 'https://example.com/b.jpg', 'alt' => 'İki', 'caption' => ''],
            ]],
        ]);

        $this->assertStringContainsString('class="article-gallery"', $html);
        $this->assertStringContainsString('example.com/a.jpg', $html);
        $this->assertStringContainsString('example.com/b.jpg', $html);
        $this->assertStringContainsString('<figcaption>İlk</figcaption>', $html);
        $this->assertSame(2, substr_count($html, '<figure>'));
    }

    public function test_legacy_single_url_image_is_promoted_to_gallery_format(): void
    {
        $html = ArticleBlockRenderer::toHtml([
            ['type' => 'image', 'url' => 'https://example.com/legacy.jpg', 'alt' => 'Eski', 'caption' => 'Eski başlık'],
        ]);

        $this->assertStringContainsString('<figure>', $html);
        $this->assertStringContainsString('legacy.jpg', $html);
        $this->assertStringContainsString('<figcaption>Eski başlık</figcaption>', $html);
        $this->assertStringNotContainsString('article-gallery', $html);
    }

    public function test_gallery_with_all_empty_urls_returns_empty(): void
    {
        $html = ArticleBlockRenderer::toHtml([
            ['type' => 'image', 'images' => [
                ['url' => '', 'alt' => '', 'caption' => ''],
                ['url' => '  ', 'alt' => '', 'caption' => ''],
            ]],
        ]);

        $this->assertEmpty(trim($html));
    }

    public function test_gallery_skips_images_with_empty_url(): void
    {
        $html = ArticleBlockRenderer::toHtml([
            ['type' => 'image', 'images' => [
                ['url' => 'https://example.com/good.jpg', 'alt' => 'Geçerli', 'caption' => ''],
                ['url' => '',                              'alt' => 'Boş URL', 'caption' => ''],
            ]],
        ]);

        // Only 1 valid image → single figure, no gallery wrapper
        $this->assertStringContainsString('good.jpg', $html);
        $this->assertStringNotContainsString('Boş URL', $html);
        $this->assertStringNotContainsString('article-gallery', $html);
    }
}
