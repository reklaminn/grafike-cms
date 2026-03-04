<?php

namespace App\Console\Commands\Legacy;

use Illuminate\Console\Command;

class MigrateAllCommand extends Command
{
    protected $signature = 'migrate:legacy
                            {--fresh : Truncate all tables before migrating}
                            {--source-path= : Path to old CMS upload directory for media}
                            {--skip-media : Skip media file migration}
                            {--skip-submissions : Skip form submission migration}
                            {--verify : Run verification after migration}';

    protected $description = 'Run all legacy data migration commands in correct order';

    /**
     * Migration steps in dependency order.
     */
    protected array $steps = [
        ['command' => 'migrate:legacy:languages', 'label' => '🌐 Languages'],
        ['command' => 'migrate:legacy:admins', 'label' => '👤 Admins & Roles', 'extra' => ['--with-roles' => true]],
        ['command' => 'migrate:legacy:pages', 'label' => '📄 Pages'],
        ['command' => 'migrate:legacy:articles', 'label' => '📝 Articles'],
        ['command' => 'migrate:legacy:seo', 'label' => '🔍 SEO Entries'],
        ['command' => 'migrate:legacy:menus', 'label' => '📋 Menus'],
        ['command' => 'migrate:legacy:media', 'label' => '🖼️  Media Files'],
        ['command' => 'migrate:legacy:forms', 'label' => '📋 Forms & Submissions', 'extra' => ['--submissions' => true]],
        ['command' => 'migrate:legacy:settings', 'label' => '⚙️  Site Settings'],
        ['command' => 'migrate:legacy:redirects', 'label' => '🔄 Redirects'],
    ];

    public function handle(): int
    {
        $this->newLine();
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║     IRASPA Legacy CMS → Laravel Migration    ║');
        $this->info('║          Full Data Migration Suite            ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->newLine();

        $fresh = $this->option('fresh');
        $sourcePath = $this->option('source-path');
        $skipMedia = $this->option('skip-media');
        $skipSubmissions = $this->option('skip-submissions');

        if ($fresh) {
            $this->warn('⚠️  FRESH MODE: All existing data will be deleted before migration.');
            if (!$this->confirm('Are you sure you want to continue?')) {
                $this->info('Migration cancelled.');
                return self::SUCCESS;
            }
        }

        $totalSteps = count($this->steps);
        $currentStep = 0;
        $failedSteps = [];

        foreach ($this->steps as $step) {
            $currentStep++;
            $command = $step['command'];
            $label = $step['label'];

            // Skip media if requested
            if ($skipMedia && $command === 'migrate:legacy:media') {
                $this->warn("  [{$currentStep}/{$totalSteps}] ⏭️  Skipping {$label}");
                continue;
            }

            $this->info("  [{$currentStep}/{$totalSteps}] {$label}");
            $this->line("  Running: {$command}");

            $params = [];

            if ($fresh) {
                $params['--fresh'] = true;
            }

            // Add extra params defined in steps
            if (isset($step['extra'])) {
                $params = array_merge($params, $step['extra']);
            }

            // Skip submissions if requested
            if ($skipSubmissions && $command === 'migrate:legacy:forms') {
                unset($params['--submissions']);
            }

            // Add source path for media command
            if ($command === 'migrate:legacy:media' && $sourcePath) {
                $params['--source-path'] = $sourcePath;
            }

            try {
                $exitCode = $this->call($command, $params);

                if ($exitCode !== self::SUCCESS) {
                    $failedSteps[] = $label;
                    $this->error("  ❌ {$label} failed with exit code {$exitCode}");

                    if (!$this->confirm('Continue with remaining migrations?', true)) {
                        break;
                    }
                }
            } catch (\Exception $e) {
                $failedSteps[] = $label;
                $this->error("  ❌ {$label} threw an exception: " . $e->getMessage());

                if (!$this->confirm('Continue with remaining migrations?', true)) {
                    break;
                }
            }

            $this->newLine();
        }

        // Summary
        $this->newLine();
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║           Migration Complete                  ║');
        $this->info('╠══════════════════════════════════════════════╣');

        $successCount = $totalSteps - count($failedSteps) - ($skipMedia ? 1 : 0);
        $this->info("║  ✅ Successful: {$successCount}");

        if ($skipMedia) {
            $this->info("║  ⏭️  Skipped:    1 (Media)");
        }

        if (!empty($failedSteps)) {
            $this->error("║  ❌ Failed:     " . count($failedSteps));
            foreach ($failedSteps as $failed) {
                $this->error("║    - {$failed}");
            }
        }

        $this->info('╚══════════════════════════════════════════════╝');

        // Run verification if requested
        if ($this->option('verify')) {
            $this->newLine();
            $this->call('migrate:legacy:verify', ['--detailed' => true]);
        }

        return empty($failedSteps) ? self::SUCCESS : self::FAILURE;
    }
}
