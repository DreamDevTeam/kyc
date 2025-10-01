<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Step implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    const step1 = 'step_1';
    const step2 = 'step_2';

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $requestDate = $value;

        if (isset($requestDate[0]) && $requestDate[0]->getClientOriginalName() != self::step1) {
            $fail('Please complete the 1st stage of registration.');
        }

        if (isset($requestDate[1]) && $requestDate[1]->getClientOriginalName() !== self::step2) {
            $fail('Please complete the 2nd stage of registration.');
        }

    }
}
