<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PageResource;
use App\Models\Page;
use App\Models\Site;
use App\Models\SiteSetting;
use App\Services\SeoManager\SeoManager;

class PageController extends Controller
{
    public function __construct(protected SeoManager $seoManager) {}

    public function show(string $slug)
    {
        $site = Site::resolve(request()->header('X-Site-Host'));

        if ($slug === 'home') {
            $homepageId = SiteSetting::get('cms.homepage_id', config('cms.homepage_id'), $site?->id);
            $page = Page::query()
                ->when($site, fn ($query) => $query->where('site_id', $site->id))
                ->where(fn ($query) => $query
                    ->where('legacy_id', $homepageId)
                    ->orWhere('id', $homepageId)
                )
                ->published()
                ->with(['seo', 'language', 'parent', 'site.theme'])
                ->first();

            abort_if(! $page, 404);

            return PageResource::make($page);
        }

        $resolved = $this->seoManager->resolve($slug);

        if (! $resolved) {
            $page = Page::query()
                ->when($site, function ($query) use ($site) {
                    $query->where(function ($inner) use ($site) {
                        $inner->where('site_id', $site->id)
                            ->orWhereNull('site_id');
                    });
                })
                ->where('slug', $slug)
                ->published()
                ->with(['seo', 'language', 'parent', 'site.theme'])
                ->first();

            abort_if(! $page, 404);

            return PageResource::make($page);
        }

        if (($resolved['type'] ?? null) === 'redirect') {
            return response()->json([
                'type' => 'redirect',
                'url' => $resolved['url'],
                'status_code' => $resolved['status_code'],
            ]);
        }

        $entity = $resolved['entity'] ?? null;
        abort_if(! $entity instanceof Page, 404);
        abort_if($entity->status !== 'published', 404);

        $entity->loadMissing(['seo', 'language', 'parent', 'site.theme']);

        return PageResource::make($entity);
    }
}
