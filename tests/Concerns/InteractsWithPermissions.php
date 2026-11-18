<?php

namespace Tests\Concerns;

use App\Models\User;
use Spatie\Permission\Models\Permission;

trait InteractsWithPermissions
{
    /**
     * @param  list<string>  $permissions
     */
    protected function actingAsUserWithPermissions(array $permissions): User
    {
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $user = User::factory()->create();
        $user->givePermissionTo($permissions);
        $this->actingAs($user);

        return $user;
    }
}
