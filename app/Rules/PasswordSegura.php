<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * REGLA: PasswordSegura
 *
 * Exige contraseñas con estándar de seguridad empresarial:
 *
 * ✅ Válido:   Admin@123, Esports#2024, Mi_Clave1!
 * ❌ Inválido: admin123 (sin especial), ADMIN@123 (sin minúscula),
 *              Admin@abc (sin número), Admin@1 (muy corta)
 *
 * Criterios:
 * - Mínimo 8 caracteres
 * - Al menos 1 letra mayúscula (A-Z)
 * - Al menos 1 letra minúscula (a-z)
 * - Al menos 1 número (0-9)
 * - Al menos 1 carácter especial (@, #, $, !, %, *, ?, &, _, -, +, =, ^)
 */
class PasswordSegura implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $errores = [];

        if (mb_strlen($value) < 8) {
            $errores[] = 'al menos 8 caracteres';
        }

        if (!preg_match('/[A-Z]/', $value)) {
            $errores[] = 'al menos una letra mayúscula';
        }

        if (!preg_match('/[a-z]/', $value)) {
            $errores[] = 'al menos una letra minúscula';
        }

        if (!preg_match('/[0-9]/', $value)) {
            $errores[] = 'al menos un número';
        }

        if (!preg_match('/[\@\#\$\!\%\*\?\&\_\-\+\=\^\.\,\;\:\~\`\|\<\>\(\)\[\]\{\}\/\\\\]/', $value)) {
            $errores[] = 'al menos un carácter especial (@, #, $, !, %, *, ?, &, _, -)';
        }

        if (!empty($errores)) {
            $fail('La contraseña debe contener ' . implode(', ', $errores) . '.');
        }
    }
}
