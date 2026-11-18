<?php

namespace Tests\Feature\Users;

use App\Features\Permissions\Constants\PermissionTypes;
use App\Features\Permissions\Constants\RoleTypes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\Concerns\InteractsWithPermissions;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use InteractsWithPermissions;
    use RefreshDatabase;

    public function test_authorized_user_can_view_the_index(): void
    {
        $this->actingAsUserWithPermissions([PermissionTypes::USERS_VIEW]);

        $this->get(route('users.index'))
            ->assertOk()
            ->assertSee('users-table', false)
            ->assertSee(route('user-headers.index'), false);
    }

    public function test_unauthorized_user_receives_forbidden(): void
    {
        Permission::findOrCreate(PermissionTypes::USERS_VIEW, 'web');
        $this->actingAs(User::factory()->create());

        $this->get(route('users.index'))->assertForbidden();
    }

    public function test_authorized_user_can_create_a_user(): void
    {
        Role::findOrCreate(RoleTypes::USUARIO, 'web');
        $this->actingAsUserWithPermissions([
            PermissionTypes::USERS_VIEW,
            PermissionTypes::USERS_CREATE,
        ]);

        $this->post(route('users.store'), [
            'name' => 'Ana Campo',
            'nickname' => 'AnaCampo',
            'email' => 'ana@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'roles' => [RoleTypes::USUARIO],
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'nickname' => 'anacampo',
            'email' => 'ana@example.com',
        ]);
    }

    public function test_authorized_user_can_update_a_user_without_changing_the_password(): void
    {
        Role::findOrCreate(RoleTypes::USUARIO, 'web');
        $this->actingAsUserWithPermissions([PermissionTypes::USERS_UPDATE]);

        $user = User::factory()->create([
            'nickname' => 'ana',
            'password' => 'password',
        ]);
        $user->assignRole(RoleTypes::USUARIO);
        $original = $user->password;

        $this->put(route('users.update', $user), [
            'name' => 'Ana Editada',
            'nickname' => 'ana',
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => '',
            'roles' => [RoleTypes::USUARIO],
        ])->assertRedirect(route('users.index'));

        $user->refresh();
        $this->assertSame('Ana Editada', $user->name);
        $this->assertSame($original, $user->password);
    }

    public function test_user_cannot_delete_themselves(): void
    {
        $actor = $this->actingAsUserWithPermissions([PermissionTypes::USERS_DELETE]);

        $this->delete(route('users.destroy', $actor))
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', ['id' => $actor->id, 'deleted_at' => null]);
    }

    public function test_headers_return_the_datatable_envelope(): void
    {
        Role::findOrCreate(RoleTypes::USUARIO, 'web');
        $this->actingAsUserWithPermissions([PermissionTypes::USERS_VIEW]);

        $user = User::factory()->create(['name' => 'Beatriz']);
        $user->assignRole(RoleTypes::USUARIO);

        $this->getJson(route('user-headers.index', [
            'offset' => 0,
            'limit' => 10,
            'draw' => 2,
            'order_by' => 'users.name',
            'order_direction' => 'asc',
        ]))
            ->assertOk()
            ->assertJsonPath('draw', 2)
            ->assertJsonFragment(['name' => 'Beatriz']);
    }
}
