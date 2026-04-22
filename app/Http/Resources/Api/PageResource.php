<?php

namespace App\Http\Resources\Api;

use App\Models\Page;
use App\Models\SectionTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Page $page */
        $page = $this->resource;
        $sections = $page->sections_json ?: $page->layout_json ?: [];
        $sections = $this->enrichSections($sections);
        $themeSlug = $page->site?->theme?->slug ?: 'porto-furniture';

        return [
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'excerpt' => null,
                'featured_image' => $page->getFirstMediaUrl('cover'),
                'template' => $page->template ?: $page->page_template,
                'layout' => $page->layout_json ?? [],
                'sections' => $sections,
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
                'slug' => $themeSlug,
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

    protected function enrichSections(array $sections): array
    {
        $templateIds = collect($sections)
            ->pluck('section_template_id')
            ->filter()
            ->values();

        if ($templateIds->isEmpty()) {
            return $sections;
        }

        $templates = SectionTemplate::query()
            ->whereIn('id', $templateIds)
            ->get()
            ->keyBy('id');

        return collect($sections)
            ->map(function (array $section) use ($templates) {
                $template = $templates->get($section['section_template_id'] ?? null);

                if (! $template) {
                    return $section;
                }

                $section['template_name'] = $template->name;
                $section['html_template'] = $template->html_template;
                $section['component_key'] = $template->component_key;
                $section['schema'] = $template->schema_json ?? [];

                return $section;
            })
            ->all();
    }
}
