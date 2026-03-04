<?php

namespace App\Observers;

use App\Models\Article;
use Illuminate\Support\Facades\Cache;

class ArticleObserver
{
    public function saved(Article $article): void
    {
        $this->clearArticleCache($article);
    }

    public function deleted(Article $article): void
    {
        $this->clearArticleCache($article);
    }

    protected function clearArticleCache(Article $article): void
    {
        // Clear SEO resolution cache
        if ($article->seo) {
            Cache::forget("seo_resolve_{$article->seo->slug}_");
        }

        // Clear sitemap cache
        Cache::forget('sitemap_xml');

        // Clear parent page's rendered layout and page cache
        if ($article->page_id) {
            Cache::forget("page_{$article->page_id}");
            Cache::forget("layout_{$article->page_id}_0");
            Cache::forget("layout_{$article->page_id}_{$article->id}");
        }
    }
}
