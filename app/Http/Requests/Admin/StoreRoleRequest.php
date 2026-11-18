<?php

namespace App\Http\Requests\Admin;

use App\Features\Permissions\Constants\PermissionTypes;
use App\Features\Permissions\PermissionHandler;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(PermissionTypes::ROLES_CREATE) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->where('guard_name', 'web')],
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
