<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class TextBlockModule extends BaseModule
{
    public function getName(): string
    {
        return 'Metin Bloğu';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.text-block';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        return [
            'title' => $config['title'] ?? null,
            'body' => $config['body'] ?? '',
            'align' => $config['align'] ?? 'left',
            'maxWidth' => $config['max_width'] ?? '3xl',
        ];
    }
}
