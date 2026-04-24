<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ThemeRequest;
use App\Models\Theme;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function index(Request $request)
    {
        $query = Theme::query();

        if ($request->filled('q')) {
            $search = trim((string) $request->string('q'));
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('engine', 'like', "%{$search}%");
            });
        }

        $themes = $query
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(18)
            ->withQueryString();

        return view('admin.themes.index', compact('themes'));
    }

    public function create()
    {
        return view('admin.themes.create', [
            'theme' => new Theme([
                'engine' => 'nextjs-basic-html',
                'is_active' => true,
                'assets_json' => ['css' => [], 'js' => []],
                'tokens_json' => [],
                'settings_schema_json' => [],
            ]),
        ]);
    }

    public function store(ThemeRequest $request)
    {
        $theme = Theme::create($request->validated());

        return redirect()
            ->route('admin.themes.edit', $theme)
            ->with('success', 'Tema oluşturuldu.');
    }

    public function edit(Theme $theme)
    {
        return view('admin.themes.edit', compact('theme'));
    }

    public function update(ThemeRequest $request, Theme $theme)
    {
        $theme->update($request->validated());

        return redirect()
            ->route('admin.themes.edit', $theme)
            ->with('success', 'Tema güncellendi.');
    }

    public function destroy(Theme $theme)
    {
        $theme->delete();

        return redirect()
            ->route('admin.themes.index')
            ->with('success', 'Tema silindi.');
    }
}
