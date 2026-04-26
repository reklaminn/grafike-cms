<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        $entries = SeoEntry::with('seoable')
            ->orderBy('slug')
            ->paginate(50);

        return view('admin.sitemap.index', compact('entries'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'entries' => 'required|array',
            'entries.*.id' => 'required|exists:seo_entries,id',
            'entries.*.sitemap_priority' => 'required|numeric|min:0|max:1',
            'entries.*.sitemap_changefreq' => 'required|in:always,hourly,daily,weekly,monthly,yearly,never',
            'entries.*.sitemap_exclude' => 'nullable|boolean',
        ]);

        foreach ($request->entries as $entryData) {
            SeoEntry::where('id', $entryData['id'])->update([
                'sitemap_priority' => $entryData['sitemap_priority'],
                'sitemap_changefreq' => $entryData['sitemap_changefreq'],
                'sitemap_exclude' => $entryData['sitemap_exclude'] ?? false,
            ]);
        }

        return redirect()
            ->route('admin.sitemap.index')
            ->with('success', 'Sitemap ayarları güncellendi.');
    }

    public function refresh()
    {
        Cache::forget('sitemap_xml');

        return redirect()
            ->route('admin.sitemap.index')
            ->with('success', 'Sitemap önbelleği temizlendi. Bir sonraki ziyarette yeniden oluşturulacak.');
    }
}
