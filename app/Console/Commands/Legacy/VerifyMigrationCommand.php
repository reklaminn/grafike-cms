<?php

namespace App\Console\Commands\Legacy;

use Illuminate\Support\Facades\DB;

class VerifyMigrationCommand extends BaseMigrationCommand
{
    protected $signature = 'migrate:legacy:verify
                            {--detailed : Show detailed comparison per table}';

    protected $description = 'Verify legacy data migration by comparing record counts and integrity';

    /**
     * Table mapping: legacy table → [new table, entity_type]
     */
    protected array $tableMap = [
        'diller1' => ['languages', 'language'],
        'yonetici' => ['admins', 'admin'],
        'kategoriyazi' => ['pages', 'page'],
        'yazilar' => ['articles', 'article'],
        'seolar' => ['seo_entries', 'seo'],
        'menuler' => ['menu_items', 'menu_item'],
        'resimd' => ['media', 'media'],
        'form_logs' => ['form_submissions', 'form_submission'],
        'seolink' => ['redirects', 'redirect'],
    ];

    public function handle(): int
    {
        $this->info('🔍 Starting Migration Verification...');
        $this->newLine();

        if (!$this->checkLegacyConnection()) {
            return self::FAILURE;
        }

        $allPassed = true;
        $results = [];

        foreach ($this->tableMap as $legacyTable => [$newTable, $entityType]) {
            try {
                $legacyCount = $this->legacy($legacyTable)->count();
                $newCount = DB::table($newTable)->count();
                $mappedCount = DB::table('legacy_id_map')
                    ->where('entity_type', $entityType)
                    ->count();

                $status = '✅';
                $note = '';

                if ($newCount === 0 && $legacyCount > 0) {
                    $status = '❌';
                    $note = 'Not migrated';
                    $allPassed = false;
                } elseif ($newCount < $legacyCount) {
                    $status = '⚠️';
                    $note = 'Partial migration';
                    $diff = $legacyCount - $newCount;
                    $note .= " ({$diff} missing)";
                } elseif ($newCount >= $legacyCount) {
                    $note = 'Complete';
                }

                $results[] = [
                    'status' => $status,
                    'legacy' => $legacyTable,
                    'new' => $newTable,
                    'legacy_count' => $legacyCount,
                    'new_count' => $newCount,
                    'mapped' => $mappedCount,
                    'note' => $note,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'status' => '❌',
                    'legacy' => $legacyTable,
                    'new' => $newTable,
                    'legacy_count' => '?',
                    'new_count' => '?',
                    'mapped' => '?',
                    'note' => $e->getMessage(),
                ];
                $allPassed = false;
            }
        }

        // Display results table
        $this->table(
            ['Status', 'Legacy Table', 'New Table', 'Legacy #', 'New #', 'Mapped', 'Note'],
            array_map(fn($r) => [
                $r['status'], $r['legacy'], $r['new'],
                $r['legacy_count'], $r['new_count'], $r['mapped'], $r['note'],
            ], $results)
        );

        // Additional integrity checks
        $this->newLine();
        $this->info('🔗 Integrity Checks:');

        // Check orphan pages (parent_id references)
        $orphanPages = DB::table('pages')
            ->whereNotNull('parent_id')
            ->whereNotIn('parent_id', DB::table('pages')->select('id'))
            ->count();
        $this->line("  Pages with invalid parent_id: " . ($orphanPages === 0 ? '✅ 0' : "⚠️ {$orphanPages}"));

        // Check orphan articles (page_id references)
        $orphanArticles = DB::table('articles')
            ->whereNotNull('page_id')
            ->whereNotIn('page_id', DB::table('pages')->select('id'))
            ->count();
        $this->line("  Articles with invalid page_id: " . ($orphanArticles === 0 ? '✅ 0' : "⚠️ {$orphanArticles}"));

        // Check SEO entries with valid seoable references
        $orphanSeo = DB::table('seo_entries')
            ->where('seoable_type', 'App\\Models\\Page')
            ->whereNotIn('seoable_id', DB::table('pages')->select('id'))
            ->count();
        $orphanSeo += DB::table('seo_entries')
            ->where('seoable_type', 'App\\Models\\Article')
            ->whereNotIn('seoable_id', DB::table('articles')->select('id'))
            ->count();
        $this->line("  SEO entries with invalid references: " . ($orphanSeo === 0 ? '✅ 0' : "⚠️ {$orphanSeo}"));

        // Check menu items with valid menu_id
        $orphanMenuItems = DB::table('menu_items')
            ->whereNotIn('menu_id', DB::table('menus')->select('id'))
            ->count();
        $this->line("  Menu items with invalid menu_id: " . ($orphanMenuItems === 0 ? '✅ 0' : "⚠️ {$orphanMenuItems}"));

        // Check admins with legacy passwords (still need first login)
        $legacyPasswordCount = DB::table('admins')
            ->whereNotNull('legacy_password')
            ->count();
        if ($legacyPasswordCount > 0) {
            $this->line("  Admins with legacy passwords pending upgrade: ℹ️ {$legacyPasswordCount}");
        }

        // Detailed output if requested
        if ($this->option('detailed')) {
            $this->newLine();
            $this->info('📊 Legacy ID Map Summary:');

            $mapCounts = DB::table('legacy_id_map')
                ->select('entity_type', DB::raw('COUNT(*) as count'))
                ->groupBy('entity_type')
                ->get();

            foreach ($mapCounts as $row) {
                $this->line("  {$row->entity_type}: {$row->count} mappings");
            }
        }

        $this->newLine();
        if ($allPassed) {
            $this->info('🎉 All migration checks passed!');
        } else {
            $this->warn('⚠️  Some checks failed. Review the table above for details.');
        }

        return $allPassed ? self::SUCCESS : self::FAILURE;
    }
}
