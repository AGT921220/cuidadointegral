<?php

namespace App\Http\Requests\Admin;

use App\Features\Permissions\Constants\PermissionTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(PermissionTypes::USERS_CREATE) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('users', 'nickname')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', Rule::exists('roles', 'name')->where('guard_name', 'web')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'nickname' => 'usuario',
            'email' => 'correo',
            'password' => 'contraseña',
            'roles' => 'roles',
        ];
    }
}
