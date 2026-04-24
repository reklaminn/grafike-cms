<?php

namespace App\Support;

use App\Models\Page;
use Illuminate\Support\Collection;

final class PageEditorData
{
    public function __construct(
        public readonly ?Page $page,
        public readonly Collection $availableTemplates,
    ) {}

    public static function for(?Page $page, Collection $availableTemplates): self
    {
        return new self($page, $availableTemplates);
    }

    public function initialRegions(): array
    {
        $oldSectionsJson = old('sections_json');

        if ($oldSectionsJson) {
            $decoded = json_decode((string) $oldSectionsJson, true);

            if (is_array($decoded)) {
                return FrontendSections::normalize($decoded);
            }
        }

        return FrontendSections::normalize($this->page?->sections_json ?? []);
    }

    public function availableTemplatesPayload(): array
    {
        return $this->availableTemplates
            ->map(fn ($template) => [
                'id' => $template->id,
                'name' => $template->name,
                'type' => $template->type,
                'variation' => $template->variation,
                'render_mode' => $template->render_mode,
                'component_key' => $template->component_key,
                'html_template' => $template->html_template,
                'schema' => $template->schema_json ?? [],
                'default_content' => $template->default_content_json ?? [],
            ])
            ->values()
            ->all();
    }

    public function frontendSectionEditorPayload(): array
    {
        return [
            'initialRegions' => $this->initialRegions(),
            'availableTemplates' => $this->availableTemplatesPayload(),
        ];
    }

    public function hasSectionsJson(): bool
    {
        return ! empty($this->page?->sections_json);
    }

    public function hasLayoutJson(): bool
    {
        return ! empty($this->page?->layout_json);
    }

    public function activeBuilder(): string
    {
        if (old('sections_json')) {
            return 'frontend';
        }

        if (old('layout_json')) {
            return 'legacy';
        }

        if ($this->hasSectionsJson()) {
            return 'frontend';
        }

        if ($this->hasLayoutJson()) {
            return 'legacy';
        }

        return $this->page?->site ? 'frontend' : 'legacy';
    }

    public function showBuilderToggle(): bool
    {
        if ($this->page === null) {
            return false;
        }

        if ($this->hasLayoutJson() && ! $this->hasSectionsJson()) {
            return true;
        }

        if ($this->hasLayoutJson() && $this->hasSectionsJson()) {
            return true;
        }

        if (! $this->page->site) {
            return true;
        }

        return false;
    }

    public function shouldRenderFrontendEditor(): bool
    {
        return $this->page !== null && ($this->page->site !== null || ! empty($this->initialRegions()['regions']['body'] ?? []));
    }
}
