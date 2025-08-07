<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidRutChileno implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->isValidRut($value)) {
            $fail('El :attribute no es un RUT chileno válido.');
        }
    }

    /**
     * Validate Chilean RUT
     */
    private function isValidRut(string $rut): bool
    {
        // Remove dots and hyphens
        $rut = str_replace(['.', '-', ' '], '', strtoupper($rut));
        
        // Check if it's empty or too short
        if (strlen($rut) < 2) {
            return false;
        }
        
        // Split number and verification digit
        $rutBody = substr($rut, 0, -1);
        $dv = substr($rut, -1);
        
        // Check if body is numeric
        if (!is_numeric($rutBody)) {
            return false;
        }
        
        // Calculate verification digit
        $sum = 0;
        $multiplier = 2;
        
        for ($i = strlen($rutBody) - 1; $i >= 0; $i--) {
            $sum += intval($rutBody[$i]) * $multiplier;
            $multiplier = $multiplier == 7 ? 2 : $multiplier + 1;
        }
        
        $remainder = $sum % 11;
        $calculatedDv = $remainder == 0 ? '0' : ($remainder == 1 ? 'K' : strval(11 - $remainder));
        
        return $dv === $calculatedDv;
    }
}
