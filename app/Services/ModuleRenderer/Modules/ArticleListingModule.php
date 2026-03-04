<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class ArticleListingModule extends BaseModule
{
    public function getName(): string
    {
        return 'Makale Listeleme';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.article-listing';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        $pageId = $config['sayfa'] ?? $page->id;
        $perPage = $config['ladet'] ?? 10;
        $orderBy = $config['orderBy'] ?? 'sort_order';

        $query = Article::where('page_id', $pageId)
            ->where('status', 'published')
            ->with(['media', 'page']);

        // Sorting
        switch ($orderBy) {
            case 'date':
                $query->latest('published_at');
                break;
            case 'title':
                $query->orderBy('title');
                break;
            default:
                $query->orderBy('sort_order');
        }

        $articles = $query->paginate($perPage);

        return [
            'articles' => $articles,
            'title' => $config['baslik'] ?? $page->title,
            'columns' => $config['columns'] ?? 1,
            'showExcerpt' => $config['showExcerpt'] ?? true,
            'showImage' => $config['showImage'] ?? true,
            'showDate' => $config['showDate'] ?? true,
            'template' => $config['temp'] ?? null,
        ];
    }
}
