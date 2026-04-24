<?php

namespace Tests\Feature\Console;

use App\Models\SectionTemplate;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncLegacyModulesToSectionTemplatesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_syncs_legacy_modules_into_section_templates(): void
    {
        $theme = Theme::create([
            'name' => 'Porto',
            'slug' => 'porto',
            'is_active' => true,
        ]);

        config()->set('modules', [
            137 => [
                'name' => 'Ust Menu',
                'configSchema' => [
                    ['name' => 'menu_id', 'label' => 'Menü ID', 'type' => 'number'],
                ],
            ],
            1501 => [
                'name' => 'Hero Banner',
                'configSchema' => [
                    ['name' => 'title', 'label' => 'Başlık', 'type' => 'text', 'default' => 'Başlık'],
                    ['name' => 'align', 'label' => 'Hizalama', 'type' => 'select', 'options' => 'left,center,right', 'default' => 'left'],
                ],
            ],
        ]);

        $this->artisan('cms:sync-legacy-modules', ['--theme' => $theme->slug])
            ->assertExitCode(0);

        $this->assertDatabaseHas('section_templates', [
            'theme_id' => $theme->id,
            'legacy_module_key' => '137',
            'type' => 'ust-menu',
            'render_mode' => 'component',
            'is_active' => false,
        ]);

        $template = SectionTemplate::query()->where('legacy_module_key', '1501')->firstOrFail();

        $this->assertSame('html', $template->render_mode);
        $this->assertStringContainsString('{{title}}', (string) $template->html_template);
        $this->assertSame('left', data_get($template->default_content_json, 'align'));
        $this->assertSame(['left', 'center', 'right'], data_get($template->schema_json, 'align.options'));
        $this->assertSame('align', data_get($template->legacy_config_map_json, 'align'));
    }

    public function test_command_supports_dry_run(): void
    {
        Theme::create([
            'name' => 'Porto',
            'slug' => 'porto',
            'is_active' => true,
        ]);

        config()->set('modules', [
            90 => [
                'name' => 'Icerik Blogu',
                'configSchema' => [],
            ],
        ]);

        $this->artisan('cms:sync-legacy-modules', ['--dry-run' => true])
            ->assertExitCode(0);

        $this->assertDatabaseCount('section_templates', 0);
    }
}
