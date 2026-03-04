<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoEntry;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function index(Request $request)
    {
        $query = SeoEntry::with('seoable')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('slug', 'like', "%{$search}%")
                    ->orWhere('meta_title', 'like', "%{$search}%");
            });
        }

        if ($request->input('noindex') === '1') {
            $query->where('is_noindex', true);
        }

        $seoEntries = $query->paginate(20)->withQueryString();

        return view('admin.seo.index', compact('seoEntries'));
    }

    public function edit(SeoEntry $seoEntry)
    {
        $seoEntry->load('seoable');

        return view('admin.seo.edit', compact('seoEntry'));
    }

    public function update(Request $request, SeoEntry $seoEntry)
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:seo_entries,slug,' . $seoEntry->id,
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'h1_override' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url|max:500',
            'is_noindex' => 'boolean',
            'page_css' => 'nullable|string|max:10000',
            'page_js' => 'nullable|string|max:10000',
        ]);

        $validated['is_noindex'] = $request->boolean('is_noindex');

        $seoEntry->update($validated);

        return redirect()->route('admin.seo.index')
            ->with('success', 'SEO kaydı güncellendi.');
    }

    public function destroy(SeoEntry $seoEntry)
    {
        $seoEntry->delete();

        return redirect()->route('admin.seo.index')
            ->with('success', 'SEO kaydı silindi.');
    }

    public function bulkAnalysis()
    {
        $entries = SeoEntry::with('seoable')->get();

        $issues = [];
        foreach ($entries as $entry) {
            $entryIssues = [];

            if (empty($entry->meta_title)) {
                $entryIssues[] = 'Meta title eksik';
            } elseif (mb_strlen($entry->meta_title) > 60) {
                $entryIssues[] = 'Meta title 60 karakterden uzun';
            }

            if (empty($entry->meta_description)) {
                $entryIssues[] = 'Meta description eksik';
            } elseif (mb_strlen($entry->meta_description) > 155) {
                $entryIssues[] = 'Meta description 155 karakterden uzun';
            }

            if (empty($entry->slug)) {
                $entryIssues[] = 'Slug eksik';
            }

            if (! empty($entryIssues)) {
                $issues[] = [
                    'entry' => $entry,
                    'issues' => $entryIssues,
                ];
            }
        }

        $stats = [
            'total' => $entries->count(),
            'noindex' => $entries->where('is_noindex', true)->count(),
            'missing_title' => $entries->where('meta_title', null)->count(),
            'missing_description' => $entries->where('meta_description', null)->count(),
            'with_issues' => count($issues),
        ];

        return view('admin.seo.analysis', compact('issues', 'stats'));
    }
}
