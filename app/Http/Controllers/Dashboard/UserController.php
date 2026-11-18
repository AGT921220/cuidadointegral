<?php

namespace App\Http\Controllers\Dashboard;

use App\Features\Permissions\Constants\PermissionTypes;
use App\Features\Users\Application\CreateUser;
use App\Features\Users\Application\DeleteUser;
use App\Features\Users\Application\UpdateUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

final class UserController extends Controller
{
    public function __construct(
        private readonly CreateUser $createUser,
        private readonly UpdateUser $updateUser,
        private readonly DeleteUser $deleteUser,
    ) {}

    public function index(): View
    {
        $this->authorize(PermissionTypes::USERS_VIEW);

        return view('users.index');
    }

    public function create(): View
    {
        $this->authorize(PermissionTypes::USERS_CREATE);

        return view('users.create', [
            'roles' => $this->roles(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize(PermissionTypes::USERS_CREATE);

        ($this->createUser)($request->validated());

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user): View
    {
        $this->authorize(PermissionTypes::USERS_UPDATE);

        return view('users.edit', [
            'user' => $user,
            'roles' => $this->roles(),
            'selectedRoles' => $user->roles->pluck('name')->all(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize(PermissionTypes::USERS_UPDATE);

        ($this->updateUser)($user, $request->validated());

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize(PermissionTypes::USERS_DELETE);

        ($this->deleteUser)($user, (int) auth()->id());

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Role>
     */
    private function roles()
    {
        return Role::query()->orderBy('name')->get(['id', 'name']);
    }
}
