<?php

namespace App\Support;

/**
 * Renders an article's content_json block array to plain HTML,
 * which is stored back in the `body` column for backward compatibility
 * with the legacy template engine.
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

    private static function heading(array $b): string
    {
        $level = max(1, min(6, (int) ($b['level'] ?? 2)));
        $text  = htmlspecialchars($b['text'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return $text ? "<h{$level}>{$text}</h{$level}>" : '';
    }

    private static function paragraph(array $b): string
    {
        $content = trim($b['content'] ?? '');

        return $content ?: '';
    }

    private static function image(array $b): string
    {
        $url = trim($b['url'] ?? '');
        if (! $url) {
            return '';
        }

        $alt     = htmlspecialchars($b['alt'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $caption = trim($b['caption'] ?? '');
        $img     = '<img src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" alt="' . $alt . '" loading="lazy">';

        return $caption
            ? '<figure>' . $img . '<figcaption>' . htmlspecialchars($caption, ENT_QUOTES, 'UTF-8') . '</figcaption></figure>'
            : '<figure>' . $img . '</figure>';
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
