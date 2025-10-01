<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Age implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $requestDate = $value;
        $thisDate = date('Y-m-d');

        $requestDateUnix = strtotime($requestDate);
        $thisDateUnix = strtotime($thisDate);

        $result = ($thisDateUnix - $requestDateUnix) / (60*60*24*365);
        $result = floor($result);

        if($result < 18){
            $fail('The :attribute must be more 18 years old.');
        }
    }
}
