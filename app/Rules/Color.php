<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Color implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $hex = '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$';
        $rgb = '^rgb\((\s*\d+\s*,){2}\s*[\d]+\)$';
        $rgba = '^rgba\((\s*\d+\s*,){3}[\d\.]+\)$';
        $hsl = '^hsl\((\s*\d+\s*,){2}\s*[\d\.]%\)$';
        $hsla = '^hsla\((\s*\d+\s*,){3}[\d\.]+\)$';

        $patterns = "/$hex|$rgb|$rgba|$hsl|$hsla/i";
        $bool = preg_match($patterns, $value);
        if (!$bool) {
            $fail("$attribute phải là một mã màu hợp lệ.");
        }
    }

    public function passes($attribute, $value)
    {
        $hex = '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$';
        $rgb = '^rgb\((\s*\d+\s*,){2}\s*[\d]+\)$';
        $rgba = '^rgba\((\s*\d+\s*,){3}[\d\.]+\)$';
        $hsl = '^hsl\((\s*\d+\s*,){2}\s*[\d\.]%\)$';
        $hsla = '^hsla\((\s*\d+\s*,){3}[\d\.]+\)$';

        $patterns = "/$hex|$rgb|$rgba|$hsl|$hsla/i";

        return preg_match($patterns, $value);
    }

    public function message()
    {
        return ':attribute phải là một mã màu hợp lệ.';
    }
}
