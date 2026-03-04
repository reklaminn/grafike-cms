<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;
use App\Models\SiteSetting;

class LogoMenuModule extends BaseModule
{
    public function getName(): string
    {
        return 'Logo Menü';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.logo-menu';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        return [
            'siteName' => SiteSetting::get('site.title', config('cms.name', 'IRASPA')),
            'logoUrl' => SiteSetting::get('design.logo_url', ''),
            'logoCss' => SiteSetting::get('design.logo_css', ''),
            'showMobileToggle' => $config['mobile'] ?? true,
            'homeUrl' => '/',
        ];
    }
}
