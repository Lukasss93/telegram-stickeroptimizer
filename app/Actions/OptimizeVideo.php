<?php

namespace App\Actions;

use App\Enums\TelegramLimit;
use App\Jobs\OptimizeVideoStickerJob;
use SergiX44\Nutgram\Nutgram;

class OptimizeVideo
{
    public function handle(Nutgram $bot, ?int $replyID, string $fileID, ?int $fileSize): void
    {
        if ($fileSize >= TelegramLimit::DOWNLOAD->value) {
            $bot->sendMessage(
                text: trans('common.too_large_file'),
                reply_to_message_id: $replyID,
                allow_sending_without_reply: true,
            );

            return;
        }

        $statusMessage = $bot->sendMessage(
            text: trans('optimize.running'),
            chat_id: $bot->chatId(),
            reply_to_message_id: $replyID,
            allow_sending_without_reply: true,
        );

        dispatch(new OptimizeVideoStickerJob(
            chatID: $bot->chatId(),
            replyID: $replyID,
            fileID: $fileID,
            statusMessageId: $statusMessage?->message_id
        ));
    }
}
