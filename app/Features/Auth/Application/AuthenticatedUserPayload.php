<?php

namespace App\Features\Auth\Application;

use App\Models\User;

final class AuthenticatedUserPayload
{
    /**
     * @return array<string, mixed>
     */
    public function __invoke(User $user): array
    {
        $user->loadMissing('roles');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'nickname' => $user->nickname,
            'email' => $user->email,
            'roles' => $user->getRoleNames()->values(),
            'permissions' => $user->getAllPermissions()->pluck('name')->values(),
        ];
    }
}
