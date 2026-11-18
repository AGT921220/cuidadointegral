<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar sesión | {{ config('business.name') }}</title>
    @vite([
        'resources/css/app.css',
        'resources/css/pages/login.css',
        'resources/js/app.js',
    ])
</head>
<body class="login-page">
    <main class="login-shell">
        <div class="login-panel">
            <div class="login-brand">
                <i class="ti ti-heart-handshake login-brand__mark" aria-hidden="true"></i>
                <h1 class="login-brand__title">{{ config('business.name') }}</h1>
                <p class="login-brand__subtitle">Administración del backend</p>
            </div>

            <div class="login-card">
                <h2 class="login-card__heading">
                    <i class="ti ti-login" aria-hidden="true"></i>
                    Iniciar sesión
                </h2>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <x-form.input
                        name="nickname"
                        label="Usuario"
                        type="text"
                        icon="ti ti-at"
                        :value="old('nickname')"
                        required
                        autofocus
                        autocomplete="username"
                    />

                    <x-form.input
                        name="password"
                        label="Contraseña"
                        type="password"
                        icon="ti ti-lock"
                        required
                        autocomplete="current-password"
                    />

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Recordarme</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-login me-1"></i>
                        Entrar
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
