<?php

namespace App\Telegram\Handlers;

use App\Models\Chat;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Chat\ChatMemberBanned;

class UpdateChatStatusHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $chat = $bot->get(Chat::class);
        $chatMember = $bot->chatMember();

        if ($chat !== null && $chatMember !== null) {
            $chat->blocked_at = $chatMember->new_chat_member instanceof ChatMemberBanned ? now() : null;
            $chat->save();

            stats($chat->blocked_at === null ? 'user.status.unblocked' : 'user.status.blocked');
        }
    }
}
