<?php

namespace App\Enums;

use App\Contracts\Enum;
use App\Exceptions\TelegramMessageNotModifiedException;
use App\Exceptions\TelegramMessageToDeleteNotFoundException;
use App\Exceptions\TelegramMessageToEditNotFoundException;
use App\Exceptions\TelegramUserBlockedException;
use App\Exceptions\TelegramUserDeactivatedException;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class ExceptionType extends Enum
{
    public const USER_BLOCKED = '.*bot was blocked by the user.*';
    public const USER_DEACTIVATED = '.*user is deactivated.*';
    public const SAME_CONTENT = '.*specified new message content and reply markup are exactly the same.*';
    public const MSG_TO_EDIT_NOT_FOUND = '.*message to edit not found.*';
    public const MSG_TO_DELETE_NOT_FOUND = '.*message to delete not found.*';

    public static function getExceptionFromType(string $type): string
    {
        return match ($type) {
            self::USER_BLOCKED => TelegramUserBlockedException::class,
            self::USER_DEACTIVATED => TelegramUserDeactivatedException::class,
            self::SAME_CONTENT => TelegramMessageNotModifiedException::class,
            self::MSG_TO_EDIT_NOT_FOUND => TelegramMessageToEditNotFoundException::class,
            self::MSG_TO_DELETE_NOT_FOUND => TelegramMessageToDeleteNotFoundException::class,
            default => throw new InvalidArgumentException('Unknown exception type')
        };
    }

    public static function getAllExceptions(): Collection
    {
        return self::all()->map(fn ($value) => self::getExceptionFromType($value));
    }
}
