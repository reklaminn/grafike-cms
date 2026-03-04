<?php

namespace App\Console\Commands\Legacy;

use App\Models\Language;

class MigrateLanguagesCommand extends BaseMigrationCommand
{
    protected $signature = 'migrate:legacy:languages
                            {--fresh : Truncate languages table before migrating}';

    protected $description = 'Migrate languages from legacy diller1 table';

    public function handle(): int
    {
        $this->info('🌐 Starting Languages Migration (diller1 → languages)...');

        if (!$this->checkLegacyConnection()) {
            return self::FAILURE;
        }

        if ($this->option('fresh') && $this->confirm('This will delete all existing languages. Continue?')) {
            Language::truncate();
            $this->warn('Languages table truncated.');
        }

        $legacyLanguages = $this->legacy('diller1')->get();

        if ($legacyLanguages->isEmpty()) {
            $this->warn('No languages found in legacy database.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($legacyLanguages->count());
        $bar->start();

        foreach ($legacyLanguages as $lang) {
            try {
                $code = $this->toUtf8($lang->ceviricode ?? 'tr');
                $name = $this->toUtf8($lang->kate ?? 'Türkçe');

                // Map common language codes to locales
                $localeMap = [
                    'tr' => 'tr_TR', 'en' => 'en_US', 'de' => 'de_DE',
                    'fr' => 'fr_FR', 'ar' => 'ar_SA', 'ru' => 'ru_RU',
                    'es' => 'es_ES', 'it' => 'it_IT', 'nl' => 'nl_NL',
                    'ja' => 'ja_JP', 'zh' => 'zh_CN', 'ko' => 'ko_KR',
                ];

                $language = Language::updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => $name,
                        'locale' => $localeMap[$code] ?? $code . '_' . strtoupper($code),
                        'is_active' => (bool) ($lang->alt ?? 1),
                        'direction' => in_array($code, ['ar', 'he', 'fa']) ? 'rtl' : 'ltr',
                        'sort_order' => (int) ($lang->sira ?? 0),
                    ]
                );

                $this->storeLegacyMapping($lang->id, $language->id, 'language');
                $this->migrated++;
            } catch (\Exception $e) {
                $this->failed++;
                $this->newLine();
                $this->error("Failed to migrate language ID {$lang->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->printSummary('Languages');

        return self::SUCCESS;
    }
}
