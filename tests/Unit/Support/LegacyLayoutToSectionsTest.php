<?php

namespace Tests\Unit\Support;

use App\Models\Page;
use App\Models\SectionTemplate;
use App\Models\Theme;
use App\Support\LegacyLayoutToSections;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegacyLayoutToSectionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_converts_legacy_layout_into_regions_v2(): void
    {
        $theme = Theme::create([
            'name' => 'Porto',
            'slug' => 'porto',
            'is_active' => true,
        ]);

        $template = SectionTemplate::create([
            'theme_id' => $theme->id,
            'name' => 'Hero Legacy',
            'type' => 'hero',
            'variation' => 'legacy-hero',
            'render_mode' => 'html',
            'legacy_module_key' => '1501',
            'schema_json' => [
                'title' => ['type' => 'text'],
                'align' => ['type' => 'select', 'options' => ['left', 'center', 'right']],
            ],
            'default_content_json' => [
                'title' => 'Varsayılan',
                'align' => 'left',
            ],
            'legacy_config_map_json' => [
                'title' => 'title',
                'align' => 'align',
            ],
        ]);

        $page = new Page([
            'title' => 'Legacy Page',
            'layout_json' => [
                [
                    'type' => 'body',
                    'cont' => 'container',
                    'elcss' => 'py-8',
                    'children' => [[
                        [
                            'coltype' => 'col-6',
                            'colmdtype' => 'col-md-4',
                            'celcss' => 'bg-white',
                            'children' => [[
                                [
                                    'modulid' => 1501,
                                    'json' => [
                                        ['name' => 'title', 'value' => 'Hero Baslik'],
                                        ['name' => 'align', 'value' => 'center'],
                                    ],
                                ],
                            ]],
                        ],
                    ]],
                ],
            ],
        ]);

        $sections = LegacyLayoutToSections::convert($page, $theme);

        $this->assertSame(2, $sections['version']);
        $this->assertSame('py-8', $sections['regions']['body'][0]['css_class']);
        $this->assertSame(6, $sections['regions']['body'][0]['columns'][0]['width']);
        $this->assertSame(4, $sections['regions']['body'][0]['columns'][0]['responsive']['md']);
        $this->assertSame('bg-white', $sections['regions']['body'][0]['columns'][0]['css_class']);
        $this->assertSame($template->id, $sections['regions']['body'][0]['columns'][0]['blocks'][0]['section_template_id']);
        $this->assertSame('Hero Baslik', $sections['regions']['body'][0]['columns'][0]['blocks'][0]['content']['title']);
        $this->assertSame('center', $sections['regions']['body'][0]['columns'][0]['blocks'][0]['content']['align']);
    }

    public function test_it_creates_placeholder_for_unmapped_legacy_module(): void
    {
        $theme = Theme::create([
            'name' => 'Porto',
            'slug' => 'porto',
            'is_active' => true,
        ]);

        $page = new Page([
            'title' => 'Legacy Page',
            'layout_json' => [
                [
                    'type' => 'body',
                    'children' => [[
                        [
                            'coltype' => 'col-12',
                            'children' => [[
                                ['modulid' => 999999, 'json' => []],
                            ]],
                        ],
                    ]],
                ],
            ],
        ]);

        $sections = LegacyLayoutToSections::convert($page, $theme);
        $block = $sections['regions']['body'][0]['columns'][0]['blocks'][0];

        $this->assertSame('legacy-module', $block['type']);
        $this->assertStringContainsString('Legacy modül eşleşmedi', (string) $block['html_override']);
    }

    public function test_it_can_use_inactive_legacy_mapping_templates(): void
    {
        $theme = Theme::create([
            'name' => 'Porto',
            'slug' => 'porto',
            'is_active' => true,
        ]);

        SectionTemplate::create([
            'theme_id' => $theme->id,
            'name' => 'Legacy Hero',
            'type' => 'hero-banner',
            'variation' => 'legacy-hero-banner',
            'render_mode' => 'component',
            'legacy_module_key' => '1501',
            'schema_json' => [],
            'default_content_json' => [],
            'legacy_config_map_json' => [],
            'is_active' => false,
        ]);

        $page = new Page([
            'title' => 'Legacy Page',
            'layout_json' => [
                [
                    'type' => 'body',
                    'children' => [[
                        [
                            'coltype' => 'col-12',
                            'children' => [[
                                ['modulid' => 1501, 'json' => []],
                            ]],
                        ],
                    ]],
                ],
            ],
        ]);

        $sections = LegacyLayoutToSections::convert($page, $theme);

        $this->assertSame('hero-banner', data_get($sections, 'regions.body.0.columns.0.blocks.0.type'));
        $this->assertSame('legacy-hero-banner', data_get($sections, 'regions.body.0.columns.0.blocks.0.variation'));
    }
}
