<?php

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;

class SuccessfulPaymentHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage(__('donate.thanks'));

        stats('donation', 'payment');
    }
}
