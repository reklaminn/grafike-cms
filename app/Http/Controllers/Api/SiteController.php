<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ResolvesApiLanguage;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SiteResource;
use App\Models\Language;
use App\Models\SiteSetting;

class SiteController extends Controller
{
    use ResolvesApiLanguage;

    public function index()
    {
        $language = $this->resolveLanguage();

        return SiteResource::make([
            'name' => SiteSetting::get('site.title', config('cms.name', 'Grafike CMS')),
            'domain' => request()->getHost(),
            'theme' => [
                'slug' => 'porto-furniture',
                'engine' => 'next',
            ],
            'tokens' => [
                'color_primary' => SiteSetting::get('design.color_primary', '#7c5a3a'),
                'color_secondary' => SiteSetting::get('design.color_secondary', '#f3ede6'),
                'color_accent' => SiteSetting::get('design.color_accent', '#111827'),
                'radius_card' => SiteSetting::get('design.radius_card', '20px'),
                'radius_button' => SiteSetting::get('design.radius_button', '999px'),
                'container_width' => SiteSetting::get('design.container_width', '1320px'),
            ],
            'header_variant' => SiteSetting::get('theme.header_variant', 'default-header'),
            'footer_variant' => SiteSetting::get('theme.footer_variant', 'default-footer'),
            'locale' => $language?->code ?? app()->getLocale(),
            'available_locales' => Language::active()->get(['code', 'locale', 'name']),
        ]);
    }
}
