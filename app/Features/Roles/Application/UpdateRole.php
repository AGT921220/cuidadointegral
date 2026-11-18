<?php

namespace App\Features\Roles\Application;

use App\Features\Permissions\Constants\RoleTypes;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class UpdateRole
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __invoke(Role $role, array $data): Role
    {
        if ($role->name === RoleTypes::ADMIN) {
            throw ValidationException::withMessages([
                'name' => 'El rol Admin no se puede modificar.',
            ]);
        }

        if ($role->name === RoleTypes::USUARIO && $data['name'] !== $role->name) {
            throw ValidationException::withMessages([
                'name' => 'El nombre del rol Usuario es fijo.',
            ]);
        }

        $permissions = $data['permissions'] ?? [];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $role->name = $data['name'];
        $role->save();
        $role->syncPermissions($permissions);

        return $role;
    }
}
