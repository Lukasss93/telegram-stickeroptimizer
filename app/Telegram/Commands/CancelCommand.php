<?php

namespace App\Telegram\Commands;

use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardRemove;

class CancelCommand extends Command
{
    protected string $command = 'cancel';

    protected ?string $description = 'Cancel current action';

    public function handle(Nutgram $bot): void
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
