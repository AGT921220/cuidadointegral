<?php

namespace Tests\Feature\Roles;

use App\Features\Permissions\Constants\PermissionTypes;
use App\Features\Permissions\Constants\RoleTypes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\Concerns\InteractsWithPermissions;
use Tests\TestCase;

class RoleCrudTest extends TestCase
{
    use InteractsWithPermissions;
    use RefreshDatabase;

    public function test_authorized_user_can_view_the_index(): void
    {
        $this->actingAsUserWithPermissions([PermissionTypes::ROLES_VIEW]);

        $this->get(route('roles.index'))
            ->assertOk()
            ->assertSee('roles-table', false)
            ->assertSee(route('role-headers.index'), false);
    }

    public function test_authorized_user_can_create_a_role(): void
    {
        $this->actingAsUserWithPermissions([
            PermissionTypes::ROLES_VIEW,
            PermissionTypes::ROLES_CREATE,
        ]);

        $this->post(route('roles.store'), [
            'name' => 'Supervisor',
            'permissions' => [PermissionTypes::USERS_VIEW],
        ])->assertRedirect(route('roles.index'));

        $role = Role::query()->where('name', 'Supervisor')->first();
        $this->assertNotNull($role);
        $this->assertTrue($role->hasPermissionTo(PermissionTypes::USERS_VIEW));
    }

    public function test_admin_role_cannot_be_updated(): void
    {
        $this->actingAsUserWithPermissions([PermissionTypes::ROLES_UPDATE]);
        $admin = Role::findOrCreate(RoleTypes::ADMIN, 'web');

        $this->put(route('roles.update', $admin), [
            'name' => 'Otro',
            'permissions' => [],
        ])->assertSessionHasErrors('name');
    }

    public function test_system_role_cannot_be_deleted(): void
    {
        $this->actingAsUserWithPermissions([PermissionTypes::ROLES_DELETE]);
        $role = Role::findOrCreate(RoleTypes::USUARIO, 'web');

        $this->delete(route('roles.destroy', $role))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseHas('roles', ['name' => RoleTypes::USUARIO]);
    }

    public function test_role_with_users_cannot_be_deleted(): void
    {
        $this->actingAsUserWithPermissions([PermissionTypes::ROLES_DELETE]);
        $role = Role::findOrCreate('Supervisor', 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->delete(route('roles.destroy', $role))
            ->assertSessionHasErrors('role');
    }

    public function test_headers_return_the_datatable_envelope(): void
    {
        $this->actingAsUserWithPermissions([PermissionTypes::ROLES_VIEW]);
        Role::findOrCreate('Supervisor', 'web');

        $this->getJson(route('role-headers.index', [
            'draw' => 3,
            'offset' => 0,
            'limit' => 10,
        ]))
            ->assertOk()
            ->assertJsonPath('draw', 3)
            ->assertJsonFragment(['name' => 'Supervisor']);
    }
}
