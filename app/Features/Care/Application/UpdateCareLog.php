<?php

namespace App\Features\Care\Application;

use App\Features\Permissions\Constants\RoleTypes;
use App\Models\CareLog;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

final class UpdateCareLog
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function __invoke(User $user, CareLog $log, array $data): array
    {
        if (! $user->hasRole(RoleTypes::ADMIN) && (int) $log->user_id !== (int) $user->id) {
            throw new AuthorizationException('Solo el autor puede modificar esta anotación.');
        }

        $log->fill([
            'kind' => $data['kind'] ?? $log->kind,
            'body' => $data['body'] ?? $log->body,
        ])->save();

        $log->load('user:id,name');

        return $log->toCareArray();
    }
}
