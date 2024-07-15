<?php

namespace App\Telegram\Handlers;

use App\Enums\TelegramLimit;
use App\Jobs\OptimizeVideoStickerJob;
use SergiX44\Nutgram\Nutgram;

class AnimationHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $replyID = $bot->messageId();
        $fileSize = $bot->message()->animation->file_size;
        $fileID = $bot->message()->animation->file_id;

        if ($fileSize >= TelegramLimit::DOWNLOAD->value) {
            $bot->sendMessage(
                text: trans('common.too_large_file'),
                reply_to_message_id: $replyID,
                allow_sending_without_reply: true,
            );

            return;
        }

        OptimizeVideoStickerJob::dispatch($bot->chatId(), $replyID, $fileID);

        stats('handler.animation');
    }
}
