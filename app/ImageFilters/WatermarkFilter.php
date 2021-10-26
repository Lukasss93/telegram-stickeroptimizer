<?php

namespace App\ImageFilters;

use App\Enums\WatermarkPosition;
use GDText\Box;
use GDText\Color;
use Glorand\Model\Settings\Managers\TableSettingsManager;
use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class WatermarkFilter implements FilterInterface
{
    protected TableSettingsManager $settings;
    protected const BOX_PADDING = 10;

    public function __construct(TableSettingsManager $settings)
    {
        $this->settings = $settings;
    }

    public static function make(TableSettingsManager $settings): self
    {
        return new self($settings);
    }

    /**
     * @inheritDoc
     */
    public function applyFilter(Image $image): Image
    {
        //create a new layer
        $layer = imagecreatetruecolor($image->getWidth(), $image->getHeight());
        imagesavealpha($layer, true);
        imagefill($layer, 0, 0, imagecolorallocatealpha($layer, 0, 0, 0, 127));

        //initialize box
        $box = new Box($layer);
        $box->setBox(
            x: 0 + self::BOX_PADDING,
            y: 0 + self::BOX_PADDING,
            width: $image->getWidth() - self::BOX_PADDING - 20,
            height: $image->getHeight() - self::BOX_PADDING - 10
        );
        $box->setTextAlign(
            WatermarkPosition::getX($this->settings->get('watermark.position')),
            WatermarkPosition::getY($this->settings->get('watermark.position')),
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
