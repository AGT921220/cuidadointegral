<?php

namespace App\Features\Users\Application;

use App\Models\User;
use Illuminate\Support\Str;

final class CreateUser
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __invoke(array $data): User
    {
        $roles = $data['roles'];
        unset($data['roles'], $data['password_confirmation']);

        $data['nickname'] = Str::lower(trim((string) $data['nickname']));

        $user = User::query()->create($data);
        $user->syncRoles($roles);

        return $user;
    }
}
