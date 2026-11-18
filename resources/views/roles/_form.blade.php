<x-form.input
    name="name"
    label="Nombre"
    icon="ti ti-shield"
    :value="$role->name ?? null"
    required
/>

<div class="mb-3">
    <label class="form-label">Permisos</label>
    @foreach ($permissionGroups as $module => $permissions)
        <div class="mb-3">
            <div class="form-label">{{ $module }}</div>
            @foreach ($permissions as $permission => $label)
                <label class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="permissions[]"
                        value="{{ $permission }}"
                        @checked(in_array($permission, old('permissions', $selectedPermissions), true))
                    >
                    <span class="form-check-label">{{ $label }}</span>
                </label>
            @endforeach
        </div>
    @endforeach
    @error('permissions')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @error('permissions.*')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
