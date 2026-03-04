<?php

namespace App\Console\Commands\Legacy;

use App\Models\SeoEntry;
use App\Models\Page;
use App\Models\Article;

class MigrateSeoCommand extends BaseMigrationCommand
{
    protected $signature = 'migrate:legacy:seo
                            {--fresh : Truncate seo_entries table before migrating}';

    protected $description = 'Migrate SEO entries from legacy seolar table';

    public function handle(): int
    {
        $this->info('🔍 Starting SEO Migration (seolar → seo_entries)...');

        if (!$this->checkLegacyConnection()) {
            return self::FAILURE;
        }

        if ($this->option('fresh') && $this->confirm('This will delete all existing SEO entries. Continue?')) {
            SeoEntry::truncate();
            $this->warn('SEO entries table truncated.');
        }

        $legacySeo = $this->legacy('seolar')->orderBy('id')->get();

        if ($legacySeo->isEmpty()) {
            $this->warn('No SEO entries found in legacy database.');
            return self::SUCCESS;
        }

        $this->info("  Found {$legacySeo->count()} SEO entries to migrate.");

        $bar = $this->output->createProgressBar($legacySeo->count());
        $bar->start();

        foreach ($legacySeo as $legacy) {
            try {
                // Determine the polymorphic type
                $seoType = $legacy->seoturu ?? 'sayfa';
                $seoableType = null;
                $seoableId = null;

                if ($seoType === 'sayfa') {
                    // Map to Page model
                    $seoableType = Page::class;
                    $seoableId = $this->mapLegacyId((int) $legacy->seoid, 'page');
                } elseif ($seoType === 'yazi') {
                    // Map to Article model
                    $seoableType = Article::class;
                    $seoableId = $this->mapLegacyId((int) $legacy->seoid, 'article');
                }

                if (!$seoableId) {
                    $this->skipped++;
                    $bar->advance();
                    continue;
                }

                // Map language
                $languageId = null;
                if (!empty($legacy->lang)) {
                    $languageId = $this->mapLegacyId((int) $legacy->lang, 'language');
                }

                $slug = $this->toUtf8($legacy->seolink ?? '');
                if (empty($slug)) {
                    $slug = $this->toUtf8($legacy->seolinkek ?? '');
                }

                // Clean the slug
                $slug = ltrim($slug, '/');
                if (empty($slug)) {
                    $slug = $this->generateSlug(
                        $this->toUtf8($legacy->seobaslik ?? 'seo-entry-' . $legacy->id),
                        'seo_entries',
                        'slug'
                    );
                }

                // Ensure unique slug
                $originalSlug = $slug;
                $counter = 1;
                while (SeoEntry::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter++;
                }

                // Parse structured data
                $structuredData = null;
                if (!empty($legacy->structred)) {
                    $decoded = json_decode($legacy->structred, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $structuredData = $decoded;
                    }
                }

                $seoEntry = SeoEntry::create([
                    'seoable_id' => $seoableId,
                    'seoable_type' => $seoableType,
                    'slug' => $slug,
                    'language_id' => $languageId ?? 1,
                    'meta_title' => $this->toUtf8($legacy->seobaslik ?? null),
                    'meta_description' => $this->toUtf8($legacy->seoaciklama ?? null),
                    'meta_keywords' => $this->toUtf8($legacy->seoanahtar ?? $legacy->seotag ?? null),
                    'h1_override' => $this->toUtf8($legacy->seoh1 ?? null),
                    'canonical_url' => $this->toUtf8($legacy->canoncial ?? null),
                    'hreflang_tags' => $this->toUtf8($legacy->hraflang ?? null),
                    'is_noindex' => (bool) ($legacy->noindex ?? 0),
                    'page_css' => $this->toUtf8($legacy->seohtml ?? null),
                    'legacy_id' => $legacy->id,
                ]);

                $this->storeLegacyMapping($legacy->id, $seoEntry->id, 'seo');
                $this->migrated++;
            } catch (\Exception $e) {
                $this->failed++;
                $this->newLine();
                $this->error("Failed to migrate SEO ID {$legacy->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->printSummary('SEO Entries');

        return self::SUCCESS;
    }
}
