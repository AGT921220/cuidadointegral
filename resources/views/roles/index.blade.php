@extends('layouts.app')

@section('title', 'Roles')
@section('page-title', 'Roles')

@section('page-actions')
    @can(\App\Features\Permissions\Constants\PermissionTypes::ROLES_CREATE)
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-2"></i>
            Nuevo rol
        </a>
    @endcan
@endsection

@section('content')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-shield me-2"></i>
                    Listado de roles
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table
                        id="roles-table"
                        class="table table-vcenter card-table w-100"
                        data-url="{{ route('role-headers.index') }}"
                    >
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Permisos</th>
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
    @vite('resources/js/modules/roles/index.js')
@endpush
