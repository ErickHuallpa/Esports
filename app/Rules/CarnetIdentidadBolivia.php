<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * REGLA: CarnetIdentidadBolivia
 *
 * Valida el Carnet de Identidad boliviano con extensión departamental.
 *
 * ✅ Válido:   1234567, 12345678, 1234567 LP, 8765432 CB, 1234567-1G
 * ❌ Inválido: mm, abc, 123, @#$%
 *
 * Formatos aceptados (Bolivia):
 * - Solo números: 7-8 dígitos  (ej: 1234567, 12345678)
 * - Con extensión: número + espacio + sigla (ej: 1234567 LP)
 * - Con complemento: número + guion + alfanumérico (ej: 1234567-1G)
 */
class CarnetIdentidadBolivia implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = trim(strtoupper($value));

        // Formato válido:
        // ^[0-9]{5,8}               -> 5 a 8 dígitos (CI boliviano)
        // ([- ]                     -> separador: espacio o guión
        //  [A-Z0-9]{1,4})?$         -> extensión: sigla departamental o complemento (opcional)
        //
        // Ejemplos: 1234567, 1234567 LP, 8765432 CB, 1234567-1G, 9876543 OR
        if (!preg_match('/^[0-9]{5,8}([- ][A-Z0-9]{1,4})?$/', $value)) {
            $fail('El C.I. no tiene un formato válido. Ejemplos: 1234567, 1234567 LP, 8765432-1G.');
        }
    }
}
