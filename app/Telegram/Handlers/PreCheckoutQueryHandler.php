<?php

namespace App\Telegram\Handlers;

use App\Models\Chat;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ChatMemberStatus;

class PreCheckoutQueryHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->answerPreCheckoutQuery(true);
    }
}
