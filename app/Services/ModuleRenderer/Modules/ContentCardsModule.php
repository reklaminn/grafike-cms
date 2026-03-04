<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class ContentCardsModule extends BaseModule
{
    public function getName(): string
    {
        return 'İçerik Kartları';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.content-cards';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        $pageId = $config['sayfa'] ?? $page->id;
        $limit = $config['ladet'] ?? 6;

        $articles = Article::where('page_id', $pageId)
            ->where('status', 'published')
            ->with('media')
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();

        return [
            'articles' => $articles,
            'title' => $config['baslik'] ?? '',
            'columns' => $config['columns'] ?? 3,
            'showExcerpt' => $config['showExcerpt'] ?? true,
            'template' => $config['temp'] ?? null,
        ];
    }
}
