<?php

namespace App\Http\Controllers\Dashboard;

use App\Features\Permissions\Constants\PermissionTypes;
use App\Features\Permissions\PermissionHandler;
use App\Features\Roles\Application\CreateRole;
use App\Features\Roles\Application\DeleteRole;
use App\Features\Roles\Application\UpdateRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

final class RoleController extends Controller
{
    public function __construct(
        private readonly CreateRole $createRole,
        private readonly UpdateRole $updateRole,
        private readonly DeleteRole $deleteRole,
        private readonly PermissionHandler $permissionHandler,
    ) {}

    public function index(): View
    {
        $this->authorize(PermissionTypes::ROLES_VIEW);

        return view('roles.index');
    }

    public function create(): View
    {
        $this->authorize(PermissionTypes::ROLES_CREATE);

        return view('roles.create', [
            'permissionGroups' => $this->permissionHandler->grouped(),
            'selectedPermissions' => [],
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->authorize(PermissionTypes::ROLES_CREATE);

        ($this->createRole)($request->validated());

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol creado correctamente.');
    }

    public function edit(Role $role): View
    {
        $this->authorize(PermissionTypes::ROLES_UPDATE);

        return view('roles.edit', [
            'role' => $role,
            'permissionGroups' => $this->permissionHandler->grouped(),
            'selectedPermissions' => $role->permissions->pluck('name')->all(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->authorize(PermissionTypes::ROLES_UPDATE);

        ($this->updateRole)($role, $request->validated());

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize(PermissionTypes::ROLES_DELETE);

        ($this->deleteRole)($role);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol eliminado correctamente.');
    }
}
