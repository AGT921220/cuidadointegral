<?php

namespace App\Providers;

use App\Features\Permissions\Constants\RoleTypes;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        Horizon::auth(function ($request): bool {
            return Gate::check('viewHorizon', [$request->user()]);
        });
    }

    protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user): bool {
            return $user !== null && $user->hasRole(RoleTypes::ADMIN);
        });
    }
}
