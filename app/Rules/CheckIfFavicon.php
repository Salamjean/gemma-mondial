<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckIfFavicon implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value && method_exists($value, 'getClientOriginalExtension')) {
            $ext = strtolower($value->getClientOriginalExtension());
            if (!in_array($ext, ['ico', 'png', 'jpg', 'jpeg', 'svg'])) {
                $fail("Le favicon doit être un fichier image de type : .ico, .png, .jpg, .jpeg, .svg.");
            }
        }
    }
}
