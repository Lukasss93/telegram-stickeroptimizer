<?php

namespace App\Telegram\Handlers;

use App\Enums\TelegramLimit;
use App\Jobs\OptimizeStickerJob;
use SergiX44\Nutgram\Nutgram;

class DocumentHandler
{
    public function __invoke(Nutgram $bot)
    {
        $replyID = $bot->messageId();
        $fileSize = $bot->message()->document->file_size;
        $fileID = $bot->message()->document->file_id;

        if ($fileSize >= TelegramLimit::DOWNLOAD) {
            $bot->sendMessage(trans('common.too_large_file'), [
                'reply_to_message_id' => $replyID,
                'allow_sending_without_reply' => true,
            ]);

            return;
        }

        OptimizeStickerJob::dispatchSync($bot->chatId(), $replyID, $fileID);

        stats('document', 'handler');
    }
}
