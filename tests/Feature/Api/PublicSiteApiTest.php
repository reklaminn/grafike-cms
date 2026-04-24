<?php

namespace Tests\Feature\Api;

use App\Models\Language;
use App\Models\Page;
use Database\Seeders\FrontendTemplateDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(FrontendTemplateDemoSeeder::class);
    }

    public function test_site_endpoint_returns_site_specific_theme_and_tokens(): void
    {
        $response = $this
            ->withServerVariables(['HTTP_HOST' => 'demo.grafike.test'])
            ->getJson('/api/v1/site');

        $response
            ->assertOk()
            ->assertJsonPath('data.site.name', 'Grafike Next Demo')
            ->assertJsonPath('data.site.theme.slug', 'porto-furniture')
            ->assertJsonPath('data.site.theme.assets.css.0', '/themes/porto-furniture/vendor.css')
            ->assertJsonPath('data.site.theme.assets.js.0', '/themes/porto-furniture/theme.js')
            ->assertJsonPath('data.site.tokens.color_primary', '#8b5e3c')
            ->assertJsonPath('data.site.header_variant', 'porto-furniture-header');
    }

    public function test_home_page_endpoint_returns_seeded_sections_for_resolved_site(): void
    {
        $response = $this
            ->withServerVariables(['HTTP_HOST' => 'demo.grafike.test'])
            ->getJson('/api/v1/pages/home');

        $response
            ->assertOk()
            ->assertJsonPath('data.page.template', 'porto-furniture-home')
            ->assertJsonPath('data.page.sections.0.type', 'hero')
            ->assertJsonPath('data.page.sections.0.render_mode', 'html')
            ->assertJsonPath('data.page.region_version', 2)
            ->assertJsonPath('data.page.regions.body.0.columns.0.blocks.0.type', 'hero')
            ->assertJsonPath('data.page.regions.body.0.columns.0.blocks.0.render_mode', 'html')
            ->assertJsonPath('data.page.sections.0.template_name', 'Hero / Porto Split')
            ->assertJsonPath('data.theme.slug', 'porto-furniture');

        $template = data_get($response->json(), 'data.page.sections.0.html_template');

        $this->assertIsString($template);
        $this->assertStringContainsString('{{title}}', $template);
        $this->assertStringContainsString('{{button_text}}', $template);
    }

    public function test_header_menu_endpoint_returns_site_specific_menu_items(): void
    {
        $response = $this
            ->withServerVariables(['HTTP_HOST' => 'demo.grafike.test'])
            ->getJson('/api/v1/menus/header');

        $response
            ->assertOk()
            ->assertJsonPath('data.location', 'header')
            ->assertJsonPath('data.items.0.title', 'Ana Sayfa')
            ->assertJsonPath('data.items.1.title', 'Blog');
    }

    public function test_settings_endpoint_returns_site_specific_contact_information(): void
    {
        $response = $this
            ->withServerVariables(['HTTP_HOST' => 'demo.grafike.test'])
            ->getJson('/api/v1/settings');

        $response
            ->assertOk()
            ->assertJsonPath('settings.site_title', 'Grafike Next Demo')
            ->assertJsonPath('settings.contact.email', 'hello@grafike.test')
            ->assertJsonPath('settings.social.instagram', 'https://instagram.com/grafike');
    }

    public function test_page_endpoint_can_fallback_to_page_slug_without_seo_entry(): void
    {
        $language = Language::factory()->create([
            'is_active' => true,
        ]);

        $page = Page::factory()->create([
            'site_id' => 1,
            'language_id' => $language->id,
            'title' => 'Legacy Page',
            'slug' => 'legacy-page',
            'status' => 'published',
            'sections_json' => [
                'version' => 2,
                'regions' => [
                    'header' => [],
                    'body' => [],
                    'footer' => [],
                ],
            ],
        ]);

        $response = $this
            ->withServerVariables(['HTTP_HOST' => 'demo.grafike.test'])
            ->getJson('/api/v1/pages/'.$page->slug);

        $response
            ->assertOk()
            ->assertJsonPath('data.page.slug', 'legacy-page')
            ->assertJsonPath('data.page.id', $page->id);
    }
}
