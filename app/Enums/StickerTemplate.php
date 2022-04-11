<?php

namespace App\Enums;

use ArchTech\Enums\InvokableCases;

/**
 * @method string STICKER()
 * @method string ICON()
 */
enum StickerTemplate: string
{
    use InvokableCases;

    case STICKER = 'sticker';
    case ICON = 'icon';

    public function getLabel(): string
    {
        return match ($this) {
            self::STICKER => trans('settings.template.sticker').' (512x512)',
            self::ICON => trans('settings.template.icon').' (100x100)',
        };
    }
}
