<?php

namespace App\Features\Roles\Application;

use App\Features\Permissions\Constants\RoleTypes;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

final class DeleteRole
{
    public function __invoke(Role $role): void
    {
        if (in_array($role->name, RoleTypes::fixedNames(), true)) {
            throw ValidationException::withMessages([
                'role' => 'Este rol del sistema no se puede eliminar.',
            ]);
        }

        if ($role->users()->exists()) {
            throw ValidationException::withMessages([
                'role' => 'El rol tiene usuarios asignados.',
            ]);
        }

        $role->delete();
    }
}
