<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\SeoEntry;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        $xml = Cache::remember('sitemap_xml', config('cms.cache.ttl', 600), function () {
            return $this->generateSitemap();
        });

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    protected function generateSitemap(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $xml .= ' xmlns:xhtml="http://www.w3.org/1999/xhtml">';

        // Homepage
        $xml .= $this->urlEntry(url('/'), now()->toW3cString(), '1.0', 'daily');

        // Get all published SEO entries that are indexable
        $seoEntries = SeoEntry::where('is_noindex', false)
            ->whereHasMorph('seoable', [Page::class], function ($query) {
                $query->where('status', 'published');
            })
            ->orWhereHasMorph('seoable', ['App\Models\Article'], function ($query) {
                $query->where('status', 'published');
            })
            ->with('seoable')
            ->get();

        foreach ($seoEntries as $seo) {
            $entity = $seo->seoable;
            if (! $entity) {
                continue;
            }

            $url = url($seo->slug);
            $lastmod = $entity->updated_at?->toW3cString();
            $priority = $entity instanceof Page ? '0.8' : '0.6';
            $changefreq = $entity instanceof Page ? 'weekly' : 'monthly';

            $xml .= $this->urlEntry($url, $lastmod, $priority, $changefreq);

            // Add hreflang if available
            if ($seo->hreflang_tags && is_array($seo->hreflang_tags)) {
                foreach ($seo->hreflang_tags as $lang => $hrefUrl) {
                    $xml .= '  <xhtml:link rel="alternate" hreflang="' . e($lang) . '" href="' . e($hrefUrl) . '"/>';
                }
            }
        }

        $xml .= '</urlset>';

        return $xml;
    }

    protected function urlEntry(string $loc, ?string $lastmod, string $priority, string $changefreq): string
    {
        $xml = '<url>';
        $xml .= '<loc>' . e($loc) . '</loc>';
        if ($lastmod) {
            $xml .= '<lastmod>' . $lastmod . '</lastmod>';
        }
        $xml .= '<changefreq>' . $changefreq . '</changefreq>';
        $xml .= '<priority>' . $priority . '</priority>';
        $xml .= '</url>';

        return $xml;
    }
}
