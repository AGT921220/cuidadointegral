<?php

namespace App\Console\Commands;

use App\Features\Permissions\Constants\RoleTypes;
use App\Features\Permissions\PermissionHandler;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

#[Signature('roles:create')]
#[Description('Crea los roles del sistema y sincroniza sus permisos')]
class CreateRolesCommand extends Command
{
    public function handle(
        PermissionHandler $permissionHandler,
        PermissionRegistrar $permissionRegistrar,
    ): int {
        $permissionRegistrar->forgetCachedPermissions();

        foreach ([RoleTypes::ADMIN, RoleTypes::CUIDADOR, RoleTypes::FAMILIAR, RoleTypes::SUPERVISOR] as $roleName) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($permissionHandler->permissionsForRole($roleName));
        }

        Role::findOrCreate(RoleTypes::USUARIO, 'web');

        $permissionRegistrar->forgetCachedPermissions();

        $this->components->info('Roles Admin, Cuidador, Familiar, Supervisor y Usuario listos.');

        return self::SUCCESS;
    }
}
