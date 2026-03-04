<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class ContentBlockModule extends BaseModule
{
    public function getName(): string
    {
        return 'İçerik Bloğu';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.content-block';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        $targetPage = $page;
        if (!empty($config['sayfa'])) {
            $targetPage = Page::find($config['sayfa']) ?? $page;
        }

        // Load the article content for this page
        $contentArticle = $article;
        if (!$contentArticle) {
            $contentArticle = $targetPage->articles()
                ->where('status', 'published')
                ->orderBy('sort_order')
                ->first();
        }

        // Check password protection (uses session array from PageUnlockController)
        $unlockedPages = session('unlocked_pages', []);
        $isProtected = $targetPage->is_password_protected && !in_array($targetPage->id, $unlockedPages);

        return [
            'title' => $contentArticle?->title ?? $targetPage->title,
            'body' => $isProtected ? null : ($contentArticle?->body ?? ''),
            'excerpt' => $contentArticle?->excerpt ?? '',
            'isProtected' => $isProtected,
            'images' => $contentArticle ? $contentArticle->getMedia('gallery') : collect(),
            'coverImage' => $contentArticle?->getFirstMediaUrl('cover') ?? $targetPage->getFirstMediaUrl('cover'),
            'template' => $config['temp'] ?? null,
        ];
    }
}
