<?php

namespace App\Telegram\Commands;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardRemove;

class CancelCommand
{
    public function __invoke(Nutgram $bot): void
    {
        try {
            $bot->endConversation();

            $bot->sendMessage(
                text: 'Removing keyboard...',
                reply_markup: ReplyKeyboardRemove::make(true),
            )?->delete();
        } finally {
            stats('cancel', 'command');
        }
    }
}
