<?php

namespace App\ImageFilters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class ScaleFilter implements FilterInterface
{
    /**
     * @inheritDoc
     */
    public function applyFilter(Image $image): Image
    {
        $image->resize(512, 512, function ($constraint) {
            $constraint->aspectRatio();
        });

        return $image;
    }

    public static function make(): self
    {
        return new self();
    }
}
