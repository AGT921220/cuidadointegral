<?php

namespace App\Features\Roles\Application;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class CreateRole
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __invoke(array $data): Role
    {
        $permissions = $data['permissions'] ?? [];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $role = Role::query()->create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($permissions);

        return $role;
    }
}
