<?php

namespace App\View\Composers;

use App\Models\Language;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class FrontendComposer
{
    public function compose(View $view): void
    {
        // Active languages (cached)
        $languages = Cache::remember('active_languages', 3600, function () {
            return Language::active()->orderBy('sort_order')->get();
        });

        // Current language
        $currentLanguage = $languages->firstWhere('code', app()->getLocale())
            ?? $languages->first();

        // Site settings (cached per key in SiteSetting::get)
        $siteSettings = [
            'site_name' => SiteSetting::get('site.title', config('cms.name', 'IRASPA CMS')),
            'site_logo' => SiteSetting::get('design.logo_url', ''),
            'site_favicon' => SiteSetting::get('design.favicon_url', ''),
            'footer_text' => SiteSetting::get('site.footer_text', ''),
            'company_name' => SiteSetting::get('site.company_name', ''),
            'phone' => SiteSetting::get('contact.phone', ''),
            'email' => SiteSetting::get('contact.email', ''),
            'address' => SiteSetting::get('contact.address', ''),
        ];

        $view->with([
            'languages' => $languages,
            'currentLanguage' => $currentLanguage,
            'siteSettings' => $siteSettings,
        ]);
    }
}
