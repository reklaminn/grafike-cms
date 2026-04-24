<?php

namespace Tests\Feature\Console;

use App\Models\Language;
use App\Models\Page;
use App\Models\SectionTemplate;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MigratePageLayoutsToSectionsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_migrates_layout_json_into_sections_json(): void
    {
        $language = Language::factory()->create();

        $theme = Theme::create([
            'name' => 'Porto',
            'slug' => 'porto',
            'is_active' => true,
        ]);

        SectionTemplate::create([
            'theme_id' => $theme->id,
            'name' => 'Content Block',
            'type' => 'content-block',
            'variation' => 'legacy-content-block',
            'render_mode' => 'component',
            'legacy_module_key' => '90',
            'schema_json' => [],
            'default_content_json' => [],
            'legacy_config_map_json' => [],
        ]);

        $page = Page::create([
            'title' => 'Ana Sayfa',
            'slug' => 'ana-sayfa',
            'language_id' => $language->id,
            'status' => 'published',
            'layout_json' => [
                [
                    'type' => 'body',
                    'children' => [[
                        [
                            'coltype' => 'col-12',
                            'children' => [[
                                ['modulid' => 90, 'json' => []],
                            ]],
                        ],
                    ]],
                ],
            ],
        ]);

        $this->artisan('cms:migrate-page-layouts', ['--theme' => $theme->slug])
            ->assertExitCode(0);

        $page->refresh();

        $this->assertSame(2, data_get($page->sections_json, 'version'));
        $this->assertSame('content-block', data_get($page->sections_json, 'regions.body.0.columns.0.blocks.0.type'));
    }
}
