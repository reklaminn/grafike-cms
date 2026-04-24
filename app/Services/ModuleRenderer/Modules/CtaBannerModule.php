<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class CtaBannerModule extends BaseModule
{
    public function getName(): string
    {
        return 'CTA Banner';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.cta-banner';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        return [
            'title' => $config['title'] ?? 'Güçlü bir çağrı alanı',
            'body' => $config['body'] ?? '',
            'buttonText' => $config['button_text'] ?? null,
            'buttonUrl' => $config['button_url'] ?? '#',
            'secondaryText' => $config['secondary_text'] ?? null,
            'secondaryUrl' => $config['secondary_url'] ?? null,
            'theme' => $config['theme'] ?? 'brand',
        ];
    }
}
