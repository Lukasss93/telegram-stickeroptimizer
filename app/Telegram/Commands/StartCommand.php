<?php

namespace App\Telegram\Commands;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

class StartCommand
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage(message('start'), [
            'parse_mode' => ParseMode::HTML,
        ]);

        stats('start', 'command');
    }
}
