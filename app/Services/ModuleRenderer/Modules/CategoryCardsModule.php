<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class CategoryCardsModule extends BaseModule
{
    public function getName(): string
    {
        return 'Kategori Kartları';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.category-cards';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        $parentId = $config['sayfa'] ?? $page->id;
        $limit = $config['ladet'] ?? 6;

        $categories = Page::where('parent_id', $parentId)
            ->where('status', 'published')
            ->with('media')
            ->withCount('articles')
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();

        return [
            'categories' => $categories,
            'title' => $config['baslik'] ?? '',
            'columns' => $config['columns'] ?? 3,
            'template' => $config['temp'] ?? null,
        ];
    }
}
