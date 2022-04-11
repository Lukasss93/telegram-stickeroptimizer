<?php

namespace App\ImageFilters;

use App\Enums\StickerTemplate;
use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class ScaleFilter implements FilterInterface
{
    protected StickerTemplate $template;

    public function __construct(StickerTemplate $template = StickerTemplate::STICKER)
    {
        $this->template = $template;
    }

    public static function make(StickerTemplate $template = StickerTemplate::STICKER): self
    {
        return new self($template);
    }

    /**
     * @inheritDoc
     */
    public function applyFilter(Image $image): Image
    {
        return match ($this->template) {
            StickerTemplate::STICKER => $image->resize(512, 512, fn ($rule) => $rule->aspectRatio()),
            StickerTemplate::ICON => $image->resize(100, 100),
        };
    }
}
