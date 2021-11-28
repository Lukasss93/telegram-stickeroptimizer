<?php

namespace App\Telegram\Handlers;

use App\Enums\TelegramLimit;
use App\Jobs\OptimizeStickerJob;
use SergiX44\Nutgram\Nutgram;

class PhotoHandler
{
    public function __invoke(Nutgram $bot)
    {
        $replyID = $bot->messageId();
        $file = end($bot->message()->photo);
        $fileSize = $file->file_size;
        $fileID = $file->file_id;

        if ($fileSize >= TelegramLimit::DOWNLOAD->value) {
            $bot->sendMessage(trans('common.too_large_file'), [
                'reply_to_message_id' => $replyID,
                'allow_sending_without_reply' => true,
            ]);

            return;
        }

        OptimizeStickerJob::dispatchSync($bot->chatId(), $replyID, $fileID);

        stats('photo', 'handler');
    }
}
