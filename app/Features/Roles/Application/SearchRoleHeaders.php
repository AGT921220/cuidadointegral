<?php

namespace App\Features\Roles\Application;

use App\Features\Shared\Query\BuilderFilter;
use Spatie\Permission\Models\Role;

final class SearchRoleHeaders
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
            builder: Role::query()
                ->with('permissions:id,name')
                ->select('roles.id', 'roles.name'),
            modifiers: $modifiers,
            draw: $draw,
        );

        $result['data'] = $result['data']->map(function (Role $role): array {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->implode(', '),
            ];
        })->values();

        return $result;
    }
}
