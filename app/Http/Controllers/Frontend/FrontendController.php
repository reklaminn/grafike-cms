<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\PageBuilder\LayoutRenderer;
use App\Services\SeoManager\SeoManager;
use Illuminate\Support\Facades\Cache;

class FrontendController extends Controller
{
    public function __construct(
        protected SeoManager $seoManager,
        protected LayoutRenderer $layoutRenderer,
    ) {}

    public function home()
    {
        $homepageId = config('cms.homepage_id');
        $page = Page::where('legacy_id', $homepageId)
            ->orWhere('id', $homepageId)
            ->where('status', 'published')
            ->first();

        if (! $page) {
            return view('welcome');
        }

        return $this->renderPage($page);
    }

    public function show(string $slug)
    {
        $resolved = $this->seoManager->resolve($slug);

        if (! $resolved) {
            abort(404);
        }

        if ($resolved['type'] === 'redirect') {
            return redirect($resolved['url'], $resolved['status_code']);
        }

        $entity = $resolved['entity'];
        $seo = $resolved['seo'];

        if ($entity instanceof Page) {
            return $this->renderPage($entity, null, $seo);
        }

        // Article - load parent page
        $page = $entity->page;

        return $this->renderPage($page, $entity, $seo);
    }

    protected function renderPage(Page $page, $article = null, $seo = null)
    {
        // Increment view count (non-blocking, once per session)
        $viewKey = "viewed_page_{$page->id}";
        if (! session()->has($viewKey)) {
            $page->increment('view_count');
            session([$viewKey => true]);
        }

        $seo = $seo ?? $page->seo;

        // Cache rendered layout for published pages (bypass in debug mode)
        $cacheEnabled = config('cms.cache.enabled', true) && ! config('app.debug');
        $cacheKey = "layout_{$page->id}_" . ($article?->id ?? '0');

        if ($cacheEnabled) {
            $renderedLayout = Cache::remember($cacheKey, config('cms.cache.ttl', 600), function () use ($page, $article) {
                return $this->layoutRenderer->render($page, $article);
            });
        } else {
            $renderedLayout = $this->layoutRenderer->render($page, $article);
        }

        return view('frontend.layouts.main', [
            'page' => $page,
            'article' => $article,
            'seo' => $seo,
            'renderedLayout' => $renderedLayout,
        ]);
    }
}
