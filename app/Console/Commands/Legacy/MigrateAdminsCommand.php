<?php

namespace App\Console\Commands\Legacy;

use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MigrateAdminsCommand extends BaseMigrationCommand
{
    protected $signature = 'migrate:legacy:admins
                            {--fresh : Truncate admins table before migrating}
                            {--with-roles : Also migrate roles from yetkiler table}';

    protected $description = 'Migrate admin users from legacy yonetici table and roles from yetkiler';

    /**
     * Permission name mapping from Turkish to English.
     */
    protected array $permissionMap = [
        'kullanicilar' => 'users.edit', 'kullanicilars' => 'users.delete',
        'ayarlar' => 'settings.edit', 'ayarlars' => 'settings.delete',
        'sayarlar' => 'pages.edit', 'sayarlars' => 'pages.delete',
        'anketler' => 'surveys.edit', 'anketlers' => 'surveys.delete',
        'icerikler' => 'content.edit', 'iceriklers' => 'content.delete',
        'reklamlar' => 'ads.edit', 'reklamlars' => 'ads.delete',
        'diller' => 'languages.edit', 'dillers' => 'languages.delete',
        'mayarlar' => 'site-settings.edit', 'mayarlars' => 'site-settings.delete',
    ];

    public function handle(): int
    {
        $this->info('👤 Starting Admins Migration (yonetici → admins)...');

        if (!$this->checkLegacyConnection()) {
            return self::FAILURE;
        }

        if ($this->option('fresh') && $this->confirm('This will delete all existing admins and roles. Continue?')) {
            Admin::truncate();
            DB::table('model_has_roles')->truncate();
            DB::table('model_has_permissions')->truncate();
            $this->warn('Admins and role assignments truncated.');
        }

        // Step 1: Migrate roles
        if ($this->option('with-roles')) {
            $this->migrateRoles();
        }

        // Step 2: Migrate admins
        $this->migrateAdmins();

        $this->printSummary('Admins');

        return self::SUCCESS;
    }

    protected function migrateRoles(): void
    {
        $this->info('  📋 Migrating roles from yetkiler...');

        // Create all permissions first
        $allPermissions = array_values($this->permissionMap);
        foreach ($allPermissions as $permName) {
            Permission::firstOrCreate(
                ['name' => $permName, 'guard_name' => 'admin']
            );
        }

        // Also create a super-admin role
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super-admin', 'guard_name' => 'admin']
        );
        $superAdmin->givePermissionTo(Permission::where('guard_name', 'admin')->get());

        $legacyRoles = $this->legacy('yetkiler')->get();

        foreach ($legacyRoles as $legacyRole) {
            try {
                $roleName = $this->toUtf8($legacyRole->yetkiadi ?? 'role-' . $legacyRole->id);
                $roleName = \Illuminate\Support\Str::slug($roleName);

                if (empty($roleName)) {
                    $roleName = 'role-' . $legacyRole->id;
                }

                $role = Role::firstOrCreate(
                    ['name' => $roleName, 'guard_name' => 'admin']
                );

                // Assign permissions based on legacy columns
                $permissions = [];
                foreach ($this->permissionMap as $legacyField => $permName) {
                    if (isset($legacyRole->$legacyField) && $legacyRole->$legacyField) {
                        $permissions[] = $permName;
                    }
                }

                // Also check yetkijson for any JSON-based permissions
                if (!empty($legacyRole->yetkijson)) {
                    $jsonPerms = json_decode($legacyRole->yetkijson, true);
                    if (is_array($jsonPerms)) {
                        foreach ($jsonPerms as $key => $value) {
                            if ($value && isset($this->permissionMap[$key])) {
                                $permissions[] = $this->permissionMap[$key];
                            }
                        }
                    }
                }

                if (!empty($permissions)) {
                    $role->syncPermissions(array_unique($permissions));
                }

                $this->storeLegacyMapping($legacyRole->id, $role->id, 'role');
                $this->info("    ✅ Role: {$roleName} ({$role->permissions->count()} permissions)");
            } catch (\Exception $e) {
                $this->error("    ❌ Failed to migrate role ID {$legacyRole->id}: " . $e->getMessage());
            }
        }
    }

    protected function migrateAdmins(): void
    {
        $this->info('  👤 Migrating admin users...');

        $legacyAdmins = $this->legacy('yonetici')->get();

        if ($legacyAdmins->isEmpty()) {
            $this->warn('No admin users found in legacy database.');
            return;
        }

        $bar = $this->output->createProgressBar($legacyAdmins->count());
        $bar->start();

        foreach ($legacyAdmins as $legacyAdmin) {
            try {
                $username = $this->toUtf8($legacyAdmin->adsoyad ?? '');
                $email = $legacyAdmin->email ?? null;

                // Generate a unique email if missing
                if (empty($email)) {
                    $email = \Illuminate\Support\Str::slug($username) . '@legacy.local';
                }

                // Ensure unique email
                $originalEmail = $email;
                $counter = 1;
                while (Admin::where('email', $email)->exists()) {
                    $email = $counter . '-' . $originalEmail;
                    $counter++;
                }

                $admin = Admin::updateOrCreate(
                    ['username' => \Illuminate\Support\Str::slug($username) ?: 'admin-' . $legacyAdmin->id],
                    [
                        'name' => $username,
                        'email' => $email,
                        'password' => 'legacy-placeholder', // Will not work for login
                        'legacy_password' => $legacyAdmin->sifre ?? null, // MD5 hash
                        'last_login_ip' => $legacyAdmin->ipson ?? null,
                        'last_login_at' => $this->parseDate($legacyAdmin->enson ?? null),
                        'created_at' => $this->parseDate($legacyAdmin->tarih ?? null) ?? now(),
                    ]
                );

                // Assign role if exists
                if (!empty($legacyAdmin->yetki)) {
                    $newRoleId = $this->mapLegacyId((int) $legacyAdmin->yetki, 'role');
                    if ($newRoleId) {
                        $role = Role::find($newRoleId);
                        if ($role) {
                            $admin->assignRole($role);
                        }
                    }
                }

                // If admin type indicates super-admin
                if (isset($legacyAdmin->admin) && $legacyAdmin->admin == '1') {
                    $admin->assignRole('super-admin');
                }

                $this->storeLegacyMapping($legacyAdmin->id, $admin->id, 'admin');
                $this->migrated++;
            } catch (\Exception $e) {
                $this->failed++;
                $this->newLine();
                $this->error("Failed to migrate admin ID {$legacyAdmin->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
    }
}
