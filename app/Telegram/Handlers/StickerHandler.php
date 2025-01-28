<?php

namespace App\Telegram\Handlers;

use App\Actions\OptimizeVideo;
use App\Jobs\OptimizeStickerJob;
use SergiX44\Nutgram\Nutgram;

class StickerHandler
{
    public function __construct(
        protected OptimizeVideo $optimizeVideoAction,
    ) {
    }

    public function __invoke(Nutgram $bot): void
    {
        $sticker = $bot->message()->sticker;

        $replyID = $bot->messageId();
        $fileSize = $sticker->file_size;
        $fileID = $sticker->file_id;

        if ($sticker->is_animated) {
            $bot->sendMessage(
                text: trans('common.animated_not_supported'),
                reply_to_message_id: $replyID,
                allow_sending_without_reply: true,
            );

            return;
        }

        if ($sticker->is_video) {
            $this->optimizeVideoAction->handle($bot, $replyID, $fileID, $fileSize);

            return;
        }

        OptimizeStickerJob::dispatchSync($bot->chatId(), $replyID, $fileID, $fileSize);

        stats('handler.sticker');
    }
}
