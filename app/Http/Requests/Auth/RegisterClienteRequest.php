<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NombrePersona;
use App\Rules\PasswordSegura;
use App\Rules\CarnetIdentidadBolivia;
use App\Rules\TelefonoBolivia;

/**
 * FORM REQUEST: RegisterClienteRequest
 *
 * Centraliza y refuerza todas las validaciones del registro público de clientes.
 * Reemplaza el `$request->validate()` directo en ClienteController.
 *
 * Aplica: Validaciones de backend OBLIGATORIAS (independiente del frontend).
 */
class RegisterClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo usuarios NO autenticados pueden registrarse
        return !auth()->check();
    }

    public function rules(): array
    {
        return [
            // ── NOMBRES ──────────────────────────────────────────────────────
            // Solo letras, mínimo 2 chars, sin números ni símbolos
            'nombre' => [
                'required',
                'string',
                'min:2',
                'max:100',
                new NombrePersona('nombre'),
            ],

            // ── APELLIDOS ────────────────────────────────────────────────────
            'apellidos' => [
                'required',
                'string',
                'min:2',
                'max:100',
                new NombrePersona('apellido'),
            ],

            // ── CARNET DE IDENTIDAD ──────────────────────────────────────────
            // Formato boliviano: 5-8 dígitos + extensión opcional (LP, CB, etc.)
            'ci' => [
                'nullable',
                'string',
                'max:20',
                new CarnetIdentidadBolivia(),
                'unique:personas,ci',
            ],

            // ── TELÉFONO ─────────────────────────────────────────────────────
            // Exactamente 8 dígitos, prefijo boliviano válido
            'telefono' => [
                'nullable',
                'string',
                new TelefonoBolivia(),
            ],

            // ── DIRECCIÓN ────────────────────────────────────────────────────
            'direccion' => [
                'nullable',
                'string',
                'max:255',
            ],

            // ── USERNAME ──────────────────────────────────────────────────────
            // Solo alfanumérico + guión bajo, sin espacios
            'username' => [
                'required',
                'string',
                'min:4',
                'max:80',
                'regex:/^[a-zA-Z0-9_]+$/',
                'unique:users,username',
            ],

            // ── EMAIL ────────────────────────────────────────────────────────
            // Validación estricta: requiere dominio con TLD real (texto@dominio.com)
            'email' => [
                'required',
                'string',
                'max:150',
                'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/',
                'unique:users,email',
            ],

            // ── CONTRASEÑA ───────────────────────────────────────────────────
            // Mínimo 8 chars + mayúscula + minúscula + número + especial
            'password' => [
                'required',
                'string',
                'confirmed',    // Verifica que coincida con password_confirmation
                new PasswordSegura(),
            ],
        ];
    }

    /**
     * Mensajes de error en español, claros y orientados al usuario boliviano.
     */
    public function messages(): array
    {
        return [
            'nombre.required'      => 'El nombre es obligatorio.',
            'nombre.min'           => 'El nombre debe tener al menos 2 letras.',
            'nombre.max'           => 'El nombre no puede superar los 100 caracteres.',

            'apellidos.required'   => 'Los apellidos son obligatorios.',
            'apellidos.min'        => 'Los apellidos deben tener al menos 2 letras.',
            'apellidos.max'        => 'Los apellidos no pueden superar los 100 caracteres.',

            'ci.unique'            => 'Este número de C.I. ya está registrado en el sistema.',

            'username.required'    => 'El nombre de usuario es obligatorio.',
            'username.min'         => 'El usuario debe tener al menos 4 caracteres.',
            'username.max'         => 'El usuario no puede superar los 80 caracteres.',
            'username.regex'       => 'El usuario solo puede contener letras, números y guión bajo (_). Sin espacios.',
            'username.unique'      => 'Este nombre de usuario ya está en uso. Prueba con uno diferente.',

            'email.required'       => 'El correo electrónico es obligatorio.',
            'email.regex'          => 'El correo electrónico no tiene un formato válido. Ejemplo: nombre@dominio.com',
            'email.unique'         => 'Este correo ya está registrado. ¿Ya tienes una cuenta?',
            'email.max'            => 'El correo electrónico no puede superar los 150 caracteres.',

            'password.required'    => 'La contraseña es obligatoria.',
            'password.confirmed'   => 'Las contraseñas no coinciden. Por favor, verifícalas.',
        ];
    }

    /**
     * Sanitización antes de validar: capitaliza nombres, normaliza email.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Capitaliza primera letra de cada palabra en nombre y apellidos
            'nombre'    => $this->nombre ? mb_convert_case(mb_strtolower(trim($this->nombre)), MB_CASE_TITLE, 'UTF-8') : null,
            'apellidos' => $this->apellidos ? mb_convert_case(mb_strtolower(trim($this->apellidos)), MB_CASE_TITLE, 'UTF-8') : null,
            // Normaliza email a minúsculas
            'email'     => $this->email ? mb_strtolower(trim($this->email)) : null,
            // Normaliza CI a mayúsculas y sin espacios extremos
            'ci'        => $this->ci ? strtoupper(trim($this->ci)) : null,
            // Normaliza username a minúsculas
            'username'  => $this->username ? mb_strtolower(trim($this->username)) : null,
        ]);
    }
}
