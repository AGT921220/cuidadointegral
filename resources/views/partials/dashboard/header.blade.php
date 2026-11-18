@php
    use App\Features\Permissions\Constants\PermissionTypes;
@endphp

<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
            aria-controls="navbar-menu" aria-expanded="false" aria-label="Mostrar navegación">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-brand navbar-brand-autodark pe-0 pe-md-3">
            <a href="{{ route('dashboard') }}" class="text-reset text-decoration-none fw-bold">
                <i class="ti ti-heart-handshake me-1"></i>
                {{ config('business.name') }}
            </a>
        </div>

        <div class="navbar-nav flex-row order-md-last">
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown"
                    aria-label="Abrir menú de usuario" aria-expanded="false">
                    <span class="avatar avatar-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ auth()->user()->name }}</div>
                        <div class="mt-1 small text-secondary">
                            {{ auth()->user()->roles->first()?->name ?? 'Usuario' }}
                        </div>
                    </div>
                </a>

                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <div class="dropdown-header">
                        <strong>{{ auth()->user()->name }}</strong>
                        <div class="small text-secondary">{{ auth()->user()->email }}</div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="ti ti-logout me-2"></i>
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<header class="navbar-expand-md">
    <div id="navbar-menu" class="collapse navbar-collapse">
        <div class="navbar">
            <div class="container-xl">
                <ul class="navbar-nav">
                    @can(PermissionTypes::DASHBOARD_VIEW)
                        <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-layout-dashboard"></i>
                                </span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>
                    @endcan

                    @canany([PermissionTypes::USERS_VIEW, PermissionTypes::ROLES_VIEW])
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#navbar-admin" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" role="button" aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-settings"></i>
                                </span>
                                <span class="nav-link-title">Administración</span>
                            </a>
                            <div class="dropdown-menu">
                                @can(PermissionTypes::USERS_VIEW)
                                    <a href="{{ route('users.index') }}" class="dropdown-item">
                                        <i class="ti ti-users me-2"></i>
                                        Usuarios
                                    </a>
                                @endcan
                                @can(PermissionTypes::ROLES_VIEW)
                                    <a href="{{ route('roles.index') }}" class="dropdown-item">
                                        <i class="ti ti-shield me-2"></i>
                                        Roles
                                    </a>
                                @endcan
                            </div>
                        </li>
                    @endcanany
                </ul>
            </div>
        </div>
    </div>
</header>
