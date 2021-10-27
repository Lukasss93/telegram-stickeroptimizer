<?php

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;

class PreCheckoutQueryHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->answerPreCheckoutQuery(true);

        stats('precheckout', 'payment');
    }
}
