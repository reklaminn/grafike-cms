<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;
use App\Models\Redirect;

class NotFoundModule extends BaseModule
{
    public function getName(): string
    {
        return '404 Sayfası';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.not-found';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        // Check for legacy URL redirect
        $requestedUrl = request()->path();
        $redirect = Redirect::where('old_url', '/' . $requestedUrl)
            ->where('is_active', true)
            ->first();

        return [
            'title' => '404 - Sayfa Bulunamadı',
            'message' => 'Aradığınız sayfa bulunamadı veya kaldırılmış olabilir.',
            'redirect' => $redirect,
            'requestedUrl' => $requestedUrl,
            'suggestedPages' => Page::where('status', 'published')
                ->where('show_in_menu', true)
                ->orderBy('sort_order')
                ->limit(6)
                ->get(['title', 'slug']),
        ];
    }
}
