<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterClienteRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta petición.
     */
    public function authorize()
    {
        return true; 
    }

    /**
     * Sobrescribe el comportamiento de validación fallida
     * para mantener las contraseñas en el formulario.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()
            ->back()
            ->withInput($this->all())
            ->withErrors($validator);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }

    /**
     * Sanitiza y prepara los datos ANTES de validarlos.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'nombre' => ucwords(strtolower(trim($this->nombre))),
            'apellidos' => ucwords(strtolower(trim($this->apellidos))),
            'email' => strtolower(trim($this->email)),
            'ci' => strtoupper(trim($this->ci)),
            'username' => trim($this->username),
            'telefono' => $this->telefono ? trim($this->telefono) : null,
        ]);
    }

    /**
     * Reglas de validación estrictas.
     */
    public function rules()
    {
        return [
            'username' => [
                'required',
                'string',
                'min:4',
                'max:80',
                'regex:/^[a-zA-Z0-9_]+$/', 
                'unique:users,username',   
            ],
            'nombre' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\pL\s\-]+$/u', 
            ],
            'apellidos' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\pL\s\-]+$/u', 
            ],
            'ci' => [
                'required',
                'string',
                'min:5',
                'max:20',
                'regex:/^[0-9]{5,10}([- ][A-Z0-9]{1,4})?$/', 
                'unique:personas,ci', 
            ],
            'telefono' => [
                'nullable', 
                'string',
                'size:8',
                'regex:/^[2367][0-9]{7}$/', 
                'unique:personas,telefono',
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns', 
                'max:150',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed', 
            ],
        ];
    }

    /**
     * Mensajes de error en español amigables.
     */
    public function messages()
    {
        return [
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.regex' => 'El usuario solo puede contener letras, números y guiones bajos (_).',
            'username.unique' => 'Este nombre de usuario ya está registrado, elige otro.',
            
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.regex' => 'El nombre no puede contener números ni caracteres especiales.',
            
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.regex' => 'Los apellidos no pueden contener números ni caracteres especiales.',
            
            'ci.required' => 'El Carnet de Identidad (C.I.) es obligatorio.',
            'ci.regex' => 'El formato del C.I. no es válido (Ej: 1234567 o 1234567 LP).',
            'ci.unique' => 'Este C.I. ya se encuentra registrado en el sistema.',
            
            'telefono.size' => 'El teléfono debe tener exactamente 8 dígitos.',
            'telefono.regex' => 'El teléfono debe empezar con 2, 3, 6 o 7 (formato inválido).',
            'telefono.unique' => 'Este teléfono ya ha sido registrado.',
            
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debes proporcionar un correo electrónico válido y de un dominio real.',
            'email.unique' => 'Este correo electrónico ya está en uso. Si es tuyo, inicia sesión.',
            
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden. Por favor, verifica.',
        ];
    }
}