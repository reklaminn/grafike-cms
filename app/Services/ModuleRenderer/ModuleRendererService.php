<?php

namespace App\Services\ModuleRenderer;

use App\Models\Article;
use App\Models\Page;
use Illuminate\Support\Facades\Log;

class ModuleRendererService
{
    protected array $moduleInstances = [];

    public function render(int $moduleId, array $config, Page $page, ?Article $article = null): string
    {
        $moduleConfig = config("modules.{$moduleId}");

        if (! $moduleConfig) {
            Log::warning("Unknown module ID: {$moduleId}");

            return "<!-- Unknown module: {$moduleId} -->";
        }

        $moduleClass = $moduleConfig['class'];

        if (! class_exists($moduleClass)) {
            Log::warning("Module class not found: {$moduleClass}");

            return "<!-- Module class not found: {$moduleId} -->";
        }

        if (! isset($this->moduleInstances[$moduleId])) {
            $this->moduleInstances[$moduleId] = app($moduleClass);
        }

        try {
            return $this->moduleInstances[$moduleId]->render($config, $page, $article);
        } catch (\Throwable $e) {
            Log::error("Module render error [{$moduleId}]: {$e->getMessage()}");

            return "<!-- Module error: {$moduleId} -->";
        }
    }

    public function getAvailableModules(): array
    {
        return config('modules', []);
    }
}
