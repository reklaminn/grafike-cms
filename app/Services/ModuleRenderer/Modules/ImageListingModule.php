<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class ImageListingModule extends BaseModule
{
    public function getName(): string
    {
        return 'Resim Listeleme';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.image-listing';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        $pageId = $config['sayfa'] ?? $page->id;
        $limit = $config['ladet'] ?? 12;

        $articles = Article::where('page_id', $pageId)
            ->where('status', 'published')
            ->with('media')
            ->orderBy('sort_order')
            ->paginate($limit);

        $images = [];
        foreach ($articles as $art) {
            $coverUrl = $art->getFirstMediaUrl('cover');
            if ($coverUrl) {
                $images[] = [
                    'url' => $coverUrl,
                    'thumb' => $art->getFirstMediaUrl('cover', 'thumb') ?: $coverUrl,
                    'title' => $art->title,
                    'link' => '/' . $art->slug,
                    'alt' => $art->title,
                ];
            }
            // Also include gallery images
            foreach ($art->getMedia('gallery') as $media) {
                $images[] = [
                    'url' => $media->getUrl(),
                    'thumb' => $media->getUrl('thumb') ?: $media->getUrl(),
                    'title' => $media->getCustomProperty('alt_text', $art->title),
                    'link' => '/' . $art->slug,
                    'alt' => $media->getCustomProperty('alt_text', $art->title),
                ];
            }
        }

        return [
            'images' => $images,
            'articles' => $articles,
            'title' => $config['baslik'] ?? $page->title,
            'columns' => $config['columns'] ?? 4,
            'template' => $config['temp'] ?? null,
        ];
    }
}
