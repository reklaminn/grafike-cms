<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;

class MapModule extends BaseModule
{
    public function getName(): string
    {
        return 'Harita';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.map';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        $markers = [];

        // Load articles with coordinate data for map markers
        $targetPageId = $config['sayfa'] ?? $page->id;
        $articles = Article::where('page_id', $targetPageId)
            ->where('status', 'published')
            ->get();

        foreach ($articles as $art) {
            // Extract coordinates from extra_info or config
            $coords = $this->extractCoordinates($art);
            if ($coords) {
                $markers[] = [
                    'lat' => $coords['lat'],
                    'lng' => $coords['lng'],
                    'title' => $art->title,
                    'body' => \Illuminate\Support\Str::limit(strip_tags($art->body ?? ''), 200),
                    'url' => '/' . $art->slug,
                ];
            }
        }

        // Default coordinates (Turkey center) if no markers
        $defaultLat = $config['lat'] ?? 38.4237;
        $defaultLng = $config['lng'] ?? 27.1428;

        return [
            'markers' => $markers,
            'defaultLat' => $defaultLat,
            'defaultLng' => $defaultLng,
            'zoom' => $config['zoom'] ?? 12,
            'height' => $config['height'] ?? '450px',
            'title' => $config['baslik'] ?? '',
            'apiKey' => config('cms.google_maps_key', ''),
        ];
    }

    protected function extractCoordinates(Article $article): ?array
    {
        $extraInfo = $article->extra_info ?? '';
        // Try to parse "lat,lng" format
        if (preg_match('/(-?\d+\.?\d*)\s*,\s*(-?\d+\.?\d*)/', $extraInfo, $matches)) {
            return ['lat' => (float) $matches[1], 'lng' => (float) $matches[2]];
        }
        return null;
    }
}
