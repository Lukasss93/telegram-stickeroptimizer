<?php

namespace App\Telegram\Exceptions;

use SergiX44\Nutgram\Exception\ApiException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

class UserDeactivatedException extends ApiException
{
    public static ?string $pattern = '.*user is deactivated.*';
}
