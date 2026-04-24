<?php

namespace App\Http\Resources\Api;

use App\Models\Page;
use App\Models\SectionTemplate;
use App\Support\FrontendSections;
use App\Support\LegacyLayoutToSections;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Page $page */
        $page = $this->resource;
        $rawSections = $this->resolveRenderableSections($page);
        $sections = $this->enrichSections(FrontendSections::flattenBlocks($rawSections));
        $regionLayout = $this->enrichRegionBlocks($rawSections);
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
                'region_version' => $regionLayout['version'] ?? 2,
                'regions' => $regionLayout['regions'] ?? [],
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

    protected function resolveRenderableSections(Page $page): array
    {
        $sections = $page->sections_json;

        if (! empty($sections)) {
            $flattened = FrontendSections::flattenBlocks($sections);

            if (! empty($flattened)) {
                return $sections;
            }
        }

        if (! empty($page->layout_json)) {
            return LegacyLayoutToSections::convert($page, $page->site?->theme);
        }

        return [];
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
        $templateIds = FrontendSections::collectTemplateIds($sections);

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

    protected function enrichRegionBlocks(array $sections): array
    {
        $templateIds = FrontendSections::collectTemplateIds($sections);

        if ($templateIds->isEmpty()) {
            return FrontendSections::normalize($sections);
        }

        $templates = SectionTemplate::query()
            ->whereIn('id', $templateIds)
            ->get()
            ->keyBy('id');

        return FrontendSections::mapBlocks($sections, function (array $block) use ($templates) {
            $template = $templates->get($block['section_template_id'] ?? null);

            if (! $template) {
                return $block;
            }

            $block['template_name'] = $template->name;
            $block['html_template'] = $template->html_template;
            $block['component_key'] = $template->component_key;
            $block['schema'] = $template->schema_json ?? [];

            return $block;
        });
    }
}
