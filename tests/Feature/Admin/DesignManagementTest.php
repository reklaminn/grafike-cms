<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\DesignAsset;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
    }

    public function test_admin_can_view_design_page_with_theme_assets(): void
    {
        Theme::create([
            'name' => 'Porto Furniture',
            'slug' => 'porto-furniture',
            'engine' => 'nextjs-basic-html',
            'assets_json' => [
                'css' => ['/themes/porto-furniture/theme.css'],
                'js' => ['/themes/porto-furniture/theme.js'],
            ],
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.design.index'));

        $response->assertOk()->assertSee('Tema Assetleri')->assertSee('Porto Furniture');
    }

    public function test_admin_can_update_theme_asset_paths_and_global_assets(): void
    {
        $globalCss = DesignAsset::create(['type' => 'css', 'name' => 'global', 'content' => '/* old */']);
        $globalJs = DesignAsset::create(['type' => 'js', 'name' => 'global', 'content' => '// old']);
        $headerScripts = DesignAsset::create(['type' => 'header_scripts', 'name' => 'header', 'content' => '']);
        $footerScripts = DesignAsset::create(['type' => 'footer_scripts', 'name' => 'footer', 'content' => '']);

        $theme = Theme::create([
            'name' => 'Porto Furniture',
            'slug' => 'porto-furniture',
            'engine' => 'nextjs-basic-html',
            'assets_json' => [],
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.design.update'), [
                'assets' => [
                    ['id' => $globalCss->id, 'content' => '/* new css */'],
                    ['id' => $globalJs->id, 'content' => '// new js'],
                    ['id' => $headerScripts->id, 'content' => '<meta name="x-test" content="1">'],
                    ['id' => $footerScripts->id, 'content' => '<script>console.log(1)</script>'],
                ],
                'theme_assets' => [
                    [
                        'id' => $theme->id,
                        'css_paths' => "/themes/porto-furniture/vendor.css\n/themes/porto-furniture/theme.css",
                        'js_paths' => "/themes/porto-furniture/theme.js\n/themes/porto-furniture/extra.js",
                    ],
                ],
            ]);

        $response->assertRedirect(route('admin.design.index'));

        $this->assertDatabaseHas('design_assets', [
            'id' => $globalCss->id,
            'content' => '/* new css */',
        ]);

        $theme->refresh();

        $this->assertSame(
            ['/themes/porto-furniture/vendor.css', '/themes/porto-furniture/theme.css'],
            data_get($theme->assets_json, 'css')
        );

        $this->assertSame(
            ['/themes/porto-furniture/theme.js', '/themes/porto-furniture/extra.js'],
            data_get($theme->assets_json, 'js')
        );
    }
}
