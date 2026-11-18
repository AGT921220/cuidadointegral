<?php

namespace App\Features\Users\Application;

use App\Models\User;
use Illuminate\Support\Str;

final class UpdateUser
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __invoke(User $user, array $data): User
    {
        $roles = $data['roles'];
        unset($data['roles'], $data['password_confirmation']);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $data['nickname'] = Str::lower(trim((string) $data['nickname']));

        $user->update($data);
        $user->syncRoles($roles);

        return $user;
    }
}
