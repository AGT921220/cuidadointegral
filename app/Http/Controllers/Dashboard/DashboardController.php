<?php

namespace App\Http\Controllers\Dashboard;

use App\Features\Permissions\Constants\PermissionTypes;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function index(): View
    {
        $this->authorize(PermissionTypes::DASHBOARD_VIEW);

        return view('dashboard');
    }
}
