<?php

namespace App\Features\Users\Application;

use App\Features\Shared\Query\BuilderFilter;
use App\Models\User;

final class SearchUserHeaders
{
    public function __construct(
        private readonly BuilderFilter $builderFilter,
    ) {}

    /**
     * @param  array<int, mixed>  $modifiers
     * @return array<string, mixed>
     */
    public function __invoke(array $modifiers = [], int $draw = 1): array
    {
        $result = $this->builderFilter->paginate(
            builder: User::query()
                ->with('roles:id,name')
                ->select('users.id', 'users.name', 'users.nickname', 'users.email'),
            modifiers: $modifiers,
            draw: $draw,
        );

        $result['data'] = $result['data']->map(function (User $user): array {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'nickname' => $user->nickname,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->implode(', '),
            ];
        })->values();

        return $result;
    }
}
