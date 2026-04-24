<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class SpacerModule extends BaseModule
{
    public function getName(): string
    {
        return 'Spacer';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.spacer';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        return [
            'height' => max(0, (int) ($config['height'] ?? 64)),
            'heightMobile' => max(0, (int) ($config['height_mobile'] ?? 32)),
        ];
    }
}
