<?php

namespace App\Observers;

use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class PageObserver
{
    public function updating(Page $page): void
    {
        if ($page->isDirty('sections_json') || $page->isDirty('layout_json')) {
            Page::recordSnapshot($page, 'pre-update');
        }
    }

    public function saved(Page $page): void
    {
        $this->clearPageCache($page);
    }

    public function deleted(Page $page): void
    {
        $this->clearPageCache($page);
    }

    protected function clearPageCache(Page $page): void
    {
        // Clear SEO resolution cache
        if ($page->seo) {
            Cache::forget("seo_resolve_{$page->seo->slug}_");
        }

        // Clear sitemap cache
        Cache::forget('sitemap_xml');

        // Clear rendered layout cache
        Cache::forget("layout_{$page->id}_0");

        // Clear page-level cache if exists
        Cache::forget("page_{$page->id}");

        // Clear parent page cache (for menu / child list updates)
        if ($page->parent_id) {
            Cache::forget("page_{$page->parent_id}");
            Cache::forget("page_children_{$page->parent_id}");
        }
    }
}
