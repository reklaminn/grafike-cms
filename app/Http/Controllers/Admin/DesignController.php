<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesignAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DesignController extends Controller
{
    public function index()
    {
        $globalCss = DesignAsset::firstOrCreate(
            ['type' => 'css', 'name' => 'global'],
            ['content' => '/* Global CSS */']
        );

        $globalJs = DesignAsset::firstOrCreate(
            ['type' => 'js', 'name' => 'global'],
            ['content' => '// Global JavaScript']
        );

        $headerScripts = DesignAsset::firstOrCreate(
            ['type' => 'header_scripts', 'name' => 'header'],
            ['content' => '']
        );

        $footerScripts = DesignAsset::firstOrCreate(
            ['type' => 'footer_scripts', 'name' => 'footer'],
            ['content' => '']
        );

        return view('admin.design.index', compact('globalCss', 'globalJs', 'headerScripts', 'footerScripts'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'assets' => 'required|array',
            'assets.*.id' => 'required|exists:design_assets,id',
            'assets.*.content' => 'nullable|string|max:500000',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->assets as $assetData) {
                $asset = DesignAsset::find($assetData['id']);

                // Create backup before update (keep last 5)
                DB::table('design_asset_backups')->insert([
                    'design_asset_id' => $asset->id,
                    'content' => $asset->content ?? '',
                    'created_at' => now(),
                ]);

                // Clean old backups (keep last 5)
                $oldBackups = DB::table('design_asset_backups')
                    ->where('design_asset_id', $asset->id)
                    ->orderByDesc('id')
                    ->skip(5)
                    ->pluck('id');

                if ($oldBackups->isNotEmpty()) {
                    DB::table('design_asset_backups')->whereIn('id', $oldBackups)->delete();
                }

                $asset->update(['content' => $assetData['content'] ?? '']);
            }
        });

        return redirect()
            ->route('admin.design.index')
            ->with('success', 'Tasarım dosyaları başarıyla kaydedildi.');
    }
}
