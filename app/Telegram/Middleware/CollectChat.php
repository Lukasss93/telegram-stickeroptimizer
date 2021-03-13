<?php

namespace App\Telegram\Middleware;

use App\Models\Chat;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;

class CollectChat
{
    public function __invoke(Nutgram $bot, $next): void
    {
        $user = $bot->user();

        if ($user === null) {
            return;
        }

        $type = match ($bot->update()?->getType()) {
            UpdateTypes::MESSAGE => $bot->update()->message->chat->type,
            UpdateTypes::EDITED_MESSAGE => $bot->update()->edited_message->chat->type,
            UpdateTypes::INLINE_QUERY, UpdateTypes::CALLBACK_QUERY => 'private',
            default => 'unknown',
        };

        $chat = Chat::updateOrCreate([
            'chat_id' => $user->id,
        ], [
                'type' => $type,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'username' => $user->username,
                'language_code' => $user->language_code,
                'started' => true,
                'blocked' => false,
            ]
        );

        $bot->setData(Chat::class, $chat);

        $next($bot);
    }
}
