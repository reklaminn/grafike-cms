<?php

namespace App\Http\Resources\Api;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Page $page */
        $page = $this->resource;

        return [
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'excerpt' => null,
                'featured_image' => $page->getFirstMediaUrl('cover'),
                'template' => $page->template ?: $page->page_template,
                'layout' => $page->layout_json ?? [],
                'sections' => $page->layout_json ?? [],
                'language' => $page->language?->code,
            ],
            'seo' => [
                'title' => $page->seo?->meta_title ?: $page->title,
                'description' => $page->seo?->meta_description ?: '',
                'canonical' => $page->seo?->canonical_url ?: url($page->slug ?: '/'),
                'noindex' => (bool) ($page->seo?->is_noindex ?? false),
            ],
            'breadcrumbs' => $this->buildBreadcrumbs($page),
            'theme' => [
                'slug' => 'porto-furniture',
            ],
        ];
    }

    protected function buildBreadcrumbs(Page $page): array
    {
        $breadcrumbs = [];
        $current = $page;

        while ($current) {
            array_unshift($breadcrumbs, [
                'title' => $current->title,
                'slug' => $current->slug,
                'url' => '/'.$current->slug,
            ]);

            $current = $current->parent;
        }

        return $breadcrumbs;
    }
}
