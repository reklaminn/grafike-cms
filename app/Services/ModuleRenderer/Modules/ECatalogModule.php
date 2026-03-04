<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class ECatalogModule extends BaseModule
{
    public function getName(): string
    {
        return 'E-Katalog';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.e-catalog';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        $pageId = $config['sayfa'] ?? $page->id;
        $perPage = $config['ladet'] ?? 12;

        $products = Article::where('page_id', $pageId)
            ->where('status', 'published')
            ->with('media')
            ->orderBy('sort_order')
            ->paginate($perPage);

        // Get subcategories for sidebar filtering
        $subcategories = Page::where('parent_id', $pageId)
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->get(['id', 'title', 'slug']);

        return [
            'products' => $products,
            'subcategories' => $subcategories,
            'title' => $config['baslik'] ?? $page->title,
            'columns' => $config['columns'] ?? 3,
            'showPrice' => $config['showPrice'] ?? false,
            'template' => $config['temp'] ?? null,
        ];
    }
}
