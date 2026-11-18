<x-form.input
    name="name"
    label="Nombre"
    icon="ti ti-user"
    :value="$user->name ?? null"
    required
    autocomplete="name"
/>

<x-form.input
    name="nickname"
    label="Usuario"
    icon="ti ti-at"
    :value="$user->nickname ?? null"
    required
    autocomplete="username"
    help="Se usa para entrar al panel y a la app."
/>

<x-form.input
    name="email"
    label="Correo"
    type="email"
    icon="ti ti-mail"
    :value="$user->email ?? null"
    required
    autocomplete="email"
/>

<x-form.input
    name="password"
    label="Contraseña"
    type="password"
    icon="ti ti-lock"
    :required="! isset($user)"
    autocomplete="new-password"
    :help="isset($user) ? 'Déjala vacía si no quieres cambiarla.' : null"
/>

<x-form.input
    name="password_confirmation"
    label="Confirmar contraseña"
    type="password"
    icon="ti ti-lock-check"
    :required="! isset($user)"
    autocomplete="new-password"
/>

<div class="mb-3">
    <label for="roles" class="form-label required">Roles</label>
    <div class="input-icon">
        <span class="input-icon-addon">
            <i class="ti ti-shield"></i>
        </span>
        <select
            id="roles"
            name="roles[]"
            class="form-select @error('roles') is-invalid @enderror @error('roles.*') is-invalid @enderror"
            multiple
            required
        >
            @foreach ($roles as $role)
                <option
                    value="{{ $role->name }}"
                    @selected(in_array($role->name, old('roles', $selectedRoles), true))
                >
                    {{ $role->name }}
                </option>
            @endforeach
        </select>
    </div>
    @error('roles')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @error('roles.*')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
