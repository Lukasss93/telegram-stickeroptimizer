<?php

namespace App\Telegram\Handlers;

use App\Jobs\OptimizeStickerJob;
use SergiX44\Nutgram\Nutgram;

class StickerHandler
{
    public function __invoke(Nutgram $bot)
    {
        $sticker = $bot->message()->sticker;

        $replyID = $bot->messageId();
        $fileSize = $sticker->file_size;
        $fileID = $sticker->file_id;

        if ($sticker->is_animated || $sticker->is_video) {
            $bot->sendMessage(trans('common.animated_not_supported'), [
                'reply_to_message_id' => $replyID,
                'allow_sending_without_reply' => true,
            ]);

            return;
        }

        OptimizeStickerJob::dispatchSync($bot->chatId(), $replyID, $fileID, $fileSize);

        stats('sticker', 'handler');
    }
}
