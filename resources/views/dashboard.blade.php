@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-heart-handshake me-2"></i>
                    Bienvenido
                </h3>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    Sesión de <strong>{{ auth()->user()->name }}</strong>.
                </p>
                <p class="text-secondary mb-0">
                    Este panel administra usuarios y roles del backend.
                    La aplicación móvil entra por <code>POST /api/auth/login</code>.
                </p>
            </div>
        </div>
    </div>
@endsection
