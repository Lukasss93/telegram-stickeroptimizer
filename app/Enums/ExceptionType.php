<?php

namespace App\Enums;

use App\Exceptions\TelegramMessageNotModifiedException;
use App\Exceptions\TelegramMessageToDeleteNotFoundException;
use App\Exceptions\TelegramMessageToEditNotFoundException;
use App\Exceptions\TelegramUserBlockedException;
use App\Exceptions\TelegramUserDeactivatedException;
use App\Exceptions\TelegramWrongFileIdException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use SergiX44\Nutgram\Nutgram;

enum ExceptionType: string
{
    case USER_BLOCKED = '.*bot was blocked by the user.*';
    case USER_DEACTIVATED = '.*user is deactivated.*';
    case SAME_CONTENT = '.*specified new message content and reply markup are exactly the same.*';
    case MSG_TO_EDIT_NOT_FOUND = '.*message to edit not found.*';
    case MSG_TO_DELETE_NOT_FOUND = '.*message to delete not found.*';
    case WRONG_FILE_ID = '.*wrong file_id or the file is temporarily unavailable.*';

    public static function getExceptionFromType(?ExceptionType $type): string
    {
        return match ($type) {
            self::USER_BLOCKED => TelegramUserBlockedException::class,
            self::USER_DEACTIVATED => TelegramUserDeactivatedException::class,
            self::SAME_CONTENT => TelegramMessageNotModifiedException::class,
            self::MSG_TO_EDIT_NOT_FOUND => TelegramMessageToEditNotFoundException::class,
            self::MSG_TO_DELETE_NOT_FOUND => TelegramMessageToDeleteNotFoundException::class,
            self::WRONG_FILE_ID => TelegramWrongFileIdException::class,
            default => throw new InvalidArgumentException('Unknown exception type')
        };
    }

    public static function getAllExceptions(): Collection
    {
        return collect(self::cases())->map(fn ($value) => self::getExceptionFromType($value));
    }

    public function toNutgramException(): array
    {
        $exception = self::getExceptionFromType($this);

        return [$this->value, fn (Nutgram $bot, $e) => throw new $exception($e->getMessage())];
    }
}
