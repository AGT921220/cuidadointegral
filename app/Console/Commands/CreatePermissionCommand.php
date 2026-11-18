<?php

namespace App\Console\Commands;

use App\Features\Permissions\PermissionHandler;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

#[Signature('permissions:create')]
#[Description('Crea y actualiza los permisos del sistema')]
class CreatePermissionCommand extends Command
{
    public function handle(
        PermissionHandler $permissionHandler,
        PermissionRegistrar $permissionRegistrar,
    ): int {
        try {
            $permissionRegistrar->forgetCachedPermissions();

            $permissions = $permissionHandler->getAllPermissions();

            foreach ($permissions as $permissionName) {
                Permission::findOrCreate($permissionName, 'web');
            }

            $permissionRegistrar->forgetCachedPermissions();

            $this->components->info(count($permissions).' permisos procesados correctamente.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);

            $this->components->error('No fue posible crear los permisos: '.$exception->getMessage());

            return self::FAILURE;
        }
    }
}
