<?php

namespace App\Services\ModuleRenderer;

use App\Models\Article;
use App\Models\Page;

interface ModuleInterface
{
    public function render(array $config, Page $page, ?Article $article = null): string;

    public function getName(): string;
}
