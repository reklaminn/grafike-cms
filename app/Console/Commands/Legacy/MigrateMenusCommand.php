<?php

namespace App\Console\Commands\Legacy;

use App\Models\Menu;
use App\Models\MenuItem;

class MigrateMenusCommand extends BaseMigrationCommand
{
    protected $signature = 'migrate:legacy:menus
                            {--fresh : Truncate menus tables before migrating}';

    protected $description = 'Migrate menus from legacy menuler table';

    public function handle(): int
    {
        $this->info('📋 Starting Menus Migration (menuler → menus + menu_items)...');

        if (!$this->checkLegacyConnection()) {
            return self::FAILURE;
        }

        if ($this->option('fresh') && $this->confirm('This will delete all existing menus. Continue?')) {
            MenuItem::truncate();
            Menu::truncate();
            $this->warn('Menu tables truncated.');
        }

        $legacyMenus = $this->legacy('menuler')->orderBy('id')->get();

        if ($legacyMenus->isEmpty()) {
            $this->warn('No menu items found in legacy database.');
            return self::SUCCESS;
        }

        $this->info("  Found {$legacyMenus->count()} menu items to migrate.");

        // Step 1: Group menus by their root parent (alt=1 indicates root menu groups)
        $rootMenus = $legacyMenus->filter(fn($m) => ($m->alt ?? '0') == '1');

        // Create a menu container for each unique language + root group
        $menuContainers = [];

        foreach ($rootMenus as $rootMenu) {
            $langId = $this->mapLegacyId((int) ($rootMenu->lang ?? 240), 'language') ?? 1;
            $menuName = $this->toUtf8($rootMenu->isim ?? 'Menu');
            $location = $this->resolveMenuLocation($rootMenu);

            $menu = Menu::create([
                'name' => $menuName,
                'slug' => $this->generateSlug($menuName, 'menus', 'slug'),
                'location' => $location,
                'language_id' => $langId,
                'is_active' => (bool) ($rootMenu->durum ?? 1),
            ]);

            $menuContainers[$rootMenu->id] = $menu;
            $this->storeLegacyMapping($rootMenu->id, $menu->id, 'menu');
        }

        // If no root menus found, create default ones based on language groupings
        if ($menuContainers === []) {
            $languages = $legacyMenus->pluck('lang')->unique();
            foreach ($languages as $langCode) {
                $langId = $this->mapLegacyId((int) ($langCode ?? 240), 'language') ?? 1;
                $menu = Menu::create([
                    'name' => 'Ana Menü',
                    'slug' => $this->generateSlug('ana-menu-' . $langCode, 'menus', 'slug'),
                    'location' => 'header',
                    'language_id' => $langId,
                    'is_active' => true,
                ]);
                $menuContainers['lang-' . $langCode] = $menu;
            }
        }

        // Step 2: Create menu items
        $bar = $this->output->createProgressBar($legacyMenus->count());
        $bar->start();

        // First pass: create all items
        foreach ($legacyMenus as $legacyItem) {
            try {
                if (($legacyItem->alt ?? '0') == '1') {
                    // This is a menu container, already handled
                    $bar->advance();
                    $this->skipped++;
                    continue;
                }

                // Determine which menu container this belongs to
                $menuId = $this->findMenuContainer($legacyItem, $menuContainers, $legacyMenus);

                if (!$menuId) {
                    $this->skipped++;
                    $bar->advance();
                    continue;
                }

                // Map page reference if the link corresponds to a legacy page
                $pageId = null;
                $url = $this->toUtf8($legacyItem->link ?? '#');

                $menuItem = MenuItem::create([
                    'menu_id' => $menuId,
                    'title' => $this->toUtf8($legacyItem->isim ?? 'Menu Item'),
                    'url' => $url,
                    'page_id' => $pageId,
                    'target' => ($legacyItem->target ?? '_self') ?: '_self',
                    'sort_order' => (int) ($legacyItem->sira ?? 0),
                    'is_active' => (bool) ($legacyItem->durum ?? 1),
                    'json_config' => !empty($legacyItem->menujson)
                        ? json_decode($legacyItem->menujson, true)
                        : null,
                    'legacy_id' => $legacyItem->id,
                ]);

                $this->storeLegacyMapping($legacyItem->id, $menuItem->id, 'menu_item');
                $this->migrated++;
            } catch (\Exception $e) {
                $this->failed++;
                $this->newLine();
                $this->error("Failed to migrate menu item ID {$legacyItem->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // Second pass: set parent relationships
        $this->info('  🔗 Setting menu item parent-child relationships...');
        $updated = 0;

        foreach ($legacyMenus as $legacyItem) {
            if (!empty($legacyItem->anasek) && $legacyItem->anasek != '0' && ($legacyItem->alt ?? '0') == '0') {
                $newItemId = $this->mapLegacyId($legacyItem->id, 'menu_item');
                $newParentId = $this->mapLegacyId((int) $legacyItem->anasek, 'menu_item');

                if ($newItemId && $newParentId) {
                    MenuItem::where('id', $newItemId)->update(['parent_id' => $newParentId]);
                    $updated++;
                }
            }
        }

        $this->info("  ✅ Updated {$updated} parent-child relationships.");
        $this->printSummary('Menus');

        return self::SUCCESS;
    }

    /**
     * Resolve the menu location based on legacy menu properties.
     */
    protected function resolveMenuLocation(object $rootMenu): string
    {
        $name = strtolower($this->toUtf8($rootMenu->isim ?? ''));

        if (str_contains($name, 'alt') || str_contains($name, 'footer')) {
            return 'footer';
        }
        if (str_contains($name, 'yan') || str_contains($name, 'side')) {
            return 'sidebar';
        }
        if (str_contains($name, 'üst') || str_contains($name, 'top') || str_contains($name, 'header')) {
            return 'header';
        }

        return 'header';
    }

    /**
     * Find the parent menu container for a menu item.
     */
    protected function findMenuContainer(object $item, array $containers, $allMenus): ?int
    {
        // Look up the hierarchy to find the root
        $parentId = $item->anasek ?? null;
        $visited = [];

        while ($parentId && !isset($visited[$parentId])) {
            $visited[$parentId] = true;

            if (isset($containers[$parentId])) {
                return $containers[$parentId]->id;
            }

            $parent = $allMenus->firstWhere('id', $parentId);
            if ($parent) {
                $parentId = $parent->anasek ?? null;
            } else {
                break;
            }
        }

        // Fallback: find by language
        $langKey = 'lang-' . ($item->lang ?? 240);
        if (isset($containers[$langKey])) {
            return $containers[$langKey]->id;
        }

        // Last resort: use the first container
        $first = reset($containers);
        return $first ? $first->id : null;
    }
}
