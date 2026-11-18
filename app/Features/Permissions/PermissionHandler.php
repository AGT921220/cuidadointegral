<?php

namespace App\Features\Permissions;

use App\Features\Permissions\Constants\PermissionTypes;

final class PermissionHandler
{
    /**
     * @return list<string>
     */
    public function getAllPermissions(): array
    {
        return [
            PermissionTypes::DASHBOARD_VIEW,
            PermissionTypes::USERS_VIEW,
            PermissionTypes::USERS_CREATE,
            PermissionTypes::USERS_UPDATE,
            PermissionTypes::USERS_DELETE,
            PermissionTypes::ROLES_VIEW,
            PermissionTypes::ROLES_CREATE,
            PermissionTypes::ROLES_UPDATE,
            PermissionTypes::ROLES_DELETE,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getPermissionNamesInSpanish(): array
    {
        return [
            PermissionTypes::DASHBOARD_VIEW => 'Ver el panel',
            PermissionTypes::USERS_VIEW => 'Ver usuarios',
            PermissionTypes::USERS_CREATE => 'Crear usuarios',
            PermissionTypes::USERS_UPDATE => 'Editar usuarios',
            PermissionTypes::USERS_DELETE => 'Eliminar usuarios',
            PermissionTypes::ROLES_VIEW => 'Ver roles',
            PermissionTypes::ROLES_CREATE => 'Crear roles',
            PermissionTypes::ROLES_UPDATE => 'Editar roles',
            PermissionTypes::ROLES_DELETE => 'Eliminar roles',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getModuleLabels(): array
    {
        return [
            'dashboard' => 'Panel',
            'users' => 'Usuarios',
            'roles' => 'Roles',
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function grouped(): array
    {
        $labels = $this->getPermissionNamesInSpanish();
        $modules = $this->getModuleLabels();
        $grouped = [];

        foreach ($this->getAllPermissions() as $permission) {
            $module = strstr($permission, '.', true) ?: $permission;
            $moduleLabel = $modules[$module] ?? $module;
            $grouped[$moduleLabel][$permission] = $labels[$permission] ?? $permission;
        }

        return $grouped;
    }
}
