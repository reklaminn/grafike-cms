<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class VideoEmbedModule extends BaseModule
{
    public function getName(): string
    {
        return 'Video Embed';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.video-embed';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        return [
            'title' => $config['title'] ?? null,
            'embedUrl' => $this->normalizeEmbedUrl($config['embed_url'] ?? ''),
            'aspectRatio' => $config['aspect_ratio'] ?? '16:9',
        ];
    }

    protected function normalizeEmbedUrl(string $url): string
    {
        $url = trim($url);

        if ($url === '') {
            return '';
        }

        if (str_contains($url, 'youtube.com/watch')) {
            parse_str(parse_url($url, PHP_URL_QUERY) ?: '', $query);
            if (! empty($query['v'])) {
                return 'https://www.youtube.com/embed/'.$query['v'];
            }
        }

        if (str_contains($url, 'youtu.be/')) {
            $path = trim(parse_url($url, PHP_URL_PATH) ?: '', '/');
            if ($path !== '') {
                return 'https://www.youtube.com/embed/'.$path;
            }
        }

        if (str_contains($url, 'vimeo.com/') && ! str_contains($url, '/video/')) {
            $path = trim(parse_url($url, PHP_URL_PATH) ?: '', '/');
            if ($path !== '') {
                return 'https://player.vimeo.com/video/'.$path;
            }
        }

        return $url;
    }
}
