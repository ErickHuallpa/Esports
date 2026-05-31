<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\NombrePersona;
use App\Rules\PasswordSegura;
use App\Rules\CarnetIdentidadBolivia;
use App\Rules\TelefonoBolivia;

/**
 * FORM REQUEST: UpdateProfileRequest
 *
 * Validaciones para actualización del perfil del usuario autenticado.
 */
class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $user = auth()->user();
        $persona = $user->persona;

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
                'required', 'string', 'max:20',
                new CarnetIdentidadBolivia(),
                Rule::unique('personas', 'ci')->ignore($persona->id),
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
                'before:' . now()->subYears(13)->format('Y-m-d'), // Mínimo 13 años
                'after:' . now()->subYears(120)->format('Y-m-d'),
            ],
            'username' => [
                'required', 'string', 'min:4', 'max:80',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'required', 'string', 'max:150',
                'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'foto_perfil' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048', // 2MB máximo
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'        => 'El nombre es obligatorio.',
            'nombre.min'             => 'El nombre debe tener al menos 2 letras.',
            'apellidos.required'     => 'Los apellidos son obligatorios.',
            'apellidos.min'          => 'Los apellidos deben tener al menos 2 letras.',
            'ci.required'            => 'El C.I. es obligatorio.',
            'ci.unique'              => 'Este C.I. ya está registrado por otro usuario.',
            'fecha_nacimiento.before'=> 'Debes tener al menos 13 años para tener una cuenta.',
            'fecha_nacimiento.after' => 'La fecha de nacimiento ingresada no es válida.',
            'username.required'      => 'El nombre de usuario es obligatorio.',
            'username.min'           => 'El usuario debe tener al menos 4 caracteres.',
            'username.regex'         => 'El usuario solo permite letras, números y guión bajo. Sin espacios.',
            'username.unique'        => 'Este nombre de usuario ya está en uso por otra cuenta.',
            'email.required'         => 'El correo electrónico es obligatorio.',
            'email.regex'            => 'El correo no tiene un formato válido. Ej: nombre@dominio.com',
            'email.unique'           => 'Este correo ya está registrado en otra cuenta.',
            'foto_perfil.image'      => 'El archivo debe ser una imagen.',
            'foto_perfil.mimes'      => 'La foto debe ser JPG, PNG o WebP.',
            'foto_perfil.max'        => 'La foto no puede superar 2MB.',
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
