<?php

namespace App\Rules;

use App\Facades\ImageUtils;
use Illuminate\Contracts\Validation\InvokableRule;

class HexColorRule implements InvokableRule
{
    public function __invoke($attribute, $value, $fail)
    {
        if (!ImageUtils::isHexColor($value)) {
            $fail('The :attribute must be a valid hex color.');
        }
    }
}
