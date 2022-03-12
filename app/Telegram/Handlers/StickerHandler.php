<?php

namespace App\Telegram\Handlers;

use App\Jobs\OptimizeStickerJob;
use SergiX44\Nutgram\Nutgram;

class StickerHandler
{
    public function __invoke(Nutgram $bot)
    {
        $replyID = $bot->messageId();
        $fileSize = $bot->message()->sticker->file_size;
        $fileID = $bot->message()->sticker->file_id;

        if ($bot->message()->sticker->is_animated) {
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
