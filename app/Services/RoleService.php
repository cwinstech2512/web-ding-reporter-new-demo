<?php

namespace App\Services;

use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Support\Str;

class RoleService
{
    public function getRoles()
    {
        $roles = RoleResource::collection(Role::all());

        return response()->json([
            'roles' => $roles
        ]);
    }

    public function storeRole($request)
    {
        $role = Role::create([
            'name' => $request->params['name'],
            'slug' => Str::slug($request->params['name'], '-')
        ]);

        $menus = $request->params['menus'];
        $role->menus()->sync($menus);

        return response()->json([
            'role' => new RoleResource($role)
        ]);
    }

    public function updateRole($request, $role)
    {
        $role->update([
            'name' => $request->params['name'],
            'slug' => Str::slug($request->params['name'], '-')
        ]);
        $role->menus()->sync($request->params['menus']);

        return response()->json([
            'role' => new RoleResource($role)
        ]);
    }
}