<?php

namespace App\Telegram\Handlers;

use App\Models\Chat;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ChatMemberStatus;

class SuccessfulPaymentHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage(__('donate.thanks'));
    }
}
