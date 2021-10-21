<?php

namespace App\Telegram\Commands;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardRemove;
use Throwable;

class CancelCommand
{
    public function __invoke(Nutgram $bot)
    {
        try {
            $bot->endConversation();

            $message = $bot->sendMessage('Removing keyboard...', [
                'reply_markup' => ReplyKeyboardRemove::make(true),
            ]);

            $bot->deleteMessage($message->chat->id, $message->message_id);

        } catch (Throwable) {

        }
    }
}
