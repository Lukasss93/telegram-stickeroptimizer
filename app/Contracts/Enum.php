<?php

namespace App\Contracts;

use Illuminate\Support\Collection;
use ReflectionClass;

abstract class Enum
{
    public static function all(): Collection
    {
        $class = new ReflectionClass(static::class);

        return collect($class->getConstants());
    }
}
