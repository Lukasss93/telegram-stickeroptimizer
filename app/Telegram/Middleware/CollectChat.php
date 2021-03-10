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
        $type = match ($bot->update()?->getType()) {
            UpdateTypes::MESSAGE => $bot->update()->message->chat->type,
            UpdateTypes::INLINE_QUERY, UpdateTypes::CALLBACK_QUERY => 'private',
            default => null,
        };

        if ($user !== null && $type !== null) {
            Chat::updateOrCreate([
                'chat_id' => $user->id,
            ], [
                    'type' => $type,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'username' => $user->username,
                    'language_code' => $user->language_code,
                    'status' => true,
                    'blocked' => false,
                ]
            );
        }

        $next($bot);
    }
}
