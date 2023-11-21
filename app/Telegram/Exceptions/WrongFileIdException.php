<?php

namespace App\Telegram\Exceptions;

use SergiX44\Nutgram\Exception\ApiException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

class WrongFileIdException extends ApiException
{
    public static ?string $pattern = '.*wrong file_id or the file is temporarily unavailable.*';
}
