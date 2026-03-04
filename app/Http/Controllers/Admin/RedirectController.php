<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Redirect;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function index(Request $request)
    {
        $query = Redirect::latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('from_url', 'like', "%{$search}%")
                    ->orWhere('to_url', 'like', "%{$search}%");
            });
        }

        $redirects = $query->paginate(20)->withQueryString();

        return view('admin.redirects.index', compact('redirects'));
    }

    public function create()
    {
        return view('admin.redirects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_url' => 'required|string|max:500|unique:redirects,from_url',
            'to_url' => 'required|string|max:500',
            'status_code' => 'required|in:301,302,307',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Redirect::create($validated);

        return redirect()->route('admin.redirects.index')
            ->with('success', 'Yönlendirme oluşturuldu.');
    }

    public function edit(Redirect $redirect)
    {
        return view('admin.redirects.edit', compact('redirect'));
    }

    public function update(Request $request, Redirect $redirect)
    {
        $validated = $request->validate([
            'from_url' => 'required|string|max:500|unique:redirects,from_url,' . $redirect->id,
            'to_url' => 'required|string|max:500',
            'status_code' => 'required|in:301,302,307',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $redirect->update($validated);

        return redirect()->route('admin.redirects.index')
            ->with('success', 'Yönlendirme güncellendi.');
    }

    public function destroy(Redirect $redirect)
    {
        $redirect->delete();

        return redirect()->route('admin.redirects.index')
            ->with('success', 'Yönlendirme silindi.');
    }

    public function resetHits(Redirect $redirect)
    {
        $redirect->update(['hit_count' => 0, 'last_hit_at' => null]);

        return back()->with('success', 'Hit sayacı sıfırlandı.');
    }

    public function showImport()
    {
        return view('admin.redirects.import');
    }

    public function processImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');

        $imported = 0;
        $skipped = 0;
        $lineNum = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $lineNum++;
            if ($lineNum === 1 && (stripos($row[0] ?? '', 'url') !== false || stripos($row[0] ?? '', 'source') !== false)) {
                continue; // Skip header row
            }

            $fromUrl = trim($row[0] ?? '');
            $toUrl = trim($row[1] ?? '');
            $statusCode = (int) trim($row[2] ?? '301');

            if (empty($fromUrl) || empty($toUrl)) {
                $skipped++;
                continue;
            }

            if (!in_array($statusCode, [301, 302, 307])) {
                $statusCode = 301;
            }

            // Skip if already exists
            if (Redirect::where('from_url', $fromUrl)->exists()) {
                $skipped++;
                continue;
            }

            Redirect::create([
                'from_url' => $fromUrl,
                'to_url' => $toUrl,
                'status_code' => $statusCode,
                'is_active' => true,
            ]);
            $imported++;
        }

        fclose($handle);

        return redirect()
            ->route('admin.redirects.index')
            ->with('success', "{$imported} yönlendirme içe aktarıldı. {$skipped} atlandı.");
    }
}
