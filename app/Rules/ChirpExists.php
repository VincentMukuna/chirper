<?php

namespace App\Rules;

use App\Models\Chirp;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ChirpExists implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(Chirp::find($value)===null){
            $fail('The original chirp does not exist. It may have been deleted');
        }
    }

}
