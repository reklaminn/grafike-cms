<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ResolvesApiLanguage;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\MenuResource;
use App\Models\Menu;
use App\Models\Site;

class MenuController extends Controller
{
    use ResolvesApiLanguage;

    public function index()
    {
        $language = $this->resolveLanguage();
        $site = Site::resolve(request()->header('X-Site-Host'));

        $menus = Menu::query()
            ->when($site, fn ($query) => $query->where('site_id', $site->id))
            ->when($language, fn ($query) => $query->where('language_id', $language->id))
            ->where('is_active', true)
            ->with([
                'items' => fn ($query) => $query
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->with([
                        'children' => fn ($childQuery) => $childQuery
                            ->where('is_active', true)
                            ->orderBy('sort_order')
                            ->with('page'),
                        'page',
                    ]),
            ])
            ->orderBy('location')
            ->orderBy('name')
            ->get();

        if ($menus->isEmpty()) {
            $menus = Menu::query()
                ->whereNull('site_id')
                ->when($language, fn ($query) => $query->where('language_id', $language->id))
                ->where('is_active', true)
                ->with([
                    'items' => fn ($query) => $query
                        ->whereNull('parent_id')
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->with([
                            'children' => fn ($childQuery) => $childQuery
                                ->where('is_active', true)
                                ->orderBy('sort_order')
                                ->with('page'),
                            'page',
                        ]),
                ])
                ->orderBy('location')
                ->orderBy('name')
                ->get();
        }

        return MenuResource::collection($menus);
    }

    public function show(string $location)
    {
        $language = $this->resolveLanguage();
        $site = Site::resolve(request()->header('X-Site-Host'));

        $menu = Menu::query()
            ->when($site, fn ($query) => $query->where('site_id', $site->id))
            ->where('location', $location)
            ->when($language, fn ($query) => $query->where('language_id', $language->id))
            ->where('is_active', true)
            ->with([
                'items' => fn ($query) => $query
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->with([
                        'children' => fn ($childQuery) => $childQuery
                            ->where('is_active', true)
                            ->orderBy('sort_order')
                            ->with('page'),
                        'page',
                    ]),
            ])
            ->firstOr(function () use ($location, $language) {
                return Menu::query()
                    ->whereNull('site_id')
                    ->where('location', $location)
                    ->when($language, fn ($query) => $query->where('language_id', $language->id))
                    ->where('is_active', true)
                    ->with([
                        'items' => fn ($query) => $query
                            ->whereNull('parent_id')
                            ->where('is_active', true)
                            ->orderBy('sort_order')
                            ->with([
                                'children' => fn ($childQuery) => $childQuery
                                    ->where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->with('page'),
                                'page',
                            ]),
                    ])
                    ->firstOrFail();
            });

        return MenuResource::make($menu);
    }
}
