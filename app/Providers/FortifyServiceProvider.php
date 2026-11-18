<?php

namespace App\Providers;

use App\Features\Permissions\Constants\PermissionTypes;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::loginView(fn () => view('auth.login'));

        Fortify::authenticateUsing(function (Request $request): ?User {
            $nickname = Str::lower(trim((string) $request->input('nickname')));

            $user = User::query()->where('nickname', $nickname)->first();

            if ($user === null || ! Hash::check((string) $request->input('password'), $user->password)) {
                return null;
            }

            if (! $user->can(PermissionTypes::DASHBOARD_VIEW)) {
                return null;
            }

            return $user;
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(
                Str::lower((string) $request->input(Fortify::username())).'|'.$request->ip()
            );

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
