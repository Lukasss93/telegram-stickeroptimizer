<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \App\Support\ImageUtils
 */
class ImageUtils extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ImageUtils';
    }
}
