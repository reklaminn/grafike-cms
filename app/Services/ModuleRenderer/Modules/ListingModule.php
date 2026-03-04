<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class ListingModule extends BaseModule
{
    public function getName(): string
    {
        return 'Listeleme';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.listing';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        $pageId = $config['sayfa'] ?? $page->id;
        $perPage = $config['ladet'] ?? 12;
        $displayMode = $config['goster'] ?? 'grid';

        // Load articles
        $articles = Article::where('page_id', $pageId)
            ->where('status', 'published')
            ->with('media')
            ->orderBy('sort_order')
            ->paginate($perPage);

        // Load subcategories
        $subcategories = Page::where('parent_id', $pageId)
            ->where('status', 'published')
            ->with('media')
            ->withCount('articles')
            ->orderBy('sort_order')
            ->get();

        // Featured items
        $featured = Article::where('page_id', $pageId)
            ->where('status', 'published')
            ->where('is_featured', true)
            ->with('media')
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        return [
            'articles' => $articles,
            'subcategories' => $subcategories,
            'featured' => $featured,
            'title' => $config['baslik'] ?? $page->title,
            'displayMode' => $displayMode,
            'columns' => $config['columns'] ?? 3,
            'showExcerpt' => $config['showExcerpt'] ?? true,
            'showImage' => $config['showImage'] ?? true,
            'template' => $config['temp'] ?? null,
        ];
    }
}
