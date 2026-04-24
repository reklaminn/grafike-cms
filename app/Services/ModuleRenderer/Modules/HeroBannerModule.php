<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class HeroBannerModule extends BaseModule
{
    public function getName(): string
    {
        return 'Hero Banner';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.hero-banner';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        return [
            'title' => $config['title'] ?? ($article?->title ?? $page->title),
            'subtitle' => $config['subtitle'] ?? ($article?->excerpt ?? ''),
            'buttonText' => $config['button_text'] ?? null,
            'buttonUrl' => $config['button_url'] ?? null,
            'backgroundImage' => $config['background_image'] ?? ($page->getFirstMediaUrl('cover') ?: null),
            'align' => $config['align'] ?? 'left',
            'theme' => $config['theme'] ?? 'dark',
        ];
    }
}
