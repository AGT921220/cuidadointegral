<?php

namespace App\Http\Requests\Admin;

use App\Features\Permissions\Constants\PermissionTypes;
use App\Features\Permissions\PermissionHandler;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

final class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(PermissionTypes::ROLES_UPDATE) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Role $role */
        $role = $this->route('role');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->where('guard_name', 'web')->ignore($role->id),
            ],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::in(app(PermissionHandler::class)->getAllPermissions())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'permissions' => 'permisos',
        ];
    }
}
