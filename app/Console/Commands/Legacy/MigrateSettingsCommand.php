<?php

namespace App\Console\Commands\Legacy;

use App\Models\SiteSetting;

class MigrateSettingsCommand extends BaseMigrationCommand
{
    protected $signature = 'migrate:legacy:settings
                            {--fresh : Truncate site_settings table before migrating}';

    protected $description = 'Migrate site settings from legacy tasarim table';

    /**
     * Mapping of tasarim columns to site_settings keys.
     */
    protected array $settingsMap = [
        'unvan' => 'site.company_name',
        'title' => 'site.title',
        'aciklama' => 'site.description',
        'sahip' => 'site.owner',
        'adres' => 'contact.address',
        'tel' => 'contact.phone',
        'fax' => 'contact.fax',
        'icq' => 'contact.messenger',
        'info' => 'site.info',
        'logocss' => 'design.logo_css',
        'arkazeminrenk' => 'design.background_color',
        'menuzeminrenk' => 'design.menu_bg_color',
        'sliderzeminrenk' => 'design.slider_bg_color',
        'baslikzeminrenk' => 'design.title_bg_color',
        'icerikzeminrenk' => 'design.content_bg_color',
        'altzeminrenk' => 'design.footer_bg_color',
        'yazifont' => 'design.font_family',
        'yazirengi' => 'design.text_color',
        'yazilrengi' => 'design.link_color',
        'yanmenurenk' => 'design.sidebar_color',
        'yanmenurenk2' => 'design.sidebar_color_2',
        'ifzeminrengi' => 'design.form_bg_color',
        'ifyazirengi' => 'design.form_text_color',
        'anauadet' => 'layout.top_category_count',
        'kateuadet' => 'layout.sub_category_count',
        'usttur' => 'layout.top_menu_type',
        'alttur' => 'layout.footer_menu_type',
        'urundur' => 'layout.product_type',
        'headercnt' => 'design.header_content',
        'havale' => 'payment.bank_transfer',
    ];

    public function handle(): int
    {
        $this->info('⚙️  Starting Settings Migration (tasarim → site_settings)...');

        if (!$this->checkLegacyConnection()) {
            return self::FAILURE;
        }

        if ($this->option('fresh') && $this->confirm('This will delete all existing settings. Continue?')) {
            SiteSetting::truncate();
            $this->warn('Site settings table truncated.');
        }

        // Get the active design record
        $legacySettings = $this->legacy('tasarim')
            ->where('aktif', '1')
            ->first();

        if (!$legacySettings) {
            // Fallback: get the first record
            $legacySettings = $this->legacy('tasarim')->first();
        }

        if (!$legacySettings) {
            $this->warn('No settings found in legacy database.');
            return self::SUCCESS;
        }

        $this->info('  Found active design configuration. Migrating settings...');

        // Migrate mapped settings
        foreach ($this->settingsMap as $legacyColumn => $settingKey) {
            try {
                if (isset($legacySettings->$legacyColumn) && $legacySettings->$legacyColumn !== null) {
                    $value = $this->toUtf8((string) $legacySettings->$legacyColumn);
                    SiteSetting::set($settingKey, $value);
                    $this->migrated++;
                }
            } catch (\Exception $e) {
                $this->failed++;
                $this->error("  ❌ Failed to migrate setting {$settingKey}: " . $e->getMessage());
            }
        }

        // Migrate numbered design parameters (d1-d60)
        $this->info('  📐 Migrating design parameters (d1-d60)...');
        for ($i = 1; $i <= 60; $i++) {
            $column = 'd' . $i;
            if (isset($legacySettings->$column) && !empty($legacySettings->$column)) {
                try {
                    SiteSetting::set("design.param.d{$i}", $this->toUtf8((string) $legacySettings->$column));
                    $this->migrated++;
                } catch (\Exception $e) {
                    $this->failed++;
                }
            }
        }

        // Migrate tour/product JSON if exists
        if (!empty($legacySettings->tourjson)) {
            try {
                $decoded = json_decode($legacySettings->tourjson, true);
                if ($decoded) {
                    SiteSetting::set('modules.tour_config', json_encode($decoded));
                } else {
                    SiteSetting::set('modules.tour_config', $this->toUtf8($legacySettings->tourjson));
                }
                $this->migrated++;
            } catch (\Exception $e) {
                $this->failed++;
            }
        }

        // Migrate page settings JSON
        if (!empty($legacySettings->sayfaayar)) {
            try {
                SiteSetting::set('layout.page_settings', $this->toUtf8($legacySettings->sayfaayar));
                $this->migrated++;
            } catch (\Exception $e) {
                $this->failed++;
            }
        }

        $this->printSummary('Settings');

        return self::SUCCESS;
    }
}
