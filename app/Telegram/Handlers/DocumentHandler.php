<?php

namespace App\Telegram\Handlers;

use App\Jobs\OptimizeStickerJob;
use SergiX44\Nutgram\Nutgram;

class DocumentHandler
{
    public function __invoke(Nutgram $bot)
    {
        $replyID = $bot->messageId();
        $fileSize = $bot->message()->document->file_size;
        $fileID = $bot->message()->document->file_id;

        OptimizeStickerJob::dispatchSync($bot->chatId(), $replyID, $fileID, $fileSize);

        stats('document', 'handler');
    }
}
