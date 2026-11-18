<?php

namespace Tests\Feature\Auth;

use App\Features\Permissions\Constants\PermissionTypes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class WebLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/dashboard');
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_user_with_panel_access_can_login(): void
    {
        Permission::findOrCreate(PermissionTypes::DASHBOARD_VIEW, 'web');

        $user = User::factory()->create([
            'nickname' => 'admin',
            'password' => 'password',
        ]);
        $user->givePermissionTo(PermissionTypes::DASHBOARD_VIEW);

        $this->post('/login', [
            'nickname' => 'admin',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Bienvenido');
    }

    public function test_user_without_panel_access_cannot_login(): void
    {
        User::factory()->create([
            'nickname' => 'campo',
            'password' => 'password',
        ]);

        $this->post('/login', [
            'nickname' => 'campo',
            'password' => 'password',
        ])->assertSessionHasErrors('nickname');
    }
}
