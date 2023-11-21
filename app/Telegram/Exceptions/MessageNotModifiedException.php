<?php

namespace App\Telegram\Exceptions;

use SergiX44\Nutgram\Exception\ApiException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

class MessageNotModifiedException extends ApiException
{
    public static ?string $pattern = '.*specified new message content and reply markup are exactly the same.*';
}
