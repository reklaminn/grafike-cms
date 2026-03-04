<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class ImageGalleryModule extends BaseModule
{
    public function getName(): string
    {
        return 'Resim Galerisi';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.image-gallery';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        $images = collect();

        // Get images from current article's gallery
        if ($article) {
            $images = $article->getMedia('gallery');
        }

        // Also check page-level images
        if ($images->isEmpty()) {
            $pageId = $config['sayfa'] ?? $page->id;
            $pageArticles = Article::where('page_id', $pageId)
                ->where('status', 'published')
                ->with('media')
                ->orderBy('sort_order')
                ->get();

            foreach ($pageArticles as $art) {
                $artImages = $art->getMedia('gallery');
                if ($artImages->isNotEmpty()) {
                    $images = $images->merge($artImages);
                }
                // Also add cover images
                $cover = $art->getFirstMedia('cover');
                if ($cover) {
                    $images->push($cover);
                }
            }
        }

        return [
            'images' => $images,
            'title' => $config['baslik'] ?? '',
            'columns' => $config['columns'] ?? 4,
            'lightbox' => $config['lightbox'] ?? true,
            'template' => $config['temp'] ?? null,
        ];
    }
}
