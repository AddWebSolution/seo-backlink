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
            'client' => ['view', 'create', 'update', 'delete'],
            'report' => ['view', 'create', 'update', 'delete'],
            'clientdomain' => ['view', 'create', 'update', 'delete'],
            'rivaldomain' => ['view', 'create', 'update', 'delete'],
            'keyword' => ['view', 'create', 'update', 'delete'],
            'keyworddatum' => ['view', 'create', 'update', 'delete'],
            'keywordreport' => ['view', 'create', 'update', 'delete'],
            'backlinkdatum' => ['view', 'create', 'update', 'delete'],
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
                'view clientdomain',
                'view rivaldomain',
                'view keyword',
                'view keyworddatum',
                'view keywordreport',
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
