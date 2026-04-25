<?php

namespace App\Support;

/**
 * Renders an article's content_json block array to plain HTML,
 * stored back in the `body` column for backward compat with the template engine.
 *
 * Image block format (v2 — gallery):
 *   { "type": "image", "images": [{ "url": "...", "alt": "...", "caption": "..." }, ...] }
 *
 * Legacy image format (v1 — single):
 *   { "type": "image", "url": "...", "alt": "...", "caption": "..." }
 *   → auto-promoted to images array for rendering.
 */
class ArticleBlockRenderer
{
    public static function toHtml(array $blocks): string
    {
        return implode("\n", array_filter(array_map(
            fn (array $block) => match ($block['type'] ?? '') {
                'heading'   => self::heading($block),
                'paragraph' => self::paragraph($block),
                'image'     => self::image($block),
                'video'     => self::video($block),
                'html'      => self::html($block),
                default     => '',
            },
            $blocks
        )));
    }

    // ─── Block renderers ────────────────────────────────────────────────

    private static function heading(array $b): string
    {
        $level = max(1, min(6, (int) ($b['level'] ?? 2)));
        $text  = htmlspecialchars($b['text'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return $text ? "<h{$level}>{$text}</h{$level}>" : '';
    }

    private static function paragraph(array $b): string
    {
        return trim($b['content'] ?? '');
    }

    private static function image(array $b): string
    {
        // Promote legacy single-image format to images array
        if (isset($b['images']) && is_array($b['images'])) {
            $images = array_values(array_filter($b['images'], fn ($i) => trim($i['url'] ?? '') !== ''));
        } elseif (!empty($b['url'])) {
            $images = [['url' => $b['url'], 'alt' => $b['alt'] ?? '', 'caption' => $b['caption'] ?? '']];
        } else {
            return '';
        }

        if (empty($images)) {
            return '';
        }

        if (count($images) === 1) {
            return self::singleFigure($images[0]);
        }

        // Gallery wrapper for multiple images
        $inner = implode("\n", array_map([self::class, 'singleFigure'], $images));

        return '<div class="article-gallery">' . "\n" . $inner . "\n" . '</div>';
    }

    private static function singleFigure(array $img): string
    {
        $url     = trim($img['url'] ?? '');
        $alt     = htmlspecialchars($img['alt'] ?? '', ENT_QUOTES, 'UTF-8');
        $caption = trim($img['caption'] ?? '');
        $imgTag  = '<img src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" alt="' . $alt . '" loading="lazy">';

        return $caption
            ? '<figure>' . $imgTag . '<figcaption>' . htmlspecialchars($caption, ENT_QUOTES, 'UTF-8') . '</figcaption></figure>'
            : '<figure>' . $imgTag . '</figure>';
    }

    private static function video(array $b): string
    {
        $embed = trim($b['embed_url'] ?? '');
        $url   = trim($b['url'] ?? '');

        if ($embed) {
            $safe = htmlspecialchars($embed, ENT_QUOTES, 'UTF-8');

            return '<div class="video-embed" style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden">'
                . '<iframe src="' . $safe . '" frameborder="0" allowfullscreen loading="lazy" '
                . 'style="position:absolute;top:0;left:0;width:100%;height:100%"></iframe>'
                . '</div>';
        }

        if ($url) {
            $safe = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

            return '<video src="' . $safe . '" controls style="max-width:100%"></video>';
        }

        return '';
    }

    private static function html(array $b): string
    {
        return trim($b['code'] ?? '');
    }
}
