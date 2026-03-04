<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class PageHeaderModule extends BaseModule
{
    public function getName(): string
    {
        return 'Sayfa Başlığı';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.page-header';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        // Build breadcrumb
        $breadcrumbs = [];
        $current = $page;
        while ($current) {
            array_unshift($breadcrumbs, [
                'title' => $current->title,
                'slug' => $current->slug,
                'url' => '/' . $current->slug,
            ]);
            $current = $current->parent;
        }

        return [
            'title' => $article?->title ?? $page->title,
            'subtitle' => $config['baslik'] ?? null,
            'breadcrumbs' => $breadcrumbs,
            'showBreadcrumb' => $page->show_breadcrumb,
            'backgroundImage' => $page->getFirstMediaUrl('cover'),
            'h1Override' => $page->seo?->h1_override,
        ];
    }
}
