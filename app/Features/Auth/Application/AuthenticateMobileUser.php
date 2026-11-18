<?php

namespace App\Features\Auth\Application;

use App\Features\Permissions\Constants\RoleTypes;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class AuthenticateMobileUser
{
    public function __invoke(string $nickname, string $password): User
    {
        $user = User::query()
            ->with('roles')
            ->where('nickname', Str::lower(trim($nickname)))
            ->first();

        if ($user === null || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'nickname' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        if (! $user->hasAnyRole(RoleTypes::mobile())) {
            throw ValidationException::withMessages([
                'nickname' => ['Este usuario no puede entrar a la aplicación.'],
            ]);
        }

        return $user;
    }
}
