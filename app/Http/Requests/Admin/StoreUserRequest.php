<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\NombrePersona;
use App\Rules\PasswordSegura;
use App\Rules\CarnetIdentidadBolivia;
use App\Rules\TelefonoBolivia;

/**
 * FORM REQUEST: StoreUserRequest
 *
 * Validaciones para creación de usuarios desde el panel administrativo.
 * Más estricto que el registro de clientes: contraseñas más fuertes obligatorias.
 */
class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización por rol se maneja vía middleware
    }

    public function rules(): array
    {
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
                'unique:personas,ci',
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
                'unique:users,email',
            ],
            'username' => [
                'required',
                'string',
                'min:4',
                'max:80',
                'regex:/^[a-zA-Z0-9_]+$/',
                'unique:users,username',
            ],
            // Contraseña OBLIGATORIA al crear, con seguridad completa
            'password' => [
                'required',
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
            'ci.unique'          => 'Este C.I. ya está registrado en el sistema.',
            'rol_id.required'    => 'Debe seleccionar un rol operativo.',
            'rol_id.exists'      => 'El rol seleccionado no es válido.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.regex'        => 'El correo no tiene formato válido. Ej: nombre@dominio.com',
            'email.unique'       => 'Este correo ya está registrado en el sistema.',
            'username.required'  => 'El nombre de usuario es obligatorio.',
            'username.min'       => 'El usuario debe tener al menos 4 caracteres.',
            'username.regex'     => 'El usuario solo permite letras, números y guión bajo (_).',
            'username.unique'    => 'Este nombre de usuario ya está en uso.',
            'password.required'  => 'La contraseña temporal es obligatoria.',
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
