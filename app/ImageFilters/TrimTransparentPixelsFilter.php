<?php

namespace App\ImageFilters;

use App\Enums\WatermarkPosition;
use GDText\Box;
use GDText\Color;
use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;
use Lukasss93\ModelSettings\Contracts\SettingsManagerContract;

class TrimTransparentPixelsFilter implements FilterInterface
{
    protected SettingsManagerContract $settings;

    public function __construct(SettingsManagerContract $settings)
    {
        $this->settings = $settings;
    }

    public static function make(SettingsManagerContract $settings): self
    {
        return new self($settings);
    }

    /**
     * @inheritDoc
     */
    public function applyFilter(Image $image): Image
    {
        if (!$this->settings->get('trim')) {
            return $image;
        }

        $image->trim('transparent');

        return $image;
    }
}
