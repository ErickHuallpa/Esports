<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * REGLA: NombrePersona
 *
 * Valida nombres y apellidos con estándares bolivianos.
 *
 * ✅ Válido:  Juan, María, De La Cruz, Pérez López
 * ❌ Inválido: mm, kmm, Juan123, J@rge, 123abc
 *
 * Criterios:
 * - Solo letras (incluyendo acentos, ñ, ü)
 * - Puede contener espacios y guiones (nombres compuestos)
 * - Mínimo 2 caracteres
 * - No puede ser solo espacios
 */
class NombrePersona implements ValidationRule
{
    protected string $campo;

    public function __construct(string $campo = 'campo')
    {
        $this->campo = $campo;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = trim($value);

        // 1. Mínimo 2 caracteres (sin contar espacios)
        if (mb_strlen(str_replace(' ', '', $value)) < 2) {
            $fail("El {$this->campo} debe tener al menos 2 letras.");
            return;
        }

        // 2. Solo letras (con soporte UTF-8: tildes, ñ, ü, etc.), espacios y guiones
        // No permite: números, símbolos especiales (@, #, $, etc.), puntos, comas
        if (!preg_match('/^[\p{L}\s\-]+$/u', $value)) {
            $fail("El {$this->campo} solo puede contener letras, espacios y guiones. No se permiten números ni caracteres especiales.");
            return;
        }

        // 3. No puede empezar con espacio o guión
        if (preg_match('/^[\s\-]/', $value)) {
            $fail("El {$this->campo} no puede comenzar con espacio o guión.");
            return;
        }

        // 4. No puede contener caracteres repetidos sospechosos (solo letras repetidas, ej: "mmm", "kkk")
        // Detecta 3 o más letras iguales consecutivas (patrón de datos basura)
        if (preg_match('/(.)\1{2,}/u', $value)) {
            $fail("El {$this->campo} contiene caracteres repetidos inválidos.");
            return;
        }
    }
}
