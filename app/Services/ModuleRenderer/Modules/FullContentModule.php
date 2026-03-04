<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class FullContentModule extends BaseModule
{
    public function getName(): string
    {
        return 'Tam Icerik';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.full-content';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        return [
            'title' => $article?->title ?? $page->title,
            'body' => $article?->body ?? '',
            'excerpt' => $article?->excerpt ?? '',
        ];
    }
}
