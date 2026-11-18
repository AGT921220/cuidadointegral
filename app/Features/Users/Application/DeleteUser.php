<?php

namespace App\Features\Users\Application;

use App\Models\User;
use Illuminate\Validation\ValidationException;

final class DeleteUser
{
    public function __invoke(User $user, int $actorId): void
    {
        if ($user->id === $actorId) {
            throw ValidationException::withMessages([
                'user' => 'No puedes eliminar tu propio usuario.',
            ]);
        }

        $user->tokens()->delete();
        $user->delete();
    }
}
