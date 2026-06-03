<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\NombrePersona;
use App\Rules\PasswordSegura;
use App\Rules\CarnetIdentidadBolivia;
use App\Rules\TelefonoBolivia;

/**
 * FORM REQUEST: UpdateUserRequest
 *
 * Validaciones para actualización de usuarios desde el panel administrativo.
 * La contraseña es OPCIONAL al editar (solo se cambia si se proporciona).
 */
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtener el ID del usuario a actualizar desde la URL
        $userId = $this->route('id');
        $user = \App\Models\User::find($userId);
        $personaId = $user?->persona_id;

        return [
            'nombre' => [
                'required', 'string', 'min:2', 'max:100',
                new NombrePersona('nombre'),
            ],
            'apellidos' => [
                'required', 'string', 'min:2', 'max:100',
                new NombrePersona('apellido'),
            ],
            'ci' => [
                'nullable', 'string', 'max:20',
                new CarnetIdentidadBolivia(),
                // Ignorar el CI actual de esta persona al validar unicidad
                Rule::unique('personas', 'ci')->ignore($personaId),
            ],
            'telefono' => [
                'nullable', 'string',
                new TelefonoBolivia(),
            ],
            'rol_id' => [
                'required',
                'exists:roles,id',
            ],
            'email' => [
                'required',
                'string',
                'max:150',
                'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/',
                // Ignorar el email actual del usuario al validar unicidad
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'username' => [
                'required',
                'string',
                'min:4',
                'max:80',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique('users', 'username')->ignore($userId),
            ],
            // Contraseña OPCIONAL al editar: si se proporciona, debe cumplir seguridad
            'password' => [
                'nullable',
                'string',
                'min:6',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'    => 'El nombre es obligatorio.',
            'nombre.min'         => 'El nombre debe tener al menos 2 letras.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.min'      => 'Los apellidos deben tener al menos 2 letras.',
            'ci.unique'          => 'Este C.I. ya pertenece a otra persona registrada.',
            'rol_id.required'    => 'Debe seleccionar un rol operativo.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.regex'        => 'El correo no tiene formato válido. Ej: nombre@dominio.com',
            'email.unique'       => 'Este correo ya está en uso por otro usuario.',
            'username.required'  => 'El nombre de usuario es obligatorio.',
            'username.min'       => 'El usuario debe tener al menos 4 caracteres.',
            'username.regex'     => 'El usuario solo permite letras, números y guión bajo (_).',
            'username.unique'    => 'Este nombre de usuario ya está en uso.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre'    => $this->nombre    ? mb_convert_case(mb_strtolower(trim($this->nombre)), MB_CASE_TITLE, 'UTF-8') : null,
            'apellidos' => $this->apellidos ? mb_convert_case(mb_strtolower(trim($this->apellidos)), MB_CASE_TITLE, 'UTF-8') : null,
            'email'     => $this->email     ? mb_strtolower(trim($this->email)) : null,
            'ci'        => $this->ci        ? strtoupper(trim($this->ci)) : null,
            'username'  => $this->username  ? mb_strtolower(trim($this->username)) : null,
        ]);
    }
}
