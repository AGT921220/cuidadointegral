<?php

namespace App\Console\Commands;

use App\Features\Permissions\Constants\RoleTypes;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

#[Signature('initial-setup')]
#[Description('Crea el usuario administrador inicial')]
class InitialSetupCommand extends Command
{
    /**
     * @var list<array{name: string, nickname: string, email: string, role: string}>
     */
    private const USERS = [
        [
            'name' => 'Administrador',
            'nickname' => 'admin',
            'email' => 'admin@admin.com',
            'role' => RoleTypes::ADMIN,
        ],
    ];

    public function handle(): int
    {
        $password = 'admin';

        foreach (self::USERS as $userData) {
            $role = Role::findOrCreate($userData['role'], 'web');

            $user = User::query()
                ->withTrashed()
                ->where(function ($query) use ($userData): void {
                    $query->where('email', $userData['email'])
                        ->orWhere('nickname', $userData['nickname']);
                })
                ->first();

            if ($user === null) {
                $user = User::query()->create([
                    'name' => $userData['name'],
                    'nickname' => $userData['nickname'],
                    'email' => $userData['email'],
                    'password' => $password,
                    'email_verified_at' => now(),
                ]);
                $user->syncRoles([$role]);
                $this->components->info("Creado: {$userData['nickname']} ({$userData['role']})");

                continue;
            }

            if ($user->trashed()) {
                $user->restore();
            }

            $user->forceFill([
                'name' => $userData['name'],
                'nickname' => $userData['nickname'],
                'email' => $userData['email'],
                'password' => $password,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();

            $user->syncRoles([$role]);
            $this->components->info("Actualizado: {$userData['nickname']} ({$userData['role']})");
        }

        return self::SUCCESS;
    }
}
