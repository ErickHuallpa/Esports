<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * REGLA: TelefonoBolivia
 *
 * Valida números de teléfono bolivianos.
 *
 * ✅ Válido:   71234567, 68123456, 2 2341234 (fijo La Paz)
 * ❌ Inválido: nnnn, abc, 1234, 123456789 (9 dígitos)
 *
 * Formatos aceptados:
 * - Celular: 8 dígitos, empieza con 6, 7 (ej: 71234567, 68123456)
 * - Fijo:    7 dígitos con código de área (ej: 2-2341234 o 22341234)
 *
 * Para simplificar a nivel empresarial: exactamente 8 dígitos numéricos.
 */
class TelefonoBolivia implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Campo opcional: si está vacío o null, no hay error
        if ($value === null || $value === '') {
            return;
        }

        // Limpiar espacios y guiones para normalizar
        $numeroLimpio = preg_replace('/[\s\-]/', '', $value);

        // Solo dígitos después de limpiar
        if (!ctype_digit($numeroLimpio)) {
            $fail('El teléfono solo debe contener números (sin letras ni símbolos).');
            return;
        }

        // Exactamente 8 dígitos (estándar Bolivia)
        if (strlen($numeroLimpio) !== 8) {
            $fail('El teléfono debe tener exactamente 8 dígitos. Ej: 71234567.');
            return;
        }

        // Prefijos válidos en Bolivia
        // Celular: 6x, 7x
        // Fijo: 2x (La Paz), 3x (Sta Cruz), 4x (Cbba), etc.
        $primerDigito = $numeroLimpio[0];
        if (!in_array($primerDigito, ['2', '3', '4', '6', '7'])) {
            $fail('El número de teléfono no corresponde a un prefijo boliviano válido (6x, 7x para celular; 2x, 3x, 4x para fijo).');
        }
    }
}
