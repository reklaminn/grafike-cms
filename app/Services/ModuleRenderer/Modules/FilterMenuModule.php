<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class FilterMenuModule extends BaseModule
{
    public function getName(): string
    {
        return 'Filtre Menü';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.filter-menu';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        $pageId = $config['sayfa'] ?? $page->id;

        // Get all child pages as filter categories
        $filterCategories = Page::where('parent_id', $pageId)
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->get(['id', 'title', 'slug']);

        // Get articles for default display
        $articles = Article::where('page_id', $pageId)
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->get();

        return [
            'filterCategories' => $filterCategories,
            'articles' => $articles,
            'currentFilter' => request('filter'),
            'title' => $config['baslik'] ?? 'Filtrele',
            'parentPageId' => $pageId,
        ];
    }
}
