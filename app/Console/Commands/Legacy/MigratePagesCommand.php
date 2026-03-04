<?php

namespace App\Console\Commands\Legacy;

use App\Models\Page;
use Illuminate\Support\Facades\DB;

class MigratePagesCommand extends BaseMigrationCommand
{
    protected $signature = 'migrate:legacy:pages
                            {--fresh : Truncate pages table before migrating}';

    protected $description = 'Migrate pages from legacy kategoriyazi table';

    public function handle(): int
    {
        $this->info('📄 Starting Pages Migration (kategoriyazi → pages)...');

        if (!$this->checkLegacyConnection()) {
            return self::FAILURE;
        }

        if ($this->option('fresh') && $this->confirm('This will delete all existing pages. Continue?')) {
            Page::truncate();
            $this->warn('Pages table truncated.');
        }

        // First pass: create all pages (without parent references)
        $legacyPages = $this->legacy('kategoriyazi')->orderBy('id')->get();

        if ($legacyPages->isEmpty()) {
            $this->warn('No pages found in legacy database.');
            return self::SUCCESS;
        }

        $this->info("  Found {$legacyPages->count()} pages to migrate.");

        $bar = $this->output->createProgressBar($legacyPages->count());
        $bar->start();

        foreach ($legacyPages as $legacyPage) {
            try {
                $title = $this->toUtf8($legacyPage->isim ?? 'Untitled');
                $languageId = $this->mapLegacyId((int) ($legacyPage->dil ?? 240), 'language');

                // Parse layout JSON from old columns
                $layoutJson = $this->buildLayoutFromLegacy($legacyPage);

                $slug = $this->generateSlug($title, 'pages', 'slug');

                $page = Page::create([
                    'title' => $title,
                    'language_id' => $languageId ?? 1,
                    'status' => ($legacyPage->durum ?? '1') == '1' ? 'published' : 'draft',
                    'show_in_menu' => (bool) ($legacyPage->menugosterim ?? 1),
                    'sort_order' => (int) ($legacyPage->sira ?? 0),
                    'slug' => $slug,
                    'external_url' => $this->toUtf8($legacyPage->link ?? null),
                    'link_target' => $legacyPage->target ?? '_self',
                    'module_type' => ($legacyPage->modulmu ?? '0') == '1' ? 'module' : null,
                    'template' => $this->toUtf8($legacyPage->sayfatemp ?? null),
                    'layout_json' => $layoutJson,
                    'is_password_protected' => (bool) ($legacyPage->sifreli ?? 0),
                    'show_social_share' => (bool) ($legacyPage->sosyal1 ?? 0),
                    'show_facebook_comments' => (bool) ($legacyPage->sosyal2 ?? 0),
                    'show_breadcrumb' => (bool) ($legacyPage->baslikd ?? 1),
                    'view_count' => (int) ($legacyPage->okusayi ?? 0),
                    'legacy_id' => $legacyPage->id,
                    'page_template' => $legacyPage->sayfatemp ?? null,
                ]);

                $this->storeLegacyMapping($legacyPage->id, $page->id, 'page');
                $this->migrated++;
            } catch (\Exception $e) {
                $this->failed++;
                $this->newLine();
                $this->error("Failed to migrate page ID {$legacyPage->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // Second pass: set parent relationships
        $this->info('  🔗 Setting parent-child relationships...');
        $updated = 0;

        foreach ($legacyPages as $legacyPage) {
            if (!empty($legacyPage->anasek) && $legacyPage->anasek != '0' && ($legacyPage->alt ?? '1') == '0') {
                $newPageId = $this->mapLegacyId($legacyPage->id, 'page');
                $newParentId = $this->mapLegacyId((int) $legacyPage->anasek, 'page');

                if ($newPageId && $newParentId) {
                    Page::where('id', $newPageId)->update(['parent_id' => $newParentId]);
                    $updated++;
                }
            }
        }

        $this->info("  ✅ Updated {$updated} parent-child relationships.");
        $this->printSummary('Pages');

        return self::SUCCESS;
    }

    /**
     * Build layout JSON from legacy column-based layout.
     */
    protected function buildLayoutFromLegacy(object $legacyPage): ?array
    {
        $rows = [];

        // The old system stores layout as module IDs in columns: sol (left), orta (center), sayara (right)
        $leftModules = $this->parseModuleString($legacyPage->sol ?? '');
        $centerModules = $this->parseModuleString($legacyPage->orta ?? '');
        $rightModules = $this->parseModuleString($legacyPage->sayara ?? '');

        // Also check smodula and stura for additional module placement
        $headerModules = $this->parseModuleString($legacyPage->smodula ?? '');

        if (!empty($headerModules)) {
            $rows[] = [
                'type' => 'header',
                'columns' => [
                    ['width' => 12, 'modules' => $headerModules],
                ],
            ];
        }

        // Determine layout based on which columns are populated
        if (!empty($leftModules) && !empty($centerModules) && !empty($rightModules)) {
            // 3-column layout
            $rows[] = [
                'type' => 'content',
                'columns' => [
                    ['width' => 3, 'modules' => $leftModules],
                    ['width' => 6, 'modules' => $centerModules],
                    ['width' => 3, 'modules' => $rightModules],
                ],
            ];
        } elseif (!empty($leftModules) && !empty($centerModules)) {
            // 2-column layout (left sidebar)
            $rows[] = [
                'type' => 'content',
                'columns' => [
                    ['width' => 3, 'modules' => $leftModules],
                    ['width' => 9, 'modules' => $centerModules],
                ],
            ];
        } elseif (!empty($centerModules) && !empty($rightModules)) {
            // 2-column layout (right sidebar)
            $rows[] = [
                'type' => 'content',
                'columns' => [
                    ['width' => 9, 'modules' => $centerModules],
                    ['width' => 3, 'modules' => $rightModules],
                ],
            ];
        } elseif (!empty($centerModules)) {
            // Single column layout
            $rows[] = [
                'type' => 'content',
                'columns' => [
                    ['width' => 12, 'modules' => $centerModules],
                ],
            ];
        }

        return !empty($rows) ? $rows : null;
    }

    /**
     * Parse a module string (pipe or comma-separated module IDs with optional config).
     */
    protected function parseModuleString(?string $moduleStr): array
    {
        if (empty($moduleStr)) {
            return [];
        }

        $modules = [];
        // Modules can be stored as JSON, pipe-separated, or comma-separated
        $decoded = json_decode($moduleStr, true);

        if (is_array($decoded)) {
            foreach ($decoded as $mod) {
                if (is_array($mod)) {
                    $modules[] = $mod;
                } else {
                    $modules[] = ['id' => (int) $mod, 'config' => []];
                }
            }
        } else {
            // Try pipe or comma separated
            $parts = preg_split('/[|,]/', $moduleStr);
            foreach ($parts as $part) {
                $part = trim($part);
                if (is_numeric($part) && (int) $part > 0) {
                    $modules[] = ['id' => (int) $part, 'config' => []];
                }
            }
        }

        return $modules;
    }
}
