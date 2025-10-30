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

    public function roleList()
    {
        $roles = Role::select('name', 'guard_name')->get();

        return response()->json([
            'data' => $roles,
        ]);
    }

    public function permissionsList()
    {
        $permissions = Permission::select('name', 'guard_name')->get();

        return response()->json([
            'data' => $permissions,
        ]);
    }
}