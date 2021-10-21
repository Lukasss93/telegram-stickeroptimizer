<?php

namespace App\Enums;

use App\Contracts\Enum;
use InvalidArgumentException;

class WatermarkPosition extends Enum
{
    public const TOP_LEFT = 'top-left';
    public const TOP_CENTER = 'top-center';
    public const TOP_RIGHT = 'top-right';

    public const MIDDLE_LEFT = 'middle-left';
    public const MIDDLE_CENTER = 'middle-center';
    public const MIDDLE_RIGHT = 'middle-right';

    public const BOTTOM_LEFT = 'bottom-left';
    public const BOTTOM_CENTER = 'bottom-center';
    public const BOTTOM_RIGHT = 'bottom-right';

    public static function getEmojiFromValue(string $value): string
    {
        return match ($value) {
            self::TOP_LEFT => '↖',
            self::TOP_CENTER => '⬆',
            self::TOP_RIGHT => '↗',
            self::MIDDLE_LEFT => '⬅',
            self::MIDDLE_CENTER => '⏺',
            self::MIDDLE_RIGHT => '➡',
            self::BOTTOM_LEFT => '↙',
            self::BOTTOM_CENTER => '⬇',
            self::BOTTOM_RIGHT => '↘',
            default => throw new InvalidArgumentException('Invalid value'),
        };
    }

    public static function getValueFromEmoji(string $emoji): string
    {
        return match ($emoji) {
            '↖' => self::TOP_LEFT,
            '⬆' => self::TOP_CENTER,
            '↗' => self::TOP_RIGHT,
            '⬅' => self::MIDDLE_LEFT,
            '⏺' => self::MIDDLE_CENTER,
            '➡' => self::MIDDLE_RIGHT,
            '↙' => self::BOTTOM_LEFT,
            '⬇' => self::BOTTOM_CENTER,
            '↘' => self::BOTTOM_RIGHT,
            default => throw new InvalidArgumentException('Invalid value'),
        };
    }
}
