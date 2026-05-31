<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NombrePersona;
use App\Rules\PasswordSegura;
use App\Rules\CarnetIdentidadBolivia;
use App\Rules\TelefonoBolivia;

/**
 * FORM REQUEST: RegisterAdminRequest
 *
 * Validaciones para la configuración inicial del administrador raíz.
 * Más exigente que el registro de clientes (CI obligatorio, contraseña fuerte).
 */
class RegisterAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo si no existe admin aún
        return !\App\Models\User::whereHas('rol', fn($q) => $q->where('nombre', 'admin'))->exists();
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
                'required',
                'string',
                'max:20',
                new CarnetIdentidadBolivia(),
                'unique:personas,ci',
            ],
            'telefono' => [
                'nullable', 'string',
                new TelefonoBolivia(),
            ],
            'direccion' => [
                'nullable', 'string', 'max:255',
            ],
            'fecha_nacimiento' => [
                'nullable',
                'date',
                'before:' . now()->subYears(18)->format('Y-m-d'), // Admin debe ser mayor de edad
                'after:' . now()->subYears(100)->format('Y-m-d'),
            ],
            'username' => [
                'required', 'string', 'min:4', 'max:80',
                'regex:/^[a-zA-Z0-9_]+$/',
                'unique:users,username',
            ],
            'email' => [
                'required', 'string', 'max:150',
                'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/',
                'unique:users,email',
            ],
            'password' => [
                'required', 'string', 'confirmed',
                new PasswordSegura(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'              => 'El nombre del administrador es obligatorio.',
            'nombre.min'                   => 'El nombre debe tener al menos 2 letras.',
            'apellidos.required'           => 'Los apellidos son obligatorios.',
            'apellidos.min'                => 'Los apellidos deben tener al menos 2 letras.',
            'ci.required'                  => 'El C.I. es obligatorio para el administrador.',
            'ci.unique'                    => 'Este C.I. ya está registrado.',
            'fecha_nacimiento.before'      => 'El administrador debe ser mayor de 18 años.',
            'fecha_nacimiento.after'       => 'La fecha de nacimiento no es válida.',
            'username.required'            => 'El nombre de usuario es obligatorio.',
            'username.min'                 => 'El usuario debe tener al menos 4 caracteres.',
            'username.regex'               => 'El usuario solo permite letras, números y guión bajo (_).',
            'username.unique'              => 'Este nombre de usuario ya está en uso.',
            'email.required'               => 'El correo electrónico corporativo es obligatorio.',
            'email.regex'                  => 'El correo no tiene un formato válido. Ej: admin@esports.com',
            'email.unique'                 => 'Este correo ya está registrado.',
            'password.required'            => 'La contraseña maestra es obligatoria.',
            'password.confirmed'           => 'Las contraseñas no coinciden.',
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
