<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;
use App\Services\ModuleRenderer\ModuleInterface;
use Illuminate\Support\Facades\View;

abstract class BaseModule implements ModuleInterface
{
    abstract protected function getViewName(): string;

    abstract protected function getData(array $config, Page $page, ?Article $article): array;

    public function render(array $config, Page $page, ?Article $article = null): string
    {
        $data = $this->getData($config, $page, $article);
        $viewName = $this->getViewName();

        if (! View::exists($viewName)) {
            return "<!-- View not found: {$viewName} -->";
        }

        return view($viewName, array_merge($data, [
            'config' => $config,
            'page' => $page,
            'article' => $article,
        ]))->render();
    }
}
