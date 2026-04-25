<?php

namespace App\Services\SectionTemplate;

use App\Models\Menu;
use App\Models\SectionTemplate;
use App\Models\SiteSetting;

class SectionTemplateRenderer
{
    private array $systemTokens = [];

    public function render(SectionTemplate $template, ?array $overrideContent = null): string
    {
        $html = $template->html_template ?? '';
        if ($html === '') {
            return '';
        }

        $content = array_merge(
            $template->default_content_json ?? [],
            $overrideContent ?? []
        );

        $system = $this->getSystemTokens();
        $menus  = $this->buildMenuTokens();

        // {{{key}}} — raw (menu tokens, html fields)
        $html = preg_replace_callback('/\{\{\{([a-z0-9_]+)\}\}\}/', function ($m) use ($content, $system, $menus) {
            $key = $m[1];
            return $menus[$key] ?? $content[$key] ?? $system[$key] ?? '';
        }, $html);

        // {{key}} — escaped
        $html = preg_replace_callback('/\{\{([a-z0-9_]+)\}\}/', function ($m) use ($content, $system) {
            $key = $m[1];
            $val = $content[$key] ?? $system[$key] ?? '';
            return htmlspecialchars((string) $val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }, $html);

        return $html;
    }

    private function getSystemTokens(): array
    {
        if ($this->systemTokens) {
            return $this->systemTokens;
        }

        $settings = SiteSetting::query()
            ->get(['key', 'value'])
            ->pluck('value', 'key')
            ->all();

        $this->systemTokens = array_merge([
            'site_name'      => config('app.name'),
            'site_domain'    => request()->getHost(),
            'theme_slug'     => '',
            'logo_url'       => '',
            'favicon_url'    => '',
            'phone'          => '',
            'email'          => '',
            'address'        => '',
            'whatsapp_number'=> '',
            'working_hours'  => '',
            'tax_id'         => '',
            'footer_text'    => '',
        ], $settings);

        return $this->systemTokens;
    }

    private function buildMenuTokens(): array
    {
        $tokens = [];

        Menu::query()
            ->where('is_active', true)
            ->with('items')
            ->get(['id', 'name', 'slug', 'location'])
            ->each(function (Menu $menu) use (&$tokens) {
                $keys = collect([$menu->location, $menu->slug])
                    ->filter()
                    ->unique()
                    ->map(fn (string $k) => $this->normalizeKey($k));

                $itemsHtml = $this->renderMenuItems($menu);
                $menuHtml  = "<nav><ul>{$itemsHtml}</ul></nav>";

                foreach ($keys as $key) {
                    $tokens["menu_{$key}_html"]       = $menuHtml;
                    $tokens["menu_{$key}_items_html"] = $itemsHtml;
                }
            });

        return $tokens;
    }

    private function renderMenuItems(Menu $menu): string
    {
        return collect($menu->items ?? [])
            ->map(fn ($item) => sprintf(
                '<li><a href="%s">%s</a></li>',
                htmlspecialchars((string) ($item->url ?? '#'), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string) ($item->label ?? ''), ENT_QUOTES, 'UTF-8')
            ))
            ->implode('');
    }

    private function normalizeKey(string $key): string
    {
        return trim((string) str($key)->lower()->replaceMatches('/[^a-z0-9_]+/', '_'), '_');
    }
}
