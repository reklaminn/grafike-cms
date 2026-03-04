<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CmsHealthCheck extends Command
{
    protected $signature = 'cms:health';

    protected $description = 'Run CMS health check (database, models, config)';

    public function handle(): int
    {
        $this->info('Running CMS Health Check...');
        $this->newLine();

        $errors = 0;

        // Database connection
        try {
            DB::connection()->getPdo();
            $this->line('<fg=green>✓</> Database connection OK');
        } catch (\Throwable $e) {
            $this->line('<fg=red>✗</> Database connection FAILED: ' . $e->getMessage());
            $errors++;
        }

        // Model counts
        $models = [
            'Pages' => \App\Models\Page::count(),
            'Articles' => \App\Models\Article::count(),
            'Menus' => \App\Models\Menu::count(),
            'Forms' => \App\Models\Form::count(),
            'SEO Entries' => \App\Models\SeoEntry::count(),
            'Redirects' => \App\Models\Redirect::count(),
            'Members' => \App\Models\Member::count(),
            'Languages' => \App\Models\Language::count(),
            'Settings' => \App\Models\SiteSetting::count(),
            'Admins' => \App\Models\Admin::count(),
        ];

        $this->newLine();
        $this->info('Data Summary:');
        foreach ($models as $name => $count) {
            $this->line("  {$name}: {$count}");
        }

        // Config validation
        $this->newLine();
        $this->info('Config Check:');

        $homepage = config('cms.homepage_id');
        if ($homepage) {
            $page = \App\Models\Page::find($homepage);
            if ($page) {
                $this->line("<fg=green>✓</> Homepage configured: {$page->title}");
            } else {
                $this->line("<fg=yellow>!</> Homepage ID {$homepage} not found in pages table");
                $errors++;
            }
        } else {
            $this->line('<fg=yellow>!</> No homepage configured');
        }

        // Storage writable
        if (is_writable(storage_path())) {
            $this->line('<fg=green>✓</> Storage directory writable');
        } else {
            $this->line('<fg=red>✗</> Storage directory NOT writable');
            $errors++;
        }

        // Media disk
        if (is_dir(public_path('storage'))) {
            $this->line('<fg=green>✓</> Public storage symlink exists');
        } else {
            $this->line('<fg=yellow>!</> Public storage symlink missing (run: php artisan storage:link)');
        }

        // Module config
        $moduleCount = count(config('modules', []));
        $this->line("<fg=green>✓</> Modules configured: {$moduleCount}");

        $this->newLine();
        if ($errors === 0) {
            $this->info('Health check passed! No issues found.');
        } else {
            $this->error("Health check found {$errors} issue(s).");
        }

        return $errors === 0 ? self::SUCCESS : self::FAILURE;
    }
}
