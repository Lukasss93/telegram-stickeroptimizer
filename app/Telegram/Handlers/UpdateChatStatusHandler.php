<?php

namespace App\Telegram\Handlers;

use App\Models\Chat;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ChatMemberType;

class UpdateChatStatusHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $chat = $bot->getData(Chat::class);
        $chatMember = $bot->chatMember();

        if ($chat !== null && $chatMember !== null) {
            $chat->blocked_at = $chatMember->new_chat_member?->getType() === ChatMemberType::BANNED ? now() : null;
            $chat->save();

            stats($chat->blocked_at === null ? 'chat.unblocked' : 'chat.blocked', 'chat status');
        }
    }
}
