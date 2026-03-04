<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class CategoryListingModule extends BaseModule
{
    public function getName(): string
    {
        return 'Kategori Listeleme';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.category-listing';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        $parentId = $config['sayfa'] ?? $page->id;
        $limit = $config['ladet'] ?? 20;

        $categories = Page::where('parent_id', $parentId)
            ->where('status', 'published')
            ->where('show_in_menu', true)
            ->withCount('articles')
            ->with('media')
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();

        return [
            'categories' => $categories,
            'title' => $config['baslik'] ?? $page->title,
            'columns' => $config['columns'] ?? 3,
            'showCount' => $config['showCount'] ?? true,
            'template' => $config['temp'] ?? null,
        ];
    }
}
