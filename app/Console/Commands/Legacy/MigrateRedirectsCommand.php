<?php

namespace App\Console\Commands\Legacy;

use App\Models\Redirect;

class MigrateRedirectsCommand extends BaseMigrationCommand
{
    protected $signature = 'migrate:legacy:redirects
                            {--fresh : Truncate redirects table before migrating}';

    protected $description = 'Migrate URL redirects from legacy seolink table';

    public function handle(): int
    {
        $this->info('🔄 Starting Redirects Migration (seolink → redirects)...');

        if (!$this->checkLegacyConnection()) {
            return self::FAILURE;
        }

        if ($this->option('fresh') && $this->confirm('This will delete all existing redirects. Continue?')) {
            Redirect::truncate();
            $this->warn('Redirects table truncated.');
        }

        $legacyRedirects = $this->legacy('seolink')->orderBy('id')->get();

        if ($legacyRedirects->isEmpty()) {
            $this->warn('No redirects found in legacy database.');
            return self::SUCCESS;
        }

        $this->info("  Found {$legacyRedirects->count()} redirects to migrate.");

        $bar = $this->output->createProgressBar($legacyRedirects->count());
        $bar->start();

        foreach ($legacyRedirects as $legacy) {
            try {
                $oldUrl = $this->toUtf8($legacy->eskilink ?? '');
                $newUrl = $this->toUtf8($legacy->yenilink ?? '');

                if (empty($oldUrl) || empty($newUrl)) {
                    $this->skipped++;
                    $bar->advance();
                    continue;
                }

                // Normalize URLs
                $oldUrl = '/' . ltrim($oldUrl, '/');
                $newUrl = ltrim($newUrl, '/');

                // Determine if new URL is relative or absolute
                if (!str_starts_with($newUrl, 'http')) {
                    $newUrl = '/' . $newUrl;
                }

                // Skip if old == new
                if ($oldUrl === $newUrl) {
                    $this->skipped++;
                    $bar->advance();
                    continue;
                }

                Redirect::updateOrCreate(
                    ['old_url' => $oldUrl],
                    [
                        'new_url' => $newUrl,
                        'status_code' => 301,
                        'is_active' => true,
                        'hit_count' => (int) ($legacy->tiklama ?? 0),
                    ]
                );

                $this->migrated++;
            } catch (\Exception $e) {
                $this->failed++;
                $this->newLine();
                $this->error("Failed to migrate redirect ID {$legacy->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->printSummary('Redirects');

        return self::SUCCESS;
    }
}
