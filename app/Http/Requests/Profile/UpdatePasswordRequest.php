<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\PasswordSegura;

/**
 * FORM REQUEST: UpdatePasswordRequest
 *
 * Validaciones para el cambio de contraseña del usuario autenticado.
 */
class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!\Illuminate\Support\Facades\Hash::check($value, auth()->user()->password)) {
                        $fail('La contraseña actual no coincide con nuestros registros.');
                    }
                },
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
                // No puede ser igual a la contraseña actual
                function ($attribute, $value, $fail) {
                    if (\Illuminate\Support\Facades\Hash::check($value, auth()->user()->password)) {
                        $fail('La nueva contraseña no puede ser igual a la contraseña actual.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Debes ingresar tu contraseña actual para continuar.',
            'password.required'         => 'La nueva contraseña es obligatoria.',
            'password.confirmed'        => 'Las contraseñas nuevas no coinciden. Verifícalas.',
        ];
    }
}
