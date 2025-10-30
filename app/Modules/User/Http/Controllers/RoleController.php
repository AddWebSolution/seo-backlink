<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name'),
            ],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name'),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($request->permissions);

        return response()->json([
            'message' => 'Role created successfully',
        ]);
    }

    public function update(Request $request, $roleId)
    {
        $role = Role::find($roleId);

        if (!$role) {
            return response()->json([
                'message' => 'Role not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name'),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $role->update([
            'name' => $request->name,
        ]);

        $role->syncPermissions($request->permissions);

        return response()->json([
            'message' => 'Role updated successfully',
            'data' => $role->load('permissions'),
        ]);
    }


    public function roleList()
    {
        $roles = Role::select('id', 'name', 'guard_name')
        ->withCount('permissions')
        ->get();
        return response()->json([
            'data' => $roles,
        ]);
    }

    public function permissionsList()
    {
        $permissions = Permission::select('id','name', 'guard_name')->get();

        return response()->json([
            'data' => $permissions,
        ]);
    }

    public function rolePermission($roleId)
    {
        $role = Role::find($roleId);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $role->load('permissions');

        return response()->json([
            'success' => true,
            'data' => $role
        ]);
    }
}
