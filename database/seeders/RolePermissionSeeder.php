<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    protected $modulePermissionService;

    public function getModulesWithPermissions(): array
    {
        return [
            'client' => ['view', 'create', 'update', 'delete', 'import','export'],
            'report' => ['view', 'create', 'update', 'delete', 'import','export'],
            'clientdomain' => ['view', 'create', 'update', 'delete', 'import','export'],
            'dashboard' => ['view', 'create', 'update', 'delete', 'import','export'],
            'rivaldomain' => ['view', 'create', 'update', 'delete', 'import','export'],
            'keyword' => ['view', 'create', 'update', 'delete', 'import','export'],
            'keyworddatum' => ['view', 'create', 'update', 'delete', 'import','export'],
            'keywordreport' => ['view', 'create', 'update', 'delete', 'import','export'],
            'backlinkdatum' => ['view', 'create', 'update', 'delete', 'import','export'],
        ];
    }

    public function run()
    {
        $permissions = $this->getModulesWithPermissions();

        foreach ($permissions as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$action} {$module}"]);
            }
        }

        $roles = [
            'super_admin' => Permission::all()->pluck('name')->toArray(),
            'client' => [
                'view report',
                'view dashboard',
                'export report',
                'view clientdomain',
                'update clientdomain',
                'create clientdomain',
                'delete clientdomain',
                'import clientdomain',
                'view rivaldomain',
                'update rivaldomain',
                'create rivaldomain',
                'delete rivaldomain',
                'import rivaldomain',
                'view keyword',
                'create keyword',
                'delete keyword',
                'update keyword',
                'view keyworddatum',
                'view keywordreport',
                'export keywordreport',
                'view backlinkdatum',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        $adminUser = User::where('role', 'super_admin')->first();
        if ($adminUser) {
            $adminUser->assignRole('super_admin');
        }
    }
}
