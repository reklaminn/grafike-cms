<?php

namespace App\Console\Commands\Legacy;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

abstract class BaseMigrationCommand extends Command
{
    /**
     * Legacy DB connection name.
     */
    protected string $legacyConnection = 'legacy';

    /**
     * Counter for migrated records.
     */
    protected int $migrated = 0;
    protected int $skipped = 0;
    protected int $failed = 0;

    /**
     * Get a query builder for the legacy database.
     */
    protected function legacy(string $table)
    {
        return DB::connection($this->legacyConnection)->table($table);
    }

    /**
     * Convert latin5 (ISO 8859-9 Turkish) text to UTF-8.
     */
    protected function toUtf8(?string $text): ?string
    {
        if ($text === null || $text === '') {
            return $text;
        }

        // Try to detect and convert encoding
        $detected = mb_detect_encoding($text, ['UTF-8', 'ISO-8859-9', 'ISO-8859-1', 'Windows-1254'], true);

        if ($detected && $detected !== 'UTF-8') {
            return mb_convert_encoding($text, 'UTF-8', $detected);
        }

        // If already UTF-8 or cannot detect, try Windows-1254 (Turkish Windows charset)
        if (!mb_check_encoding($text, 'UTF-8')) {
            return mb_convert_encoding($text, 'UTF-8', 'Windows-1254');
        }

        return $text;
    }

    /**
     * Generate a unique slug from a given string.
     */
    protected function generateSlug(string $text, string $table = 'pages', string $column = 'slug'): string
    {
        // Turkish character mapping
        $turkishMap = [
            'ç' => 'c', 'Ç' => 'c', 'ğ' => 'g', 'Ğ' => 'g',
            'ı' => 'i', 'İ' => 'i', 'ö' => 'o', 'Ö' => 'o',
            'ş' => 's', 'Ş' => 's', 'ü' => 'u', 'Ü' => 'u',
        ];

        $slug = strtr($text, $turkishMap);
        $slug = Str::slug($slug);

        if (empty($slug)) {
            $slug = 'item-' . Str::random(6);
        }

        // Ensure uniqueness
        $original = $slug;
        $counter = 1;
        while (DB::table($table)->where($column, $slug)->exists()) {
            $slug = $original . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * Map a legacy ID to a new ID via the legacy_id_map table.
     */
    protected function mapLegacyId(int $legacyId, string $entityType): ?int
    {
        $mapping = DB::table('legacy_id_map')
            ->where('legacy_id', $legacyId)
            ->where('entity_type', $entityType)
            ->first();

        return $mapping?->new_id;
    }

    /**
     * Store a legacy-to-new ID mapping.
     */
    protected function storeLegacyMapping(int $legacyId, int $newId, string $entityType): void
    {
        DB::table('legacy_id_map')->updateOrInsert(
            ['legacy_id' => $legacyId, 'entity_type' => $entityType],
            ['new_id' => $newId, 'created_at' => now()]
        );
    }

    /**
     * Print a summary after migration.
     */
    protected function printSummary(string $entityName): void
    {
        $this->newLine();
        $this->info("╔══════════════════════════════════════╗");
        $this->info("║  {$entityName} Migration Summary");
        $this->info("╠══════════════════════════════════════╣");
        $this->info("║  ✅ Migrated: {$this->migrated}");
        if ($this->skipped > 0) {
            $this->warn("║  ⏭️  Skipped:  {$this->skipped}");
        }
        if ($this->failed > 0) {
            $this->error("║  ❌ Failed:   {$this->failed}");
        }
        $this->info("╚══════════════════════════════════════╝");
    }

    /**
     * Check if legacy database is accessible.
     */
    protected function checkLegacyConnection(): bool
    {
        try {
            DB::connection($this->legacyConnection)->getPdo();
            return true;
        } catch (\Exception $e) {
            $this->error("❌ Cannot connect to legacy database: " . $e->getMessage());
            $this->newLine();
            $this->warn("Please configure the legacy database connection in .env:");
            $this->line("  LEGACY_DB_HOST=localhost");
            $this->line("  LEGACY_DB_PORT=3306");
            $this->line("  LEGACY_DB_DATABASE=grafike-db1");
            $this->line("  LEGACY_DB_USERNAME=grafikedb");
            $this->line("  LEGACY_DB_PASSWORD=your_password");
            return false;
        }
    }

    /**
     * Parse legacy date string into a Carbon-compatible format.
     */
    protected function parseDate(?string $date): ?string
    {
        if (empty($date) || $date === '0000-00-00 00:00:00') {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($date)->format('Y-m-d H:i:s');
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Clean HTML content (fix encoding, remove dangerous tags).
     */
    protected function cleanHtml(?string $html): ?string
    {
        if (empty($html)) {
            return $html;
        }

        $html = $this->toUtf8($html);

        // Remove ASP include directives
        $html = preg_replace('/<!--\s*#include\s+.*?-->/', '', $html);

        // Fix relative image paths to absolute
        $html = preg_replace('/src=["\'](?!https?:\/\/)(?!\/)/', 'src="/legacy-assets/', $html);

        return $html;
    }
}
