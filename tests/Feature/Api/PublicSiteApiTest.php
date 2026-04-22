<?php

namespace Tests\Feature\Api;

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
            ->assertJsonPath('data.theme.slug', 'porto-furniture');
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
}
