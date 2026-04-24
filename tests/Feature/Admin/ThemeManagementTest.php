<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
    }

    public function test_admin_can_view_themes_index(): void
    {
        Theme::create([
            'name' => 'Porto',
            'slug' => 'porto',
            'engine' => 'nextjs-basic-html',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.themes.index'));

        $response->assertOk()->assertSee('Porto');
    }

    public function test_admin_can_create_theme(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.themes.store'), [
                'name' => 'Woodmart Fashion',
                'slug' => 'woodmart-fashion',
                'engine' => 'nextjs-basic-html',
                'description' => 'Shop teması',
                'css_paths' => "/themes/woodmart/theme.css\n/themes/woodmart/custom.css",
                'js_paths' => "/themes/woodmart/theme.js",
                'assets_json' => json_encode(['css' => [], 'js' => []], JSON_THROW_ON_ERROR),
                'tokens_json' => json_encode(['color_primary' => '#111111'], JSON_THROW_ON_ERROR),
                'settings_schema_json' => json_encode(['header_variant' => ['type' => 'select']], JSON_THROW_ON_ERROR),
                'is_active' => '1',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('themes', [
            'name' => 'Woodmart Fashion',
            'slug' => 'woodmart-fashion',
            'engine' => 'nextjs-basic-html',
        ]);

        $theme = Theme::where('slug', 'woodmart-fashion')->firstOrFail();
        $this->assertSame(['/themes/woodmart/theme.css', '/themes/woodmart/custom.css'], data_get($theme->assets_json, 'css'));
        $this->assertSame(['/themes/woodmart/theme.js'], data_get($theme->assets_json, 'js'));
    }

    public function test_admin_can_update_theme(): void
    {
        $theme = Theme::create([
            'name' => 'Porto',
            'slug' => 'porto',
            'engine' => 'nextjs-basic-html',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.themes.update', $theme), [
                'name' => 'Porto Corporate',
                'slug' => 'porto-corporate',
                'engine' => 'nextjs-component',
                'description' => 'Kurumsal tema',
                'assets_json' => json_encode(['css' => [], 'js' => []], JSON_THROW_ON_ERROR),
                'tokens_json' => json_encode([], JSON_THROW_ON_ERROR),
                'settings_schema_json' => json_encode([], JSON_THROW_ON_ERROR),
                'is_active' => '1',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('themes', [
            'id' => $theme->id,
            'name' => 'Porto Corporate',
            'slug' => 'porto-corporate',
            'engine' => 'nextjs-component',
        ]);
    }
}
