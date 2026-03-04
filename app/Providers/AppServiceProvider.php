<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Menu;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Observers\ArticleObserver;
use App\Observers\MenuObserver;
use App\Observers\PageObserver;
use App\Observers\SiteSettingObserver;
use App\View\Composers\FrontendComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers for cache invalidation
        Page::observe(PageObserver::class);
        Article::observe(ArticleObserver::class);
        SiteSetting::observe(SiteSettingObserver::class);
        Menu::observe(MenuObserver::class);

        // Register view composer for frontend layouts
        View::composer('frontend.layouts.*', FrontendComposer::class);
    }
}
