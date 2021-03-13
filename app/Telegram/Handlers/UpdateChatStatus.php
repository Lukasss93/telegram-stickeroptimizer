<?php

namespace App\Telegram\Handlers;

use App\Models\Chat;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ChatMemberStatus;

class UpdateChatStatus
{
    public function __invoke(Nutgram $bot): void
    {
        $chat = $bot->getData(Chat::class);
        $chatMember = $bot->chatMember();

        if ($chat !== null && $chatMember !== null) {
            $chat->blocked = $chatMember->new_chat_member->status === ChatMemberStatus::KICKED;
            $chat->save();
        }
    }
}
