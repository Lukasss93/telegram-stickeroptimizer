<?php

namespace App\Contracts;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Collection;
use ReflectionClass;

abstract class Enum
{
    public static function all(): Collection
    {
        $class = new ReflectionClass(static::class);

        return collect($class->getConstants());
    }

    public static function rule(): Rule
    {
        return new class (self::all()) implements Rule {

            private Collection $allowed;

            public function __construct(Collection $allowed)
            {
                $this->allowed = $allowed;
            }

            public function passes($attribute, $value): bool
            {
                return $this->allowed->contains($value);
            }

            public function message(): string
            {
                return trans('common.invalid_value');
            }
        };
    }
}
