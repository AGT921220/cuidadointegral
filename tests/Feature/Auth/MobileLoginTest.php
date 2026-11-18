<?php

namespace Tests\Feature\Auth;

use App\Features\Permissions\Constants\RoleTypes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MobileLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_login_returns_a_bearer_token(): void
    {
        Role::findOrCreate(RoleTypes::USUARIO, 'web');

        $user = User::factory()->create([
            'nickname' => 'campo',
            'password' => 'password',
        ]);
        $user->assignRole(RoleTypes::USUARIO);

        $response = $this->postJson('/api/auth/login', [
            'nickname' => 'Campo',
            'password' => 'password',
            'device_name' => 'pixel',
        ]);

        $response->assertOk();
        $response->assertJsonPath('token_type', 'Bearer');
        $response->assertJsonPath('user.nickname', 'campo');
        $response->assertJsonPath('user.email', $user->email);
        $this->assertNotEmpty($response->json('token'));
    }

    public function test_a_caregiver_can_log_in(): void
    {
        Role::findOrCreate(RoleTypes::CUIDADOR, 'web');

        $user = User::factory()->create([
            'nickname' => 'cuidador',
            'password' => 'password',
        ]);
        $user->assignRole(RoleTypes::CUIDADOR);

        $this->postJson('/api/auth/login', [
            'nickname' => 'cuidador',
            'password' => 'password',
            'device_name' => 'pixel',
        ])->assertOk()
            ->assertJsonPath('user.nickname', 'cuidador');
    }

    public function test_mobile_login_requires_device_name(): void
    {
        $this->postJson('/api/auth/login', [
            'nickname' => 'campo',
            'password' => 'password',
        ])->assertUnprocessable();
    }

    public function test_mobile_login_rejects_a_user_without_app_role(): void
    {
        User::factory()->create([
            'nickname' => 'invitado',
            'password' => 'password',
        ]);

        $this->postJson('/api/auth/login', [
            'nickname' => 'invitado',
            'password' => 'password',
            'device_name' => 'pixel',
        ])->assertUnprocessable();
    }

    public function test_logout_deletes_the_current_token(): void
    {
        Role::findOrCreate(RoleTypes::USUARIO, 'web');

        $user = User::factory()->create();
        $user->assignRole(RoleTypes::USUARIO);
        $token = $user->createToken('pixel')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/auth/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Sesión cerrada correctamente.');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_authenticated_user_endpoint_hides_the_password(): void
    {
        Role::findOrCreate(RoleTypes::ADMIN, 'web');

        $user = User::factory()->create();
        $user->assignRole(RoleTypes::ADMIN);
        $token = $user->createToken('pixel')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/user')
            ->assertOk()
            ->assertJsonPath('nickname', $user->nickname)
            ->assertJsonMissingPath('password');
    }
}
