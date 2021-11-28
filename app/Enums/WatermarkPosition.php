<?php

namespace App\Enums;

use InvalidArgumentException;

enum WatermarkPosition: string
{
    case TOP_LEFT = 'top-left';
    case TOP_CENTER = 'top-center';
    case TOP_RIGHT = 'top-right';

    case MIDDLE_LEFT = 'middle-left';
    case MIDDLE_CENTER = 'middle-center';
    case MIDDLE_RIGHT = 'middle-right';

    case BOTTOM_LEFT = 'bottom-left';
    case BOTTOM_CENTER = 'bottom-center';
    case BOTTOM_RIGHT = 'bottom-right';

    public function emoji(): string
    {
        return match ($this) {
            self::TOP_LEFT => '↖',
            self::TOP_CENTER => '⬆',
            self::TOP_RIGHT => '↗',
            self::MIDDLE_LEFT => '⬅',
            self::MIDDLE_CENTER => '⏺',
            self::MIDDLE_RIGHT => '➡',
            self::BOTTOM_LEFT => '↙',
            self::BOTTOM_CENTER => '⬇',
            self::BOTTOM_RIGHT => '↘',
        };
    }

    public static function getValueFromEmoji(?string $emoji): WatermarkPosition
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

    public function getX(): string
    {
        return match ($this) {
            self::TOP_LEFT, self::MIDDLE_LEFT, self::BOTTOM_LEFT => 'left',
            self::TOP_CENTER, self::MIDDLE_CENTER, self::BOTTOM_CENTER => 'center',
            self::TOP_RIGHT, self::MIDDLE_RIGHT, self::BOTTOM_RIGHT => 'right',
        };
    }

    public function getY(): string
    {
        return match ($this) {
            self::TOP_LEFT, self::TOP_CENTER, self::TOP_RIGHT => 'top',
            self::MIDDLE_LEFT, self::MIDDLE_CENTER, self::MIDDLE_RIGHT => 'center',
            self::BOTTOM_LEFT, self::BOTTOM_CENTER, self::BOTTOM_RIGHT => 'bottom',
        };
    }
}
