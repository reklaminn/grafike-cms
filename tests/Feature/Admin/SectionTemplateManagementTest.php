<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Language;
use App\Models\Menu;
use App\Models\Page;
use App\Models\SectionTemplate;
use App\Models\SiteSetting;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionTemplateManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
    }

    public function test_admin_can_view_section_templates_index(): void
    {
        $theme = Theme::create([
            'name' => 'Porto',
            'slug' => 'porto',
        ]);

        SectionTemplate::create([
            'theme_id' => $theme->id,
            'name' => 'Hero',
            'type' => 'hero',
            'variation' => 'porto-split',
            'render_mode' => 'html',
            'schema_json' => [],
            'default_content_json' => [],
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.section-templates.index'));

        $response->assertOk()
            ->assertSee('Hero')
            ->assertSee('Aktif');
    }

    public function test_index_can_filter_by_status_and_show_usage_count(): void
    {
        $theme = Theme::create([
            'name' => 'Porto',
            'slug' => 'porto',
        ]);

        $activeTemplate = SectionTemplate::create([
            'theme_id' => $theme->id,
            'name' => 'Hero',
            'type' => 'hero',
            'variation' => 'porto-split',
            'render_mode' => 'html',
            'is_active' => true,
        ]);

        SectionTemplate::create([
            'theme_id' => $theme->id,
            'name' => 'Old CTA',
            'type' => 'cta',
            'variation' => 'old',
            'render_mode' => 'html',
            'is_active' => false,
        ]);

        $language = Language::factory()->create([
            'is_active' => true,
        ]);

        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'language_id' => $language->id,
            'status' => 'published',
            'sections_json' => [
                'version' => 2,
                'regions' => [
                    'header' => [],
                    'body' => [[
                        'id' => 'row_1',
                        'columns' => [[
                            'id' => 'col_1',
                            'blocks' => [[
                                'id' => 'block_1',
                                'type' => 'hero',
                                'section_template_id' => $activeTemplate->id,
                            ]],
                        ]],
                    ]],
                    'footer' => [],
                ],
            ],
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.section-templates.index', ['status' => 'active']));

        $response->assertOk()
            ->assertSee('Hero')
            ->assertDontSee('Old CTA')
            ->assertSee('1 sayfada kullanılıyor')
            ->assertSee('Home');
    }

    public function test_index_can_filter_by_type_and_render_mode(): void
    {
        $theme = Theme::create([
            'name' => 'Porto',
            'slug' => 'porto',
        ]);

        SectionTemplate::create([
            'theme_id' => $theme->id,
            'name' => 'Hero Html',
            'type' => 'hero',
            'variation' => 'html',
            'render_mode' => 'html',
            'is_active' => true,
        ]);

        SectionTemplate::create([
            'theme_id' => $theme->id,
            'name' => 'Hero Component',
            'type' => 'hero',
            'variation' => 'component',
            'render_mode' => 'component',
            'is_active' => true,
        ]);

        SectionTemplate::create([
            'theme_id' => $theme->id,
            'name' => 'Footer Html',
            'type' => 'footer',
            'variation' => 'html',
            'render_mode' => 'html',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.section-templates.index', [
                'type' => 'hero',
                'render_mode' => 'component',
            ]));

        $response->assertOk()
            ->assertSee('Hero Component')
            ->assertDontSee('Hero Html')
            ->assertDontSee('Footer Html')
            ->assertSee('Tüm type');
    }

    public function test_admin_can_create_section_template(): void
    {
        $theme = Theme::create([
            'name' => 'Porto',
            'slug' => 'porto',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.section-templates.store'), [
                'theme_id' => $theme->id,
                'name' => 'Hero / Porto Split',
                'type' => 'hero',
                'variation' => 'porto-split',
                'render_mode' => 'html',
                'legacy_module_key' => 'PageHeader',
                'schema_json' => json_encode([
                    ['name' => 'title', 'type' => 'text'],
                ], JSON_THROW_ON_ERROR),
                'default_content_json' => json_encode([
                    'title' => 'Merhaba',
                ], JSON_THROW_ON_ERROR),
                'legacy_config_map_json' => json_encode([
                    'title' => 'title',
                ], JSON_THROW_ON_ERROR),
                'is_active' => '1',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('section_templates', [
            'name' => 'Hero / Porto Split',
            'legacy_module_key' => 'PageHeader',
        ]);
    }

    public function test_admin_can_create_template_with_custom_type_and_map_schema(): void
    {
        $theme = Theme::create([
            'name' => 'Porto',
            'slug' => 'porto',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.section-templates.store'), [
                'theme_id' => $theme->id,
                'name' => 'Pricing Cards',
                'type' => '__custom',
                'type_custom' => 'Pricing Cards',
                'variation' => 'Porto Cards',
                'render_mode' => 'html',
                'schema_json' => json_encode([
                    'title' => ['type' => 'text', 'label' => 'Title'],
                    'cards' => [
                        'type' => 'repeater',
                        'label' => 'Cards',
                        'fields' => [
                            'title' => ['type' => 'text', 'label' => 'Title'],
                        ],
                    ],
                ], JSON_THROW_ON_ERROR),
                'default_content_json' => json_encode([
                    'title' => 'Plans',
                    'cards' => [['title' => 'Basic']],
                ], JSON_THROW_ON_ERROR),
                'legacy_config_map_json' => json_encode([], JSON_THROW_ON_ERROR),
                'is_active' => '1',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('section_templates', [
            'name' => 'Pricing Cards',
            'type' => 'pricing-cards',
            'variation' => 'porto-cards',
        ]);
    }

    public function test_form_exposes_menu_and_system_placeholder_pickers(): void
    {
        $language = Language::factory()->create([
            'is_active' => true,
        ]);

        Menu::create([
            'name' => 'Header Menu',
            'slug' => 'header-menu',
            'location' => 'header',
            'language_id' => $language->id,
            'is_active' => true,
        ]);

        SiteSetting::create([
            'key' => 'contact.phone',
            'value' => '+90 212 000 0000',
            'group' => 'contact',
            'type' => 'text',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.section-templates.create'));

        $response->assertOk()
            ->assertSee('menu_header_html', false)
            ->assertSee('menu_header_items_html', false)
            ->assertSee('contact_phone', false)
            ->assertSee('Sistem alanı seç')
            ->assertSee('Repeat Alan Bul')
            ->assertSee('Manuel Repeat')
            ->assertSee('Örnek Doldur')
            ->assertSee('Yeni Öner')
            ->assertSee('+ Yeni type oluştur');
    }

    public function test_admin_can_update_section_template(): void
    {
        $theme = Theme::create([
            'name' => 'Porto',
            'slug' => 'porto',
        ]);

        $sectionTemplate = SectionTemplate::create([
            'theme_id' => $theme->id,
            'name' => 'Hero',
            'type' => 'hero',
            'variation' => 'porto-split',
            'render_mode' => 'html',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.section-templates.update', $sectionTemplate), [
                'theme_id' => $theme->id,
                'name' => 'Hero Güncel',
                'type' => 'hero',
                'variation' => 'porto-split',
                'render_mode' => 'component',
                'component_key' => 'hero/porto-split',
                'schema_json' => json_encode([], JSON_THROW_ON_ERROR),
                'default_content_json' => json_encode([], JSON_THROW_ON_ERROR),
                'legacy_config_map_json' => json_encode([], JSON_THROW_ON_ERROR),
                'is_active' => '1',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('section_templates', [
            'id' => $sectionTemplate->id,
            'name' => 'Hero Güncel',
            'render_mode' => 'component',
        ]);
    }

    public function test_edit_form_shows_pages_using_section_template(): void
    {
        $theme = Theme::create([
            'name' => 'Porto',
            'slug' => 'porto',
        ]);

        $language = Language::factory()->create([
            'is_active' => true,
        ]);

        $sectionTemplate = SectionTemplate::create([
            'theme_id' => $theme->id,
            'name' => 'Hero',
            'type' => 'hero',
            'variation' => 'porto-split',
            'render_mode' => 'html',
            'is_active' => true,
        ]);

        Page::create([
            'title' => 'Landing Page',
            'slug' => 'landing',
            'language_id' => $language->id,
            'status' => 'published',
            'sections_json' => [
                'version' => 2,
                'regions' => [
                    'header' => [],
                    'body' => [[
                        'id' => 'row_1',
                        'columns' => [[
                            'id' => 'col_1',
                            'blocks' => [[
                                'id' => 'block_1',
                                'type' => 'hero',
                                'section_template_id' => $sectionTemplate->id,
                            ]],
                        ]],
                    ]],
                    'footer' => [],
                ],
            ],
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.section-templates.edit', $sectionTemplate));

        $response->assertOk()
            ->assertSee('Bu Şablonu Kullanan Sayfalar')
            ->assertSee('Landing Page')
            ->assertSee('/landing');
    }

    public function test_admin_can_duplicate_section_template_with_unique_variation(): void
    {
        $theme = Theme::create([
            'name' => 'Porto',
            'slug' => 'porto',
        ]);

        $sectionTemplate = SectionTemplate::create([
            'theme_id' => $theme->id,
            'name' => 'Hero',
            'type' => 'hero',
            'variation' => 'porto-split',
            'render_mode' => 'html',
            'is_active' => true,
        ]);

        SectionTemplate::create([
            'theme_id' => $theme->id,
            'name' => 'Hero Existing Copy',
            'type' => 'hero',
            'variation' => 'porto-split-copy',
            'render_mode' => 'html',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.section-templates.duplicate', $sectionTemplate));

        $response->assertRedirect();

        $this->assertDatabaseHas('section_templates', [
            'name' => 'Hero (kopya)',
            'type' => 'hero',
            'variation' => 'porto-split-copy-2',
            'is_active' => false,
        ]);
    }

    public function test_invalid_json_and_schema_fields_are_rejected(): void
    {
        $theme = Theme::create([
            'name' => 'Porto',
            'slug' => 'porto',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.section-templates.create'))
            ->post(route('admin.section-templates.store'), [
                'theme_id' => $theme->id,
                'name' => 'Broken',
                'type' => 'hero',
                'variation' => 'broken',
                'render_mode' => 'html',
                'schema_json' => json_encode([
                    ['name' => 'title'],
                ], JSON_THROW_ON_ERROR),
                'default_content_json' => '{broken',
                'legacy_config_map_json' => json_encode([], JSON_THROW_ON_ERROR),
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.section-templates.create'));
        $response->assertSessionHasErrors(['schema_json', 'default_content_json']);
    }
}
