<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ResolvesApiLanguage;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SiteResource;
use App\Models\Language;
use App\Models\Site;
use App\Models\SiteSetting;

class SiteController extends Controller
{
    use ResolvesApiLanguage;

    public function index()
    {
        $language = $this->resolveLanguage();
        $site = Site::resolve(request()->header('X-Site-Host'));
        $siteId = $site?->id;
        $theme = $site?->theme;
        $tokens = array_merge($theme?->tokens_json ?? [], $site?->tokens_json ?? []);
        $siteSettings = $site?->settings_json ?? [];

        return SiteResource::make([
            'name' => $site?->name ?? SiteSetting::get('site.title', config('cms.name', 'Grafike CMS')),
            'domain' => request()->getHost(),
            'theme' => [
                'slug' => $theme?->slug ?? 'porto-furniture',
                'engine' => $theme?->engine ?? 'next',
                'assets' => [
                    'css' => data_get($theme?->assets_json, 'css', []),
                    'js' => data_get($theme?->assets_json, 'js', []),
                ],
            ],
            'tokens' => [
                'color_primary' => $tokens['color_primary'] ?? SiteSetting::get('design.color_primary', '#7c5a3a', $siteId),
                'color_secondary' => $tokens['color_secondary'] ?? SiteSetting::get('design.color_secondary', '#f3ede6', $siteId),
                'color_accent' => $tokens['color_accent'] ?? SiteSetting::get('design.color_accent', '#111827', $siteId),
                'radius_card' => $tokens['radius_card'] ?? SiteSetting::get('design.radius_card', '20px', $siteId),
                'radius_button' => $tokens['radius_button'] ?? SiteSetting::get('design.radius_button', '999px', $siteId),
                'container_width' => $tokens['container_width'] ?? SiteSetting::get('design.container_width', '1320px', $siteId),
            ],
            'header_variant' => $siteSettings['header_variant'] ?? SiteSetting::get('theme.header_variant', 'default-header', $siteId),
            'footer_variant' => $siteSettings['footer_variant'] ?? SiteSetting::get('theme.footer_variant', 'default-footer', $siteId),
            'locale' => $language?->code ?? app()->getLocale(),
            'available_locales' => Language::active()->get(['code', 'locale', 'name']),
        ]);
    }
}
