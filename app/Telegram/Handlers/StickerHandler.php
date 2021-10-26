<?php

namespace App\Telegram\Handlers;

use App\Enums\TelegramLimit;
use App\Jobs\OptimizeStickerJob;
use SergiX44\Nutgram\Nutgram;

class StickerHandler
{
    public function __invoke(Nutgram $bot)
    {
        $replyID = $bot->messageId();
        $fileSize = $bot->message()->sticker->file_size;
        $fileID = $bot->message()->sticker->file_id;

        if ($fileSize >= TelegramLimit::DOWNLOAD) {
            $bot->sendMessage(trans('common.too_large_file'), [
                'reply_to_message_id' => $replyID,
                'allow_sending_without_reply' => true,
            ]);

            return;
        }

        if ($bot->message()->sticker->is_animated) {
            $bot->sendMessage(trans('common.animated_not_supported'), [
                'reply_to_message_id' => $replyID,
                'allow_sending_without_reply' => true,
            ]);

            return;
        }

        OptimizeStickerJob::dispatch($bot->chatId(), $replyID, $fileID);
    }
}
