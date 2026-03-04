<?php

namespace App\Services\SeoManager;

use App\Models\Redirect;
use App\Models\SeoEntry;
use Illuminate\Support\Facades\Cache;

class SeoManager
{
    public function resolve(string $slug, ?string $locale = null): ?array
    {
        $cacheKey = "seo_resolve_{$slug}_{$locale}";

        return Cache::remember($cacheKey, config('cms.cache.ttl', 600), function () use ($slug) {
            // Check for redirects first
            $redirect = Redirect::where('from_url', $slug)->first();
            if ($redirect) {
                $redirect->increment('hit_count');
                $redirect->update(['last_hit_at' => now()]);

                return [
                    'type' => 'redirect',
                    'url' => $redirect->to_url,
                    'status_code' => $redirect->status_code,
                ];
            }

            // Find SEO entry
            $seoEntry = SeoEntry::where('slug', $slug)->first();
            if (! $seoEntry) {
                return null;
            }

            $entity = $seoEntry->seoable;
            if (! $entity) {
                return null;
            }

            return [
                'type' => 'content',
                'entity_type' => $seoEntry->seoable_type,
                'entity' => $entity,
                'seo' => $seoEntry,
            ];
        });
    }

    public function generateSlug(string $title): string
    {
        $slug = mb_strtolower($title, 'UTF-8');

        $turkishMap = [
            'ç' => 'c', 'ğ' => 'g', 'ı' => 'i', 'ö' => 'o', 'ş' => 's', 'ü' => 'u',
            'Ç' => 'c', 'Ğ' => 'g', 'İ' => 'i', 'Ö' => 'o', 'Ş' => 's', 'Ü' => 'u',
        ];
        $slug = strtr($slug, $turkishMap);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);

        return trim($slug, '-');
    }

    public function clearCache(?string $slug = null): void
    {
        if ($slug) {
            Cache::forget("seo_resolve_{$slug}");
        }
    }
}
