@extends('layouts.app')

@section('title', 'Usuarios')
@section('page-title', 'Usuarios')

@section('page-actions')
    @can(\App\Features\Permissions\Constants\PermissionTypes::USERS_CREATE)
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-2"></i>
            Nuevo usuario
        </a>
    @endcan
@endsection

@section('content')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-users me-2"></i>
                    Listado de usuarios
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table
                        id="users-table"
                        class="table table-vcenter card-table w-100"
                        data-url="{{ route('user-headers.index') }}"
                    >
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Usuario</th>
                                <th>Correo</th>
                                <th>Roles</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/modules/users/index.js')
@endpush
