<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class SideMenuModule extends BaseModule
{
    public function getName(): string
    {
        return 'Yan Menü';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.side-menu';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        $parentId = $config['sayfa'] ?? $page->id;
        $menuPage = Page::find($parentId) ?? $page;

        // Get the root page for this section
        $rootPage = $menuPage;
        while ($rootPage->parent_id) {
            $rootPage = $rootPage->parent()->first() ?? $rootPage;
            if (!$rootPage->parent_id) break;
        }

        // Get children for the sidebar navigation
        $menuItems = Page::where('parent_id', $rootPage->id)
            ->where('status', 'published')
            ->where('show_in_menu', true)
            ->orderBy('sort_order')
            ->get(['id', 'title', 'slug', 'parent_id']);

        return [
            'menuItems' => $menuItems,
            'currentPageId' => $page->id,
            'rootPage' => $rootPage,
            'title' => $config['baslik'] ?? $rootPage->title,
        ];
    }
}
