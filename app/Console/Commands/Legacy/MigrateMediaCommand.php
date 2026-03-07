<?php

namespace App\Console\Commands\Legacy;

use App\Models\Page;
use App\Models\Article;
use Illuminate\Support\Facades\Storage;

class MigrateMediaCommand extends BaseMigrationCommand
{
    protected $signature = 'migrate:legacy:media
                            {--fresh : Delete all existing media before migrating}
                            {--source-path= : Path to old CMS upload directory}
                            {--dry-run : Show what would be migrated without actually doing it}';

    protected $description = 'Migrate media files from legacy resimd table to Spatie Media Library';

    public function handle(): int
    {
        $this->info('🖼️  Starting Media Migration (resimd → Spatie Media Library)...');

        if (!$this->checkLegacyConnection()) {
            return self::FAILURE;
        }

        $sourcePath = $this->option('source-path')
            ?? config('cms.legacy_media_path', '/Users/eserulusoy/Downloads/grafike');

        $this->info("  📂 Source path: {$sourcePath}");

        if ($this->option('fresh') && $this->confirm('This will delete ALL existing media. Continue?')) {
            \Spatie\MediaLibrary\MediaCollections\Models\Media::truncate();
            $this->warn('Media table truncated.');
        }

        $legacyMedia = $this->legacy('resimd')->orderBy('fid')->get();

        if ($legacyMedia->isEmpty()) {
            $this->warn('No media files found in legacy database.');
            return self::SUCCESS;
        }

        $this->info("  Found {$legacyMedia->count()} media records to migrate.");
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('  🏃 DRY RUN MODE - No files will be copied.');
        }

        $bar = $this->output->createProgressBar($legacyMedia->count());
        $bar->start();

        foreach ($legacyMedia as $media) {
            try {
                $filename = $media->resimid ?? $media->dosyad ?? null;

                if (empty($filename)) {
                    $this->skipped++;
                    $bar->advance();
                    continue;
                }

                // Determine the owning entity
                $entityId = (int) ($media->baslik ?? 0);
                $entityType = $this->determineEntityType($media);
                $entity = null;

                if ($entityType === 'page') {
                    $newId = $this->mapLegacyId($entityId, 'page');
                    $entity = $newId ? Page::find($newId) : null;
                } elseif ($entityType === 'article') {
                    $newId = $this->mapLegacyId($entityId, 'article');
                    $entity = $newId ? Article::find($newId) : null;
                }

                if (!$entity) {
                    $this->skipped++;
                    $bar->advance();
                    continue;
                }

                // Build possible file paths
                $possiblePaths = $this->buildPossiblePaths($sourcePath, $filename, $media);
                $foundPath = null;

                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        $foundPath = $path;
                        break;
                    }
                }

                if (!$foundPath) {
                    $this->skipped++;
                    $bar->advance();
                    continue;
                }

                if ($dryRun) {
                    $this->migrated++;
                    $bar->advance();
                    continue;
                }

                // Determine collection
                $collection = $this->determineCollection($media);

                // Add to Spatie Media Library
                $mediaItem = $entity->addMedia($foundPath)
                    ->preservingOriginal()
                    ->withCustomProperties([
                        'legacy_id' => $media->fid,
                        'alt_text' => $this->toUtf8($media->resad ?? ''),
                        'caption' => $this->toUtf8($media->acik ?? ''),
                        'dimensions' => $media->ressize ?? null,
                    ])
                    ->toMediaCollection($collection);

                $this->storeLegacyMapping($media->fid, $mediaItem->id, 'media');
                $this->migrated++;
            } catch (\Exception $e) {
                $this->failed++;
                $this->newLine();
                $this->error("Failed to migrate media FID {$media->fid}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->printSummary('Media');

        if ($dryRun) {
            $this->newLine();
            $this->warn("  ℹ️  This was a dry run. Run without --dry-run to actually migrate files.");
        }

        return self::SUCCESS;
    }

    /**
     * Determine if the media belongs to a page or article.
     */
    protected function determineEntityType(object $media): string
    {
        $tur = $media->tur ?? '';

        // Check if referenced ID exists as article or page
        $entityId = (int) ($media->baslik ?? 0);

        // Try article first (more common)
        if ($this->mapLegacyId($entityId, 'article')) {
            return 'article';
        }

        if ($this->mapLegacyId($entityId, 'page')) {
            return 'page';
        }

        return 'article'; // default
    }

    /**
     * Determine the media collection (cover, gallery, etc.).
     */
    protected function determineCollection(object $media): string
    {
        $type = $media->type ?? '0';

        if ($type == '1') {
            return 'cover'; // Featured/cover image
        }

        return 'gallery';
    }

    /**
     * Build possible file paths for the media file.
     */
    protected function buildPossiblePaths(string $basePath, string $filename, object $media): array
    {
        $firma = $media->firma ?? '';
        $dosyad = $media->dosyad ?? '';

        return array_filter([
            $basePath . '/uploads/' . $filename,
            $basePath . '/uploads/' . $firma . '/' . $filename,
            $basePath . '/resimler/' . $filename,
            $basePath . '/resimler/' . $firma . '/' . $filename,
            $basePath . '/images/' . $filename,
            $basePath . '/' . $dosyad,
            $basePath . '/' . $filename,
        ]);
    }
}
