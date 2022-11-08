<?php

namespace App\ImageFilters;

use App\Enums\WatermarkPosition;
use GDText\Box;
use GDText\Color;
use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;
use Lukasss93\ModelSettings\Contracts\SettingsManagerContract;

class WatermarkFilter implements FilterInterface
{
    protected SettingsManagerContract $settings;
    protected const BOX_PADDING = 10;

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
        //skip if watermark opacity is 0
        if ($this->settings->get('watermark.opacity') === 0) {
            return $image;
        }

        //create a new layer
        $layer = imagecreatetruecolor($image->getWidth(), $image->getHeight());
        imagesavealpha($layer, true);
        imagefill($layer, 0, 0, imagecolorallocatealpha($layer, 0, 0, 0, 127));

        //initialize box
        $box = new Box($layer);
        $box->setBox(
            x: self::BOX_PADDING,
            y: self::BOX_PADDING,
            width: $image->getWidth() - self::BOX_PADDING - 20,
            height: $image->getHeight() - self::BOX_PADDING - 10
        );
        $box->setTextAlign(
            WatermarkPosition::tryFrom($this->settings->get('watermark.position'))?->getX(),
            WatermarkPosition::tryFrom($this->settings->get('watermark.position'))?->getY(),
        );

        //set text style
        $box->setFontSize($this->settings->get('watermark.text.size'));
        $box->setFontColor(Color::parseString($this->settings->get('watermark.text.color')));

        //set border style
        $box->setStrokeSize($this->settings->get('watermark.border.size'));
        $box->setStrokeColor(Color::parseString($this->settings->get('watermark.border.color')));

        //draw watermark
        $box->setFontFace(resource_path('fonts/OpenSansEmoji.ttf'));
        $box->draw($this->settings->get('watermark.text.content'));

        //set opacity
        imagealphablending($layer, false);
        imagefilter($layer, IMG_FILTER_COLORIZE, 0, 0, 0,
            127 - $this->mapRange($this->settings->get('watermark.opacity'), 0, 100, 0, 127)
        );

        //apply watermark to image
        $image->insert($layer);

        return $image;
    }

    /**
     * Map a range 1 to range 2
     * @see https://math.stackexchange.com/questions/914823/shift-numbers-into-a-different-range/914843
     * @param int $t
     * @param int $a
     * @param int $b
     * @param int $c
     * @param int $d
     * @return int
     */
    public function mapRange(int $t, int $a, int $b, int $c, int $d): int
    {
        return $c + (($d - $c) / ($b - $a)) * ($t - $a);
    }
}
