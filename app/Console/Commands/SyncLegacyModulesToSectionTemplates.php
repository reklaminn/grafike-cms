<?php

namespace App\Console\Commands;

use App\Models\SectionTemplate;
use App\Models\Theme;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SyncLegacyModulesToSectionTemplates extends Command
{
    protected $signature = 'cms:sync-legacy-modules
        {--theme= : Hedef tema slug değeri}
        {--activate : Oluşan kayıtları aktif yap}
        {--dry-run : Sadece ne olacağını göster, yazma}';

    protected $description = 'Sync legacy config/modules.php definitions into admin-manageable section templates';

    public function handle(): int
    {
        $theme = $this->resolveTheme();

        if (! $theme) {
            $this->error('Aktif veya hedef bir tema bulunamadı. Önce bir theme kaydı oluştur.');

            return self::FAILURE;
        }

        $modules = collect(config('modules', []));

        if ($modules->isEmpty()) {
            $this->warn('config/modules.php içinde sync edilecek modül bulunamadı.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Tema: %s (%s)', $theme->name, $theme->slug));
        $this->line(sprintf('Legacy modül sayısı: %d', $modules->count()));
        $this->newLine();

        $rows = [];
        $created = 0;
        $updated = 0;

        /** @var array<int, array<string, mixed>> $modulesArray */
        $modulesArray = $modules->all();

        foreach ($modulesArray as $legacyKey => $module) {
            $payload = $this->buildSectionTemplatePayload($theme, (string) $legacyKey, $module);
            $existing = SectionTemplate::query()
                ->where('theme_id', $theme->id)
                ->where('legacy_module_key', $payload['legacy_module_key'])
                ->first();

            $action = $existing ? 'update' : 'create';
            $rows[] = [
                $action,
                $payload['name'],
                $payload['type'],
                $payload['variation'],
                $payload['render_mode'],
                $payload['legacy_module_key'],
                $payload['is_active'] ? 'aktif' : 'taslak',
            ];

            if ($this->option('dry-run')) {
                continue;
            }

            SectionTemplate::updateOrCreate(
                [
                    'theme_id' => $theme->id,
                    'legacy_module_key' => $payload['legacy_module_key'],
                ],
                $payload
            );

            $existing ? $updated++ : $created++;
        }

        $this->table(
            ['İşlem', 'Şablon', 'Type', 'Variation', 'Mode', 'Legacy Modül', 'Durum'],
            $rows
        );

        if ($this->option('dry-run')) {
            $this->comment('Dry run tamamlandı. Veritabanına yazılmadı.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            'Sync tamamlandı. %d yeni kayıt, %d güncelleme.',
            $created,
            $updated
        ));

        return self::SUCCESS;
    }

    private function resolveTheme(): ?Theme
    {
        $themeSlug = $this->option('theme');

        if (is_string($themeSlug) && $themeSlug !== '') {
            return Theme::query()->where('slug', $themeSlug)->first();
        }

        return Theme::query()->active()->orderBy('id')->first()
            ?? Theme::query()->orderBy('id')->first();
    }

    /**
     * @param  array<string, mixed>  $module
     * @return array<string, mixed>
     */
    private function buildSectionTemplatePayload(Theme $theme, string $legacyKey, array $module): array
    {
        $name = (string) ($module['name'] ?? $legacyKey);
        $type = $this->normalizeType($name, $legacyKey);
        $variation = 'legacy-' . Str::slug($name);
        $schema = $this->normalizeSchema($module['configSchema'] ?? []);
        $defaultContent = $this->extractDefaultContent($schema);
        $componentKey = 'legacy/' . Str::slug($legacyKey . '-' . $name);
        $specialCase = $this->specialCasePayload($legacyKey, $schema, $defaultContent);

        return [
            'theme_id' => $theme->id,
            'name' => $name . ' / Legacy',
            'type' => $type,
            'variation' => $variation,
            'render_mode' => $specialCase['render_mode'] ?? 'component',
            'component_key' => $specialCase['component_key'] ?? $componentKey,
            'legacy_module_key' => $legacyKey,
            'html_template' => $specialCase['html_template'] ?? null,
            'schema_json' => $specialCase['schema_json'] ?? $schema,
            'legacy_config_map_json' => $this->buildLegacyConfigMap($schema),
            'default_content_json' => $specialCase['default_content_json'] ?? $defaultContent,
            'preview_image' => null,
            'is_active' => (bool) $this->option('activate'),
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $schema
     * @param  array<string, mixed>  $defaultContent
     * @return array<string, mixed>
     */
    private function specialCasePayload(string $legacyKey, array $schema, array $defaultContent): array
    {
        return match ($legacyKey) {
            '90' => [
                'render_mode' => 'html',
                'component_key' => null,
                'html_template' => <<<'HTML'
<section class="section-card" style="padding:32px;">
  <div class="container">
    <h2>{{title}}</h2>
    <div class="content">{{{body_html}}}</div>
  </div>
</section>
HTML,
                'schema_json' => array_merge([
                    'title' => ['type' => 'text', 'label' => 'Başlık', 'default' => 'İçerik Alanı'],
                    'body_html' => ['type' => 'textarea', 'label' => 'İçerik / HTML', 'default' => '<p>Bu alan legacy içerik bloğundan dönüştürüldü.</p>'],
                ], $schema),
                'default_content_json' => array_merge([
                    'title' => 'İçerik Alanı',
                    'body_html' => '<p>Bu alan legacy içerik bloğundan dönüştürüldü.</p>',
                ], $defaultContent),
            ],
            '110' => [
                'render_mode' => 'html',
                'component_key' => null,
                'html_template' => <<<'HTML'
<section class="section-card page-header-card" style="padding:32px;">
  <div class="container">
    <{{tag}}>{{title}}</{{tag}}>
  </div>
</section>
HTML,
                'schema_json' => array_merge([
                    'title' => ['type' => 'text', 'label' => 'Başlık', 'default' => 'Sayfa Başlığı'],
                ], $schema),
                'default_content_json' => array_merge([
                    'title' => 'Sayfa Başlığı',
                ], $defaultContent),
            ],
            '1501' => [
                'render_mode' => 'html',
                'component_key' => null,
                'html_template' => <<<'HTML'
<section class="hero hero--porto-split">
  <div class="container">
    <h1>{{title}}</h1>
    <p class="subtitle">{{subtitle}}</p>
    <a class="button" href="{{button_url}}">{{button_text}}</a>
  </div>
</section>
HTML,
            ],
            '1502' => [
                'render_mode' => 'html',
                'component_key' => null,
                'html_template' => <<<'HTML'
<section class="section-card" style="padding:32px;">
  <div class="container">
    <h2>{{title}}</h2>
    <div class="content">{{{body}}}</div>
  </div>
</section>
HTML,
            ],
            '1503' => [
                'render_mode' => 'html',
                'component_key' => null,
                'html_template' => <<<'HTML'
<section class="section-card" style="padding:32px;">
  <div class="container">
    <h2>{{title}}</h2>
    <p>{{body}}</p>
    <div class="actions">
      <a class="button" href="{{button_url}}">{{button_text}}</a>
    </div>
  </div>
</section>
HTML,
            ],
            '1504' => [
                'render_mode' => 'html',
                'component_key' => null,
                'html_template' => '<div style="height: {{height}}px;"></div>',
            ],
            '1505' => [
                'render_mode' => 'html',
                'component_key' => null,
                'html_template' => <<<'HTML'
<section class="section-card" style="padding:32px;">
  <div class="container">
    <h2>{{title}}</h2>
    <div class="video-embed">{{embed_url}}</div>
  </div>
</section>
HTML,
            ],
            '1506' => [
                'render_mode' => 'html',
                'component_key' => null,
                'html_template' => <<<'HTML'
<section class="custom-html-card">
  {{{body}}}
</section>
HTML,
            ],
            default => [],
        };
    }

    private function normalizeType(string $name, string $legacyKey): string
    {
        $normalized = Str::slug(Str::of($name)->lower()->replace(['ı', 'i̇'], 'i')->value());

        return $normalized !== '' ? $normalized : Str::slug($legacyKey);
    }

    /**
     * @param  mixed  $schema
     * @return array<string, array<string, mixed>>
     */
    private function normalizeSchema(mixed $schema): array
    {
        return collect(is_array($schema) ? $schema : [])
            ->mapWithKeys(function ($field) {
                if (! is_array($field) || empty($field['name'])) {
                    return [];
                }

                $name = (string) $field['name'];
                $type = (string) ($field['type'] ?? 'text');

                $payload = [
                    'type' => $type,
                    'label' => $field['label'] ?? Str::headline($name),
                ];

                if (array_key_exists('default', $field)) {
                    $payload['default'] = $field['default'];
                }

                if (! empty($field['options'])) {
                    $payload['options'] = $this->normalizeOptions($field['options']);
                }

                return [$name => $payload];
            })
            ->all();
    }

    /**
     * @param  mixed  $options
     * @return array<int, string>
     */
    private function normalizeOptions(mixed $options): array
    {
        if (is_array($options)) {
            return array_values(array_map('strval', $options));
        }

        if (is_string($options) && $options !== '') {
            return collect(explode(',', $options))
                ->map(fn (string $item) => trim($item))
                ->filter()
                ->values()
                ->all();
        }

        return [];
    }

    /**
     * @param  array<string, array<string, mixed>>  $schema
     * @return array<string, mixed>
     */
    private function extractDefaultContent(array $schema): array
    {
        return collect($schema)
            ->mapWithKeys(function (array $field, string $name) {
                if (array_key_exists('default', $field)) {
                    return [$name => $field['default']];
                }

                return [$name => $field['type'] === 'boolean' ? false : ''];
            })
            ->all();
    }

    /**
     * @param  array<string, array<string, mixed>>  $schema
     * @return array<string, string>
     */
    private function buildLegacyConfigMap(array $schema): array
    {
        return collect($schema)
            ->keys()
            ->mapWithKeys(fn (string $name) => [$name => $name])
            ->all();
    }
}
